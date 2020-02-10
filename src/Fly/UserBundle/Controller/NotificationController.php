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
class NotificationController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $notificationsQuery = $em->getRepository('FlyUserBundle:Notification')->getUserNotificationsQuery($this->getUser());

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $notificationsQuery,
            $this->get('request')->query->get('page', 1)/*page number*/,
            $this->container->getParameter('pagination_limit')/*limit per page*/
        );

        $this->updateUnreadNotifications($pagination);

        return $this->render('FlyUserBundle:Notification:index.html.twig', ['pagination'=>$pagination]);
    }
    public function sidebarAction()
    {
        $noticeService = $this->get('fly.user.notification.service');
        $userNotification = $noticeService->getUserNotifications($this->getUser());

        return $this->render('FlyUserBundle:Notification:sidebar.html.twig', $userNotification);
    }

    public function navAction()
    {
        $noticeService = $this->get('fly.user.notification.service');
        $userNotification = $noticeService->getUserNotifications($this->getUser());

        return $this->render('FlyUserBundle:Notification:nav.html.twig', $userNotification);
    }

    public function removeNoticeAjaxAction(Request $request)
    {
        $user = $this->getUser();

        $noticeId = $request->get('noticeId');
        if(!$noticeId){
            return JsonResponse::create(['asc'=>'error', 'msg'=>"Not found"]);
        }

        $em = $this->getDoctrine()->getManager();

        $notice = $em->getRepository('FlyUserBundle:Notification')->findOneBy(['user'=>$user,'id'=>$noticeId]);
        if(!$notice){
            return JsonResponse::create(['asc'=>'error', 'msg'=>"Notice not found"]);
        }

        $em->remove($notice);
        $em->flush();

        $countNotices = $em->getRepository('FlyUserBundle:Notification')->findOneBy(['user'=>$user]);

        return JsonResponse::create(['asc'=>'success', 'msg'=>"Notice was removed", "countNotices"=>count($countNotices) ]);

    }

    protected function updateUnreadNotifications($pagination)
    {
        $ids = [];
        foreach($pagination as $item){
            if(!$item->getIsRead())
            $ids[] = $item->getId();
        }
        if(count($ids)){
            $this->get('doctrine.orm.entity_manager')->getRepository('FlyUserBundle:Notification')->updateUnread($ids);
        }
    }





}
