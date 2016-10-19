<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Portfolio;
use AppBundle\Form\Portfolio\CreateType as PortfolioCreateType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Portfolio controller.
 *
 * @Route("/admin/portfolio")
 */
class PortfolioController extends Controller
{
    /**
     * Lists all Portfolio entities.
     *
     * @Route("/list", name="app_admin_portfolio_list")
     * @Method("GET")
     *
     * @return Response
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $portfolios = $em
            ->getRepository(Portfolio::class)
            ->findAll()
        ;

        return $this->render(
            'AppBundle:Admin/Portfolio:list.html.twig',
            [
                'portfolios' => $portfolios,
            ]
        );
    }

    /**
     * Creates a new Portfolio entity.
     *
     * @Route("/create", name="app_admin_portfolio_create")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        $portfolio = new Portfolio();
        $form = $this->createForm(PortfolioCreateType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($portfolio);
            $em->flush();

            $this
                ->get('session')
                ->getFlashBag()
                ->set(
                    'success',
                    $this
                        ->get('translator')
                        ->trans('admin.portfolio.create.success', [], 'admin')
                )
            ;

            return $this->redirectToRoute('app_admin_portfolio_list');
        }

        return $this->render(
            'AppBundle:Admin/Portfolio:create.html.twig',
            [
                'portfolio' => $portfolio,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Portfolio entity.
     *
     * @Route("/{id}/edit", name="app_admin_portfolio_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request   $request
     * @param Portfolio $portfolio
     *
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, Portfolio $portfolio)
    {
        $form = $this->createForm(PortfolioCreateType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $portfolio->setUpdatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($portfolio);
            $em->flush();

            $this
                ->get('session')
                ->getFlashBag()
                ->set(
                    'success',
                    $this
                        ->get('translator')
                        ->trans('admin.portfolio.edit.success', [], 'admin')
                )
            ;

            return $this->redirectToRoute('app_admin_portfolio_list');
        }

        return $this->render(
            'AppBundle:Admin/Portfolio:edit.html.twig',
            [
                'portfolio' => $portfolio,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Displays a Portfolio entity.
     *
     * @Route("/{id}/show", name="app_admin_portfolio_show")
     * @Method({"GET"})
     *
     * @param Portfolio $portfolio
     *
     * @return Response
     */
    public function showAction(Portfolio $portfolio)
    {
        return $this->render(
            'AppBundle:Admin/Portfolio:show.html.twig',
            [
                'portfolio' => $portfolio,
            ]
        );
    }

    /**
     * Deletes a Portfolio entity.
     *
     * @Route("/{id}/delete", name="app_admin_portfolio_delete")
     * @Method({"GET"})
     *
     * @param Portfolio $portfolio
     *
     * @return RedirectResponse
     */
    public function deleteAction(Portfolio $portfolio)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($portfolio);
        $em->flush();

        $this
            ->get('session')
            ->getFlashBag()
            ->set(
                'success',
                $this
                    ->get('translator')
                    ->trans('admin.portfolio.delete.success', [], 'admin')
            )
        ;

        return $this->redirectToRoute('app_admin_portfolio_list');
    }
}
