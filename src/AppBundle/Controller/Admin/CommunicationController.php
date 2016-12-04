<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Communication;
use AppBundle\Form\Communication\CreateType;
use Symfony\Component\HttpFoundation\Response;

/**
 * CommunicationController controller.
 *
 * @Route("/admin/communication")
 */
class CommunicationController extends Controller
{
    /**
     * Lists all Communication entities.
     *
     * @Route("/list", name="app_admin_communication_list")
     * @Method("GET")
     *
     * @return Response
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $communications = $em
            ->getRepository(Communication::class)
            ->findAll()
        ;

        return $this->render(
            'AppBundle:Admin/Communication:list.html.twig',
            [
                'communications' => $communications,
            ]
        );
    }

    /**
     * @Route("/list/filtered", options={"expose"=true}, name="app_admin_communication_list_filtered")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listByPageAction(Request $request)
    {
        $requestParams = $request->request->all();
        $dataTableService = $this->get('app.service.data_table');
        $response = $dataTableService->paginateByColumn(Communication::class, 'meetingName', $requestParams);

        return new JsonResponse($response);
    }

    /**
     * Displays Communication entity.
     *
     * @Route("/{id}/show", name="app_admin_communication_show", options={"expose"=true})
     * @Method({"GET"})
     *
     * @param Communication $communication
     *
     * @return Response
     */
    public function showAction(Communication $communication)
    {
        return $this->render(
            'AppBundle:Admin/Communication:show.html.twig',
            [
                'communication' => $communication,
            ]
        );
    }

    /**
     * Creates a new Communication entity.
     *
     * @Route("/create", name="app_admin_communication_create", options={"expose"=true})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($form->getData());
            $em->flush();

            $this
                ->get('session')
                ->getFlashBag()
                ->set(
                    'success',
                    $this
                        ->get('translator')
                        ->trans('admin.communication.create.success', [], 'admin')
                )
            ;

            return $this->redirectToRoute('app_admin_communication_list');
        }

        return $this->render(
            'AppBundle:Admin/Communication:create.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="app_admin_communication_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     *
     * @param Communication $communication
     * @param Request       $request
     *
     * @return Response|RedirectResponse
     */
    public function editAction(Communication $communication, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CreateType::class, $communication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($communication);
            $em->flush();

            $this
                ->get('session')
                ->getFlashBag()
                ->set(
                    'success',
                    $this
                        ->get('translator')
                        ->trans('admin.communication.edit.success', [], 'admin')
                )
            ;

            return $this->redirectToRoute('app_admin_communication_list');
        }

        return $this->render(
            'AppBundle:Admin/Communication:edit.html.twig',
            [
                'id' => $communication->getId(),
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="app_admin_communication_delete", options={"expose"=true})
     * @Method({"GET"})
     *
     * @param Request $request
     */
    public function deleteAction(Communication $communication, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($communication);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            $message = [
                'delete' => 'success',
            ];

            return new JsonResponse($message, Response::HTTP_OK);
        }

        $this
            ->get('session')
            ->getFlashBag()
            ->set(
                'success',
                $this
                    ->get('translator')
                    ->trans('admin.communication.delete.success.general', [], 'admin')
            )
        ;

        return $this->redirectToRoute('app_admin_communication_list');
    }
}
