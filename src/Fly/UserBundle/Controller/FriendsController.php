<?php

namespace Fly\UserBundle\Controller;

use Fly\UserBundle\Entity\Friends;
use Fly\UserBundle\Entity\GroupInvitation;
use Fly\UserBundle\FlyUserBundle;
use Fly\UserBundle\Form\Type\UserGroupListType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;



/**
 * Feed controller.
 *
 */
class FriendsController extends Controller
{
    public function indexAction()
    {
        $user = $this->getUser();
        $friendsQuery = $this->get('doctrine.orm.entity_manager')->getRepository('FlyUserBundle:Friends')->getFriendsList($user,true);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $friendsQuery,
            $this->get('request')->query->get('page', 1)/*page number*/,
            $this->container->getParameter('pagination_limit')/*limit per page*/
        );

        return $this->render('FlyUserBundle:Friends:index.html.twig', array(
            'user' => $user,
            'pagination'=>$pagination,
        ));
    }


    public function inviteAction(Request $request)
    {

        $email = $request->request->get('email');
        if($email){
            $em = $this->get('doctrine.orm.entity_manager');
            $user = $this->getUser();
            $userManager = $this->get('fos_user.user_manager');
            $userFriend = $userManager->findUserByEmail($email);
            $inviteToken = null;
//            var_dump($email,$userFriend instanceof UserInterface, $userFriend);die;
            if (null === $userFriend || !$userFriend instanceof UserInterface){

                //create new user
                $inviteToken = md5(uniqid());

                $userFriend = $userManager->createUser();
                $userFriend->setEmail($email);
                $userFriend->setUsername($email);
                $userFriend->setPlainPassword(md5(uniqid()));
                $userFriend->setEnabled(false);
                $userFriend->setConfirmationToken($inviteToken);
                $userManager->updateUser($userFriend);

                //create frind hemself
                $friendHim = new Friends();
                $friendHim->setFriendOne($userFriend);
                $friendHim->setFriendTwo($userFriend);
                $friendHim->setStatus(2);
                $em->persist($friendHim);
                $em->flush();

                // create friendships relation
                $friendsObj = new Friends();
                $friendsObj->setFriendOne($user);
                $friendsObj->setFriendTwo($userFriend);
                $friendsObj->setStatus(0);
                $em->persist($friendsObj);
                $em->flush();

            }else{
                $friendsObj = $em->getRepository('FlyUserBundle:Friends')->getFriendObject($user,$userFriend);
                if(!$friendsObj){
                    //set friends
                    $friendsObj = new Friends();
                    $friendsObj->setFriendOne($user);
                    $friendsObj->setFriendTwo($userFriend);
                    $em->persist($friendsObj);
                    $em->flush();

                }else{
                    return JsonResponse::create(['asc'=>'error', 'msg'=>'You can`t Invite this person, because you always friends.']);
                }
            }

            //send email
            $res = $this->sendEmail($email,$user,$inviteToken);

            $notification = $this->get('fly.user.notification.service');
            $notification->addNotice($userFriend,$user, FlyUserBundle::getNoticeTypeFriends());

//            var_dump($res);die;
            return JsonResponse::create(['asc'=>'success', 'friend'=>$userFriend]);
        }

        return JsonResponse::create(['asc'=>'error', 'msg'=>"Please Fill Email field."]);
    }

    public function inviteGroupAction(Request $request)
    {
//        $groupListForm = $this->createForm(new UserGroupListType(),$this->getUser());
//        $groupListForm->handleRequest($request);
//        if($groupListForm->isValid() && $groupListForm->isSubmitted()){
//            var_dump($groupListForm->getData());die;
//        }
        $formData = $request->get('fly_userbundle_user_group_list');

//        var_dump($formData); die;
        if($formData['id'] && $formData['groups']){
            $em = $this->get('doctrine.orm.entity_manager');
            // get friendUser
            $friendUser = $em->getRepository('FlyUserBundle:User')->find($formData['id']);
            if(!$friendUser){
                return JsonResponse::create(['asc'=>'error', 'msg'=>"Friend not found"]);
            }
            // get Group
            $group = $em->getRepository('FlyUserBundle:Group')->find($formData['groups']);
            if(!$group){
                return JsonResponse::create(['asc'=>'error', 'msg'=>"Group not found"]);
            }
            //check is user always in group
            if($friendUser->isGroupMember($group)){
                return JsonResponse::create(['asc'=>'error', 'msg'=>"Friend ".$friendUser." always member of group ".$group->getName()]);
            }
            //chek is user always have invite
            $checkInvite = $em->getRepository('FlyUserBundle:GroupInvitation')->findOneBy(['user'=>$friendUser,'group'=>$group]);
            if($checkInvite){
                return JsonResponse::create(['asc'=>'error', 'msg'=>"Friend ".$friendUser." always invited in group ".$group->getName()]);
            }

            //create invite
            $groupInvite = new GroupInvitation();
            $groupInvite->setUser($friendUser);
            $groupInvite->setEmail($friendUser->getEmail());
            $groupInvite->setGroup($group);
            $em->persist($groupInvite);
            $em->flush();

            //send email
            $emailSend = $this->get('fly.invitation.sender')->sendEmail($friendUser->getEmail(),$group->getName(),$groupInvite->getCode());
            //return success

            $notification = $this->get('fly.user.notification.service');
            $notification->addNotice($friendUser,$this->getUser(), FlyUserBundle::getNoticeTypeFriends(), $group);

            return JsonResponse::create(['asc'=>'success', 'msg'=>"Friend ".$friendUser." sucessfully invited in group ".$group->getName(), 'emailSend'=>$emailSend]);

        }

        return JsonResponse::create(['asc'=>'error', 'msg'=>"Please Fill Email field.", 'data'=>['groupId'=>$groupId,'friendId'=>$friendId] ] );
    }

    public function groupInviteFormAction()
    {
        $groupListForm = $this->createForm(new UserGroupListType($this->getUser()),$this->getUser());
        return $this->render('FlyUserBundle:Friends:groupModalList.html.twig',[
                'groupListForm'=>$groupListForm->createview(),
            ]);
    }

    public function confirmAction(Request $request)
    {

        $em = $this->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('FlyUserBundle:User');
        $friendOne = $this->getUser();

        $id = $request->request->get('id');
        $friendTwo = $repo->find($id);


        if($friendTwo){
            //check is always friends
            $friendObj = $em->getRepository('FlyUserBundle:Friends')->getFriendObject($friendOne,$friendTwo);
            if($friendObj){
                if($friendObj->getStatus() == 0){
                    $friendObj->setStatus(1);
                    $em->persist($friendObj);
                    $em->flush();

                    return JsonResponse::create(['asc'=>'success','friend'=>$friendTwo]);
                }else{
                    return JsonResponse::create(['asc'=>'error','msg'=>'Error: Not in pending']);
                }
            }else{
                return JsonResponse::create(['asc'=>'error','msg'=>'Cant find friends records']);
            }
        }else{
            return JsonResponse::create(['asc'=>'error','msg'=>'Cant find friend User']);
        }

    }

    public function ignoreAction(Request $request)
    {

//        $em = $this->get('doctrine.orm.entity_manager');
//        $repo = $em->getRepository('FlyUserBundle:User');
//        $friendOne = $this->getUser();
//
//        $id = $request->request->get('id');
//        $friendTwo = $repo->find($id);
//
//
//        if($friendTwo){
//                var_dump('delete record');
//        }

        die;
    }

    public function unfriendAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('FlyUserBundle:User');
        $friendOne = $this->getUser();
        $id = $request->request->get('id');
        $friendTwo = $repo->find($id);

        if($friendTwo){
            $friendObj = $em->getRepository('FlyUserBundle:Friends')->getFriendObject($friendOne,$friendTwo);
            if($friendObj){
                    $em->remove($friendObj);
                    $em->flush();

                    return JsonResponse::create(['asc'=>'success','friend'=>$friendTwo]);
            }else{
                return JsonResponse::create(['asc'=>'error','msg'=>'Cant find friends records']);
            }
        }else{
            return JsonResponse::create(['asc'=>'error','msg'=>'Cant find friend User']);
        }

    }



    public function restoreAction(Request $request)
    {
//        $em = $this->get('doctrine.orm.entity_manager');
//        $repo = $em->getRepository('FlyUserBundle:User');
//        $friendOne = $this->getUser();
//
//        $id = $request->request->get('id');
//        $friendTwo = $repo->findOneBy(['id'=>$id]);
//
//
//        if($friendTwo){
//            //check is always friends
//            $isUnfriend = $em->getRepository('FlyUserBundle:Friends')->checkUnfriends($friendOne,$friendTwo);
//            var_dump($isUnfriend);
//            if($isUnfriend){
//                var_dump('change statuse to 1 (friends)');
//            }
//        }

        die;

    }



    protected function sendEmail($email,$user,$token = null)
    {
        $tplVal = [
            'email' => $email,
        ];

        if($token){
            $tplVal['invitationLink']= $this->get('router')->getContext()->getHost().$this->get('router')->generate(
                    'fos_user_registration_confirm', ['token'=>$token]
                );

        }else{
            $tplVal['invitationLink']= $this->get('router')->getContext()->getHost().$this->get('router')->generate(
                    'fly_user_friends_index'
                );
        }

        $body = $this->render('FlyPlatformBundle:Emails:friend_invitation.html.twig',$tplVal);

        $message = $this->get('mailer')->createMessage()
            ->setSubject('You have Invitation to Registration!')
            ->setFrom($user->getEmail())
            ->setTo($email)
            ->setBody($body,'text/html')
        ;
       $sended = $this->get('mailer')->send($message);

       return $sended;
    }






}
