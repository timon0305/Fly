<?php

namespace Fly\UserBundle\Controller;


use Fly\UserBundle\Form\Type\GroupFormStepType;
use FOS\UserBundle\Event\FilterGroupResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GroupEvent;


class StepFormController extends Controller
{


    public function createGroupStepsAction()
    {
        $groupManager = $this->get('fos_user.group_manager');
        $group = $groupManager->createGroup('');
//        $group->setName(time());
        $formFactory = $this->get('fos_user.group.form.factory');
        $form = $formFactory->createForm();
        $form->setData($group);


        $friends = $this->get('doctrine.orm.entity_manager')->getRepository('FlyUserBundle:Friends')->getOnlyFriendsList($this->getUser());

        return $this->render('FlyUserBundle:Group/new:modal.html.twig', [
            'form'=>$form->createView(),
            'group'=>$group,
            'friends'=>$friends,
        ]);
    }


    /**
     * Save group
     */
    public function newAction(Request $request)
    {

        /** @var $groupManager \FOS\UserBundle\Model\GroupManagerInterface */
        $groupManager = $this->get('fos_user.group_manager');
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.group.form.factory');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $group = $groupManager->createGroup('');
//        $group->setName(time());
//        var_dump(get_class_methods($group));die;


        $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_INITIALIZE, new GroupEvent($group, $request));

//        $form = $this->createForm(new GroupFormStepType());
        $form = $formFactory->createForm();
        $form->setData($group);

        $form->handleRequest($request);

        if ($form->isValid()) {
            //var_dump('$form->isValid()');die;
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_SUCCESS, $event);

            /*Upload image */
//            $this->get('fly.local.uploader')->uploadFile($form->getData(), @$group);

            /*Update Invitation*/
//            foreach($group->getInvitation() as $inv){
//                $inv->setGroup($group);
//                $invUser = $this->get('doctrine.orm.entity_manager')->getRepository('FlyUserBundle:User')->findOneByEmail($inv->getEmail());
//                if($invUser){
//                    $inv->setUser($invUser);
//                }
//            }


//          /*Set the owner*/
            $group->setOwner($this->getUser());
            $groupManager->updateGroup($group);

            $user = $this->get('doctrine.orm.entity_manager')->getRepository('FlyUserBundle:User')->find($this->getUser()->getId());
            $user->addGroup($group);
            $this->get('doctrine.orm.entity_manager')->persist($user);
            $this->get('doctrine.orm.entity_manager')->flush();

            /*Invitation*/
            $this->get('fly.invitation.sender')->send($group);
            die('Invitation sended');

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_group_list');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::GROUP_CREATE_COMPLETED, new FilterGroupResponseEvent($group, $request, $response));

            return $response;
        }
//        var_dump($form->getErrors());
//        var_dump($form->getErrors()->count());
//        die;

        return $this->render('FlyUserBundle:Group/new:modal.html.twig', ['form'=>$form->createView(), 'group'=>$group]);
    }





}
