<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\ColorStatus;
use MainBundle\Controller\API\ApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api/color-statuses")
 */
class ColorStatusController extends ApiController
{
    /**
     * All ColorStatuses.
     *
     * @Route(name="app_api_color_status_list", options={"expose"=true})
     * @Method({"GET"})
     *
     * @return JsonResponse
     */
    public function listAction()
    {
        $colorStatuses = $this
            ->getDoctrine()
            ->getRepository(ColorStatus::class)
            ->findAll()
        ;

        return $this->createApiResponse($colorStatuses);
    }
}
