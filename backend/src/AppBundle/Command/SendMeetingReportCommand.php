<?php

namespace AppBundle\Command;

use AppBundle\Entity\Meeting;
use AppBundle\Entity\MeetingParticipant;
use AppBundle\Entity\MeetingReport;
use AppBundle\Entity\User;
use AppBundle\Services\MailerService;
use Component\PDF\Exception\PDFException;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

class SendMeetingReportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:meeting:send-report')
            ->addArgument('meetingId', InputArgument::REQUIRED, 'Meeting ID', null)
            ->addArgument('userId', InputArgument::REQUIRED, 'User ID', null)
            ->addArgument('meetingReportId', InputArgument::REQUIRED, 'Report ID', null)
            ->addArgument('host', InputArgument::REQUIRED, 'Hostname', null)
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $meetingId = (int) $input->getArgument('meetingId');
        $userId = (int) $input->getArgument('userId');
        $host = $input->getArgument('host');
        $meetingReportId = (int) $input->getArgument('meetingReportId');

        $meeting = $this->findMeeting($meetingId);
        $user = $this->findUser($userId);
        $report = $this->findReport($meetingReportId);

        $io = new SymfonyStyle($input, $output);

        $recipients = $this->getMailRecipientsGroupedByLocale($meeting);
        if (empty($recipients)) {
            $io->warning('No email recipients found.');

            return 0;
        }

        $mailer = $this->getMailer();

        $io->note('Creating meeting PDF...');
        $attachment = $this->createMailPDFAttachment($meeting, $user, $host);
        $io->success('Meeting PDF successfully created');

        $io->note(
            sprintf(
                'Emailing to: %s',
                implode(
                    ', ',
                    array_map(
                        function ($r) {
                            return implode(',', $r);
                        },
                        $recipients
                    )
                )
            )
        );

        $mailer->addFromParameter(
            $user->getEmail(),
            [
                'name' => $user->getFullName(),
                'email' => $user->getEmail(),
            ]
        );

        $trans = $this->getContainer()->get('translator');
        $currentLocale = $trans->getLocale();

        foreach ($recipients as $locale => $to) {
            $trans->setLocale($locale);

            $mailer->sendEmail(
                ':meeting:report.html.twig',
                'notification',
                $to,
                ['meeting' => $meeting, 'report' => $report],
                [$attachment],
                [],
                [
                    $user->getEmail() => $user->getFullName(),
                ]
            );
        }

        $trans->setLocale($currentLocale);

        $io->success('Email successfully sent');

        return 0;
    }

    /**
     * @param Meeting $meeting
     *
     * @return array
     */
    private function getMailRecipientsGroupedByLocale(Meeting $meeting): array
    {
        $recipients = [];
        $this
            ->getMeetingParticipants($meeting)
            ->map(
                function (MeetingParticipant $meetingParticipant) use (&$recipients) {
                    $user = $meetingParticipant->getUser();
                    $locale = $user->getLocale();
                    if (empty($recipients[$locale])) {
                        $recipients[$locale] = [];
                    }
                    $recipients[$locale][] = $user->getEmail();
                }
            )
        ;

        return $recipients;
    }

    /**
     * @param int $id
     *
     * @return Meeting
     */
    private function findMeeting(int $id): Meeting
    {
        $meeting = $this
            ->getContainer()
            ->get('app.repository.meeting')
            ->find($id)
        ;
        Assert::notNull($meeting, sprintf('Meeting with ID "%d" not found', $id));

        return $meeting;
    }

    /**
     * @param int $id
     *
     * @return User
     */
    private function findUser(int $id): User
    {
        $user = $this
            ->getContainer()
            ->get('app.repository.user')
            ->find($id)
        ;
        Assert::notNull($user, sprintf('User with ID "%d" not found', $id));

        return $user;
    }

    /**
     * @param int $id
     *
     * @return MeetingReport
     */
    private function findReport(int $id): MeetingReport
    {
        $report = $this
            ->getContainer()
            ->get('app.repository.meeting_report')
            ->find($id)
        ;
        Assert::notNull($report, sprintf('Report with ID "%d" not found', $id));

        return $report;
    }

    /**
     * @param Meeting $meeting
     * @param User    $user
     * @param string  $host
     *
     * @throws PDFException
     *
     * @return \Swift_Attachment
     */
    private function createMailPDFAttachment(Meeting $meeting, User $user, string $host): \Swift_Attachment
    {
        $pdf = $this->getPDFContent($meeting, $user, $host);

        return new \Swift_Attachment(
            $pdf,
            sprintf('status-report-%s.pdf', $meeting->getCreatedAt()->format('Y-m-d')),
            'application/pdf'
        );
    }

    /**
     * @param Meeting $meeting
     * @param User    $user
     * @param string  $host
     *
     * @throws PDFException
     *
     * @return string
     */
    private function getPDFContent(Meeting $meeting, User $user, string $host): string
    {
        return $this
            ->getContainer()
            ->get('app.pdf.builder.meeting')
            ->setMeeting($meeting)
            ->setUser($user)
            ->setHost($host)
            ->getPrinter()
            ->getContents()
        ;
    }

    /**
     * @return MailerService
     */
    private function getMailer(): MailerService
    {
        return $this->getContainer()->get('app.service.mailer');
    }

    /**
     * @param Meeting $meeting
     *
     * @return Collection|MeetingParticipant[]
     */
    private function getMeetingParticipants(Meeting $meeting)
    {
        return $meeting
            ->getMeetingParticipants()
            ->filter(
                function (MeetingParticipant $meetingParticipant) {
                    return $meetingParticipant->getInDistributionList() && $meetingParticipant->getUser();
                }
            )
        ;
    }
}
