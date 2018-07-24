<?php

namespace AppBundle\Services;

use AppBundle\Entity\Project;
use AppBundle\Entity\StatusReport;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Process\Process;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

class PDF
{
    const CONTRACT_URL = 'projects/%id%/contract';
    const PROJECT_CLOSE_DOWN_URL = 'projects/%id%/close-down-report';
    const MEETING_URL = 'meetings/%id%';
    const STATUS_REPORT_URL = 'projects/%projectID%/status-reports/view-status-report/%statusReportID%';

    /** @var RequestStack */
    private $requestStack;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var string */
    private $binaryPath;

    /** @var string */
    private $binaryOptions;

    /** @var string */
    private $serviceUrl;

    /**
     * PDF constructor.
     *
     * @param RequestStack          $requestStack
     * @param TokenStorageInterface $tokenStorage
     * @param string                $binaryPath
     * @param string                $binaryOptions
     * @param string                $serviceUrl
     */
    public function __construct(
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorage,
        string $binaryPath,
        string $binaryOptions,
        string $serviceUrl
    ) {
        $this->requestStack = $requestStack;
        $this->tokenStorage = $tokenStorage;
        $this->binaryPath = $binaryPath;
        $this->binaryOptions = $binaryOptions;
        $this->serviceUrl = $serviceUrl;
    }

    /**
     * @throws \LogicException
     *
     * @return User
     */
    private function getUser()
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            throw new \LogicException('I can only print for authenticated users. #1');
        }

        $user = $token->getUser();

        if (!is_object($user)) {
            throw new \LogicException('I can only print for authenticated users. #2');
        }

        return $user;
    }

    private function run($url, $params)
    {
        $tmpFile = tempnam('/tmp', md5($url));

        $query = [
            'host' => $this->requestStack->getMasterRequest()->getHttpHost(),
            'key' => $this->getUser()->getApiToken(),
        ];

        $options = strtr($this->binaryOptions, [
            '%url%' => $this->serviceUrl.strtr($url, $params).'?'.http_build_query($query),
            '%file%' => $tmpFile,
        ]);

        $process = new Process(
            $this->binaryPath.' '.$options,
            '/tmp'
        );

        $process->run();

        return $process->isSuccessful() ? $tmpFile : null;
    }

    public function getContractPDF(int $id)
    {
        return $this->run(self::CONTRACT_URL, ['%id%' => $id]);
    }

    public function getProjectCloseDownPDF(int $id)
    {
        return $this->run(self::PROJECT_CLOSE_DOWN_URL, ['%id%' => $id]);
    }

    public function getMeetingPDF(int $id)
    {
        return $this->run(self::MEETING_URL, ['%id%' => $id]);
    }

    public function getStatusReportPDF(StatusReport $statusReport)
    {
        Assert::integer($statusReport->getId());
        Assert::isInstanceOf($statusReport->getProject(), Project::class);
        Assert::integer($statusReport->getProject()->getId());

        return $this->run(self::STATUS_REPORT_URL, [
            '%projectID%' => $statusReport->getProjectId(),
            '%statusReportID%' => $statusReport->getId(),
        ]);
    }
}
