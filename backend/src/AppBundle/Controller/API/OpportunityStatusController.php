<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\OpportunityStatus;
use AppBundle\Form\OpportunityStatus\BaseType;
use MainBundle\Controller\API\ApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/opportunity-statuses")
 */
class OpportunityStatusController extends ApiController
{
    const ENTITY_CLASS = OpportunityStatus::class;
    const FORM_CLASS = BaseType::class;

    /**
     * Get all opportunity statuses.
     *
     * @Route("/", name="app_api_opportunity_statuses", options={"expose"=true})
     * @Method({"GET"})
     *
     * @return JsonResponse
     */
    public function indexAction()
    {
        $opportunityStatuses = $this
            ->get('app.repository.opportunity_status')
            ->findAll()
        ;

        return $this->createApiResponse($opportunityStatuses);
    }

    /**
     * Create a new OpportunityStatus.
     *
     * @Route(name="app_api_opportunity_statuses_create", options={"expose"=true})
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        $form = $this->getForm(null, ['method' => $request->getMethod()]);
        $this->processForm($request, $form);

        if ($form->isValid()) {
            $this->persistAndFlush($form->getData());

            return $this->createApiResponse($form->getData(), Response::HTTP_CREATED);
        }

        $errors = $this->getFormErrors($form);
        $errors = [
            'messages' => $errors,
        ];

        return $this->createApiResponse($errors, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Get Opportunity Status by id.
     *
     * @Route("/{id}", name="app_api_opportunity_statuses_get", options={"expose"=true})
     * @Method({"GET"})
     *
     * @param OpportunityStatus $opportunityStatus
     *
     * @return JsonResponse
     */
    public function getAction(OpportunityStatus $opportunityStatus)
    {
        return $this->createApiResponse($opportunityStatus);
    }

    /**
     * Edit a specific Opportunity Status.
     *
     * @Route("/{id}", name="app_api_opportunity_statuses_edit", options={"expose"=true})
     * @Method({"PUT", "PATCH"})
     *
     * @param Request           $request
     * @param OpportunityStatus $opportunityStatus
     *
     * @return JsonResponse
     */
    public function editAction(Request $request, OpportunityStatus $opportunityStatus)
    {
        $form = $this->getForm($opportunityStatus, ['method' => $request->getMethod()]);
        $this->processForm($request, $form, $request->isMethod(Request::METHOD_PUT));

        if ($form->isValid()) {
            $this->persistAndFlush($opportunityStatus);

            return $this->createApiResponse($opportunityStatus, Response::HTTP_ACCEPTED);
        }

        $errors = $this->getFormErrors($form);
        $errors = [
            'messages' => $errors,
        ];

        return $this->createApiResponse($errors, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Delete a specific Opportunity Status.
     *
     * @Route("/{id}", name="app_api_opportunity_statuses_delete", options={"expose"=true})
     * @Method({"DELETE"})
     *
     * @param OpportunityStatus $opportunityStatus
     *
     * @return JsonResponse
     */
    public function deleteAction(OpportunityStatus $opportunityStatus)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($opportunityStatus);
        $em->flush();

        return $this->createApiResponse(null, Response::HTTP_NO_CONTENT);
    }
}
