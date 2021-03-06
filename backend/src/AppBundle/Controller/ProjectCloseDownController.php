<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProjectCloseDown;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * @Route("/project-close-down")
 */
class ProjectCloseDownController extends Controller
{
    /**
     * @Route("/{id}.pdf", name="app_project_close_down_pdf", options={"expose"=true})
     *
     * @throws ServiceUnavailableHttpException
     *
     * @param ProjectCloseDown $projectCloseDown
     *
     * @return Response
     */
    public function pdfAction(ProjectCloseDown $projectCloseDown)
    {
        $pdf = $this
            ->get('app.service.pdf')
            ->getProjectCloseDownPDF($projectCloseDown->getProjectId())
        ;

        if (!$pdf) {
            throw new ServiceUnavailableHttpException();
        }

        $date = $projectCloseDown->getCreatedAt();
        $translator = $this->get('translator');

        return new Response(file_get_contents($pdf), Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf(
                'attachment; filename="%s - %s - %s.pdf"',
                $date->format('ymd'),
                $translator->trans('message.close_down_report', [], 'messages'),
                $projectCloseDown->getProject()->getName()
            ),
        ]);
    }

    /**
     * @Route("/{id}", name="app_project_close_down_view", requirements={"id":"\d+"})
     *
     * @param ProjectCloseDown $projectCloseDown
     *
     * @return Response
     */
    public function viewAction(ProjectCloseDown $projectCloseDown)
    {
        return $this->render(
            ':project_close_down:pdf.html.twig',
            [
                'projectCloseDown' => $projectCloseDown,
            ]
        );
    }
}
