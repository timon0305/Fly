<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fly\UserBundle\Controller;

use Fly\UserBundle\FlyUserBundle;
use Fly\UserBundle\Entity\Invitation;
use Fly\UserBundle\Form\Type\GoalsType;
use Fly\UserBundle\Form\Type\GroupFormType;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterGroupResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseGroupEvent;
use FOS\UserBundle\Event\GroupEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Fly\UserBundle\Entity\Feed;
use Fly\UserBundle\Form\Type\FeedType;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Fly\UserBundle\Entity\GroupInvitation;

/**
 * RESTful controller managing group CRUD
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class GroupController extends Controller
{
    /**
     * Show all groups
     */
    public function listAction()
    {
//        $groups = $this->get('fos_user.group_manager')->findGroups();
        $groups = $this->getUser()->getGroups();
        return $this->render('FlyUserBundle:Group:list.html.twig', array(
            'groups' => $groups
        ));
    }

    /**
     * Show one group
     */
    public function showAction($groupName)
    {

        $group = $this->findGroupBy('name', $groupName);
        $feeds = $this->getDoctrine()->getManager()->getRepository('FlyUserBundle:Feed')->findBy(['group'=>$group],['updated'=>'DESC']);

        $feed = new Feed();
        $feed->setGroup($group);
        $feed->setUser($this->getUser());
        $formFeed = $this->createForm(new FeedType(), $feed, array(
            'action' => $this->generateUrl('group_feed_create',['groupName'=>$groupName]),
            'method' => 'POST',
        ));

        $userHasInvite = $this->get('doctrine.orm.entity_manager')->getRepository('FlyUserBundle:GroupInvitation')
            ->findOneBy(['group'=>$group,'user'=>$this->getUser()]);

        return $this->render('FOSUserBundle:Group:show.html.twig', array(
            'group' => $group,
            'feeds'=>$feeds,
            'formFeed'=>$formFeed->createView(),
            'userHasInvite'=>$userHasInvite,
        ));
    }

    /**
     * Edit one group, show the edit form
     */
    public function editAction(Request $request, $groupName)
    {

        $group = $this->findGroupBy('name', $groupName);

        if(!$this->getUser()->isGroupOwner($group)){
            throw new ForbiddenOverwriteException('Access Denied');
        }
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseGroupEvent($group, $request);
        $dispatcher->dispatch(FOSUserEvents::GROUP_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.group.form.factory');

        $form = $formFactory->createForm();
        $form->setData($group);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            /** @var $groupManager \FOS\UserBundle\Model\GroupManagerInterface */
            $groupManager = $this->get('fos_user.group_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::GROUP_EDIT_SUCCESS, $event);

            /*Upload image */
            $this->get('fly.local.uploader')->uploadFile($form->getData(), @$group);

            /*Set the owner*/
            $group->setOwner($this->getUser());
            $groupManager->updateGroup($group);
            $em->persist($group);
            $em->flush();


            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_group_list', array('groupName' => $group->getName()));
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::GROUP_EDIT_COMPLETED, new FilterGroupResponseEvent($group, $request, $response));

            return $response;
        }

        return $this->render('FOSUserBundle:Group:edit.html.twig', array(
            'form'      => $form->createview(),
            'group_name'  => $group->getName(),
            'group' => $group
        ));
    }

    /**
     * Show the new form
     */
    public function newAction(Request $request)
    {
//        $friendsListInvite = $request->get('friends-list-invite');
//        dump($friendsListInvite);die;

        /** @var $groupManager \FOS\UserBundle\Model\GroupManagerInterface */
        $groupManager = $this->get('fos_user.group_manager');
        $userManager = $this->get('fos_user.user_manager');
        $notification = $this->get('fly.user.notification.service');
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.group.form.factory');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $group = $groupManager->createGroup('');
        $group->setName(time());



        $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_INITIALIZE, new GroupEvent($group, $request));

        $form = $formFactory->createForm();
        $form->setData($group);

        $form->handleRequest($request);

//        var_dump(get_class_methods($group));die;
        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_SUCCESS, $event);

            /*Upload image */
            $this->get('fly.local.uploader')->uploadFile($form->getData(), @$group);

            /*Update Invitation*/
            foreach($group->getInvitation() as $inv){
                $inv->setGroup($group);
                $invUser = $this->get('doctrine.orm.entity_manager')->getRepository('FlyUserBundle:User')->findOneByEmail($inv->getEmail());
                if($invUser){
                    $inv->setUser($invUser);
                    $notification->addNotice($invUser,$this->getUser(),FlyUserBundle::getNoticeTypeGroupInvite(),$group);
                }
            }

            /*Check friends invites*/
            $friendsListInvite = $request->get('friends-list-invite');
            if(count($friendsListInvite)){
                foreach($friendsListInvite as $friendId){
                    $invFriend = $userManager->findUserBy(['id'=>$friendId]);
                    if($invFriend){
                        $notification->addNotice($invFriend,$this->getUser(),FlyUserBundle::getNoticeTypeGroupInvite(),$group);
                    }
                }
            }


//          /*Set the owner*/
            $group->setOwner($this->getUser());
            $groupManager->updateGroup($group);

            $user = $this->get('doctrine.orm.entity_manager')->getRepository('FlyUserBundle:User')->find($this->getUser()->getId());
            $user->addGroup($group);
            $this->get('doctrine.orm.entity_manager')->persist($user);
            $this->get('doctrine.orm.entity_manager')->flush();

            /*Invitation*/
            $this->get('fly.invitation.sender')->send($group);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_group_list');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_COMPLETED, new FilterGroupResponseEvent($group, $request, $response));

//            return $response;
            return $this->redirectToRoute('fos_user_group_show',['groupName'=>$group->getName()]);
        }

//
        return $this->render('FOSUserBundle:Group:new.html.twig', array(
            'form' => $form->createview(),
        ));
    }


    public function registerInvitedUsersAction(Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            $this->addFlash('success','Your always registered');
            $url = $this->generateUrl('fos_user_profile_show');
            $response = new RedirectResponse($url);

            return $response;
        }
        if($request->get('code')){

            $em = $this->get('doctrine.orm.entity_manager');
            $invite = $em->getRepository('FlyUserBundle:GroupInvitation')->findOneByCode($request->get('code'));
            if($invite){
                //Create User
                $registerService = $this->get('fly.user.register.service');
                $pwd = $registerService->generateRandomPassword();

                $userManager = $this->get('fos_user.user_manager');
                $user = $userManager->createUser();
                $user->setEmail($invite->getEmail());
                $user->setUsername($invite->getEmail());
                $user->setPlainPassword($pwd);
                $user->setEnabled(true);
                $userManager->updateUser($user);


                $dispatcher = $this->get('event_dispatcher');

                $event = new GetResponseUserEvent($user, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

                // Here, "public" is the name of the firewall in your security.yml
                $token = new UsernamePasswordToken($user, $user->getPassword(), "public", $user->getRoles());

                // For older versions of Symfony, use security.context here
                $this->get("security.token_storage")->setToken($token);
                // Fire the login event
                // Logging the user in above the way we do it doesn't do this automatically
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

                //send email about seccess registration
                $this->sendEmail($user,$pwd);
                //set invite user
                $invite->setUser($user);
                $em->persist($invite);
                $em->flush();

                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);

                return $response;

            }

        }

        // ivitation activation is failed
        $this->addFlash('error','Error while processing registration');
        return $this->redirectToRoute('fos_user_security_login');

    }

    /**
     * Disable one group
     */
    public function disableAction(Request $request, $groupName)
    {

        $em = $this->get('doctrine.orm.entity_manager');
        try{
            $group = $this->findGroupBy('name', $groupName);

        }catch(\Exception $e){
            $this->addFlash('error',$e->getMessage());
            $response = new RedirectResponse($this->generateUrl('fly_platform_home'));
            return $response;
        }

        if(!$this->getUser()->isGroupOwner($group)){
            throw new ForbiddenOverwriteException('Access Denied');
        }

        $group->setIsActive(false);
        $em->persist($group);
        $em->flush();

        $this->addFlash('success','Group was Disabled');

        return  new RedirectResponse($this->generateUrl('fos_user_group_edit',['groupName'=>$group->getName()]));

    }

    /**
     * Enable one group
     */
    public function enableAction(Request $request, $groupName)
    {

        $em = $this->get('doctrine.orm.entity_manager');
        try{
            $group = $this->findGroupBy('name', $groupName);

        }catch(\Exception $e){
            $this->addFlash('error',$e->getMessage());
            $response = new RedirectResponse($this->generateUrl('fly_platform_home'));
            return $response;
        }

        if(!$this->getUser()->isGroupOwner($group)){
            throw new ForbiddenOverwriteException('Access Denied');
        }
        $group->setIsActive(true);
        $em->persist($group);
        $em->flush();

        $this->addFlash('success','Group was Enabled');

        return  new RedirectResponse($this->generateUrl('fos_user_group_edit',['groupName'=>$group->getName()]));

    }


    /**
     * Delete one group
     */
    public function deleteAction(Request $request, $groupName)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        try{
            $group = $this->findGroupBy('name', $groupName);

        }catch(\Exception $e){
            $this->addFlash('error',$e->getMessage());
            $response = new RedirectResponse($this->generateUrl('fly_platform_home'));
            return $response;
        }

        if(!$this->getUser()->isGroupOwner($group)){
            throw new ForbiddenOverwriteException('Access Denied');
        }

        //chek relations
//        1. Invitation
        $groupInv = $group->getInvitation();
        if(count($groupInv)){
            foreach($groupInv as $inv){
                $group->removeInvitation($inv);
                $em->remove($inv);
                $em->flush();
            }
        }
//        2. Feeds Resources
        $feeds = $group->getFeed();
        if(count($feeds)){
            foreach($feeds as $feed){

                $group->removeFeed($feed);

                $em->remove($feed);
                $em->flush();
            }
        }
//        3. Group image

        $this->get('fos_user.group_manager')->deleteGroup($group);

        $response = new RedirectResponse($this->generateUrl('fly_platform_home'));

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(FOSUserEvents::GROUP_DELETE_COMPLETED, new FilterGroupResponseEvent($group, $request, $response));

        return $response;
    }


    /**
     * @param Request $request
     * @param $groupName
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function inviteFriendsAction(Request $request, $groupName)
    {
        try{
            $group = $this->findGroupBy('name', $groupName);

        }catch(\Exception $e){
            $this->addFlash('error',$e->getMessage());
            $response = new RedirectResponse($this->generateUrl('fly_platform_home'));
            return $response;
        }

        if(!$this->getUser()->isGroupOwner($group)){
            throw new ForbiddenOverwriteException('Access Denied');
        }

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlyUserBundle:Friends')->getFriendsList($this->getUser(),true);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $this->get('request')->query->get('page', 1)/*page number*/,
            $this->container->getParameter('pagination_limit')/*limit per page*/
        );

        return $this->render('FlyUserBundle:Group:invite_friends.html.twig',
            [
                'group'=>$group,
                'pagination'=>$pagination,
            ]);
    }

    public function inviteFriendsToGroupAction(Request $request, $groupName)
    {
        try{
            $group = $this->findGroupBy('name', $groupName);

        }catch(\Exception $e){
            return JsonResponse::create(['asc' => 'error','msg'=>'Group not found.'],200);
        }

        if(!$this->getUser()->isGroupOwner($group)){
            return JsonResponse::create(['asc' => 'error','msg'=>'Access Denied'],200);
        }

        $friendId = $request->get('friendId');

        if(!$friendId){
            return JsonResponse::create(['asc' => 'error','msg'=>'Friend not found.'],200);
        }

        $em = $this->getDoctrine()->getManager();

        $friend = $em->getRepository('FlyUserBundle:User')->find($friendId);

        if(!$friend){
            return JsonResponse::create(['asc' => 'error','msg'=>'Friend not found.'],200);
        }

        //check is user always in group
        if($friend->isGroupMember($group)){
            return JsonResponse::create(['asc'=>'error', 'msg'=>"Friend ".$friend." always member of group ".$group->getName()]);
        }
        //chek is user always have invite
        $checkInvite = $em->getRepository('FlyUserBundle:GroupInvitation')->findOneBy(['user'=>$friend,'group'=>$group]);
        if($checkInvite){
            return JsonResponse::create(['asc'=>'error', 'msg'=>"Friend ".$friend." always invited in group ".$group->getName()]);
        }

        //create invite
        $groupInvite = new GroupInvitation();
        $groupInvite->setUser($friend);
        $groupInvite->setEmail($friend->getEmail());
        $groupInvite->setGroup($group);
        $em->persist($groupInvite);
        $em->flush();

        //send email
        $emailSend = $this->get('fly.invitation.sender')->sendEmail($friend->getEmail(),$group->getName(),$groupInvite->getCode());
        //return success

        $notification = $this->get('fly.user.notification.service');
        $notification->addNotice($friend,$this->getUser(), FlyUserBundle::getNoticeTypeFriends(), $group);

        return JsonResponse::create(['asc'=>'success', 'msg'=>"Friend ".$friend." sucessfully invited in group ".$group->getName(), 'emailSend'=>$emailSend]);

    }



    /**
     * Find a group by a specific property
     *
     * @param string $key   property name
     * @param mixed  $value property value
     *
     * @throws NotFoundHttpException                if user does not exist
     * @return \FOS\UserBundle\Model\GroupInterface
     */
    protected function findGroupBy($key, $value)
    {
        if (!empty($value)) {
            $group = $this->get('fos_user.group_manager')->{'findGroupBy'.ucfirst($key)}($value);
        }

        if (empty($group)) {
            throw new NotFoundHttpException(sprintf('The group with "%s" does not exist for value "%s"', $key, $value));
        }

        return $group;
    }


    protected function checkGroupGoals($goalsArray, $goal)
    {
        $exist = false;
        foreach ($goalsArray as $g){
            if($g['title'] == $goal->getTitle()){
                $exist = true;
            }
        }
        return $exist;
    }

    protected function sendEmail($user,$password)
    {
        $tplVal = [
            'user' => $user,
            'profileLink'=> $this->get('router')->getContext()->getHost().$this->get('router')->generate(
                    'fos_user_profile_show'
                ),
            'password'=>$password,
        ];


        $body = $this->render('FlyPlatformBundle:Emails:invitation_confirmation.html.twig',$tplVal);

        $message = $this->get('mailer')->createMessage()
            ->setSubject('Welcome to Flyeurope20 platform.')
            ->setFrom('admin@flyeurope20.com')
            ->setTo($user->getEmail())
            ->setBody($body,'text/html')
        ;
        $sended = $this->get('mailer')->send($message);

        return $sended;
    }
}
