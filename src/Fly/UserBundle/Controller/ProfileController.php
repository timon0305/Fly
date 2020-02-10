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

use Fly\UserBundle\Form\Type\ResetPasswordType;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Fly\UserBundle\Form\Type\UserBasicInfoType;
use Fly\UserBundle\Form\Type\UserContactType;
use Fly\UserBundle\Form\Type\UserEmailType;
use Fly\UserBundle\Form\Type\UserSummaryType;



/**
 * Controller managing the user profile
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends Controller
{
    /**
     * Show the user
     */
    public function showAction()
    {

        $user = $this->getUser();

        if ($user->getEmail()=='') {
            
            return $this->redirect($this->generateUrl('fly_user_profile_change_email'));
        }
        
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $summaryForm = $this->createForm(new UserSummaryType(),$user,[
            'action' => $this->generateUrl('fly_user_profile_update_summary'),
            'method' => 'POST',
        ]);
        
        $basicForm = $this->createForm(new UserBasicInfoType(),$user,[
            'action' => $this->generateUrl('fly_user_profile_update_basic_info'),
            'method' => 'POST',
        ]);
        
        $contactForm = $this->createForm(new UserContactType(),$user,[
            'action' => $this->generateUrl('fly_user_profile_update_contact'),
            'method' => 'POST',
        ]);

        $formFactory = $this->get('fos_user.change_password.form.factory');

        $changePasswordForm = $formFactory->createForm();
        $id = $user->getFacebookId();



        
        return $this->render('FOSUserBundle:Profile:show.html.twig', array(
            'user' => $user,
            'summaryForm'=>$summaryForm->createView(),
            'basicForm'=>$basicForm->createView(),
            'contactForm'=>$contactForm->createView(),
            'passwordForm'=>$changePasswordForm->createView(),
            'tab'=>'about',
            'id' => $id,
        ));
    }

    public function changeEmailAction(Request $request){
        
        $user = $this->getUser();
        
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $contactForm = $this->createForm(new UserEmailType(),$user,[
            'action' => $this->generateUrl('fly_user_profile_update_email'),
            'method' => 'POST',
        ]);

        return $this->render('FOSUserBundle:Profile:contactInfo.html.twig', array(
            'user' => $user,
            'contactForm'=>$contactForm->createView(),
        ));

    }

    /**
     * Edit the user
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('FOSUserBundle:Profile:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateSummaryAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(new UserSummaryType(),$user,[
            'action' => $this->generateUrl('fly_user_profile_update_summary'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);
        if($form->isValid()){
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
        }
        return $this->render('FOSUserBundle:Profile:form_summary.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateBasicInfoAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(new UserBasicInfoType(),$user,[
            'action' => $this->generateUrl('fly_user_profile_update_basic_info'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);
        if($form->isValid()){
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
        }
        return $this->render('FOSUserBundle:Profile:form_basic_info.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateContactAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(new UserContactType(),$user,[
            'action' => $this->generateUrl('fly_user_profile_update_contact'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);
        if($form->isValid()){
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
        }
        return $this->render('FOSUserBundle:Profile:form_contact.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateEmailAction(Request $request)
    {
        $user = $this->getUser();
        
        $form = $this->createForm(new UserEmailType(),$user,[
            'action' => $this->generateUrl('fly_user_profile_update_email'),
            'method' => 'POST',
        ]);
        
        $form->handleRequest($request);
        
        if($form->isValid()){
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
        }

        $this->addFlash('info','Your Email Address saved successfully!!');
        return $this->redirect($this->generateUrl('fos_user_profile_show'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function updateSideProfileAction()
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            return new Response('login');
        }

        return $this->render('FlyPlatformBundle:Partials:side_profile_menu.html.twig');

    }

    /**
     * Change user password
     */
    public function changePasswordAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.change_password.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {

            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            $this->addFlash('success','Password Successfully Changed !');
            return $this->redirectToRoute('fos_user_profile_show');
        }


        // form not valid
        $summaryForm = $this->createForm(new UserSummaryType(),$user,[
            'action' => $this->generateUrl('fly_user_profile_update_summary'),
            'method' => 'POST',
        ]);

        $basicForm = $this->createForm(new UserBasicInfoType(),$user,[
            'action' => $this->generateUrl('fly_user_profile_update_basic_info'),
            'method' => 'POST',
        ]);

        $contactForm = $this->createForm(new UserContactType(),$user,[
            'action' => $this->generateUrl('fly_user_profile_update_contact'),
            'method' => 'POST',
        ]);


        $id = $user->getFacebookId();


        $this->addFlash('danger','Error');

        return $this->render('FOSUserBundle:Profile:show.html.twig', array(
            'user' => $user,
            'summaryForm'=>$summaryForm->createView(),
            'basicForm'=>$basicForm->createView(),
            'contactForm'=>$contactForm->createView(),
            'passwordForm'=>$form->createView(),
            'tab'=>'password',
            'id' => $id,
        ));

    }


}
