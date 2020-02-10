<?php

namespace Fly\UserBundle\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



/**
 * Feed controller.
 *
 */
class WalletController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $notificationsQuery = $em->getRepository('FlyPlatformBundle:FlyOrder')->getUserWalletQuery($this->getUser());

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $notificationsQuery,
            $this->get('request')->query->get('page', 1)/*page number*/,
            $this->container->getParameter('pagination_limit')/*limit per page*/
        );

//        $this->updateUnreadNotifications($pagination);

        return $this->render('FlyUserBundle:Wallet:index.html.twig', ['pagination'=>$pagination]);
    }






}
