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

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Controller managing the registration
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class RegistrationController extends Controller
{
    
    public function registerAction(Request $request)
    {
        throw new NotFoundHttpException('The Registration is no more availible');
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = null;

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
            : null;


        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);
	
	
        /*check invite code*/
        if($request->get('code')){
            
            $invitation = $this->get('doctrine.orm.entity_manager')->getRepository('FlyUserBundle:GroupInvitation')->findOneByCode($request->get('code'));
            
            if($invitation){
                $user->setUsername($invitation->getEmail());
                $user->setEmail($invitation->getEmail());
            }
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);
        $userAlreadyExists = false;
        
        //$validator
        if ($form->isValid()) {
        
	    $userEntity = $this->get('doctrine.orm.entity_manager')->getRepository('FlyUserBundle:User')->findOneByEmail($user->getEmail());
	    
	    if ($userEntity) {
		 $userAlreadyExists = true;
	    } else {
		    /* call register service */
		    $registerService = $this->get('fly.user.register.service');
		    $pwd = $registerService->generateRandomPassword();
		    
		    $event = new FormEvent($form, $request);
		    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
		    $user->setUsername($user->getEmail());
		    $user->setPlainPassword($pwd);
		    $userManager->updateUser($user);

		    /*check invites*/
		    $em = $this->get('doctrine.orm.entity_manager');
		    $invites = $em->getRepository('FlyUserBundle:GroupInvitation')->findByEmail($user->getEmail());
		    foreach($invites as $inv){
                if(!$inv->getUser()){
                    $inv->setUser($user);
                    $em->persist($inv);
                    $em->flush();
                }
		    }


		    if (null === $response = $event->getResponse()) {
						
			/* generating password reset code */
			if (null === $user->getConfirmationToken()) {
			    
			    /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
			    $tokenGenerator = $this->get('fos_user.util.token_generator');
			    $user->setConfirmationToken($tokenGenerator->generateToken());
			}

			//$this->get('fos_user.mailer')->sendResettingEmailMessage($user);
			$user->setPasswordRequestedAt(new \DateTime());
			$this->get('fos_user.user_manager')->updateUser($user);
			
			/* sending registration email */
			$registerService->sendRegisterationEmail($user, $pwd);
			
			$url = $this->generateUrl('fos_user_profile_show');
			$response = new RedirectResponse($url);
		    }

		    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

		    return $response;
	    }
        
        }


	/* fetch social networking login url */
        $twitterService = $this->get('twitter_service');
        $twitter_url = $twitterService->getLoginUrl($this->generateUrl('fly_twitter_register_url',array(),true));
        
        $facebookService = $this->get('fly.user.facebook.service');
        $facebookUrl = $facebookService->getLoginUrl($this->generateUrl('fly_facebook_register_url',array(), true));
        
        
        return $this->render('FOSUserBundle:Registration:register.html.twig', array(
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error'         => $error,
            'csrf_token' => $csrfToken,
            'userAlreadyExists'=>$userAlreadyExists,
	        'fb_login_url' =>$facebookUrl,
            'twitter_url' => $twitter_url
        ));
    }

    public function facebookRegisterAction() 
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $facebookService = $this->get('fly.user.facebook.service');
        $userInfo = $facebookService->getUserInfo();
        
        $user = $em->getRepository('FlyUserBundle:User')->findOneBy(array('facebookId'=>$userInfo['id']));
        
        /* if user already registered redirect to login page */
        if ($user) {
		
    		$this->addFlash('error','You facebook account is already registered with us, please login with facebook');
    		
    		return $this->redirect($this->generateUrl('fos_user_security_login'));

        }
        
        $this->addFlash('success-msg','You have successfully registered via facebook with us');
        
        return $userService = $this->get('fly.user.service')->createUserByFacebook($userInfo);

    }
    
    public function twitterRegisterAction(Request $request)
    {
	$em = $this->get('doctrine.orm.entity_manager');
    	
    	$session = $request->getSession();

        if (isset($_REQUEST['oauth_token']) && $_SESSION['token'] == $_REQUEST['oauth_token']) {
		
		$twitterService = $this->get('twitter_service');
		$twitterUserService = $this->get('fly.user.service');

	        $twitter_userInfo = $twitterService->getUserInfo();

	        
	        if ($twitter_userInfo) {
	        	
	        	$email = '';
	        	$username = $twitter_userInfo->screen_name;
	        	$name = $twitter_userInfo->name;
	       		$name = explode(' ', $name);
	        	$twitterId = $twitter_userInfo->id;	

			$user = $em->getRepository('FlyUserBundle:User')->findOneBy(array('twitterId'=>$twitterId));
        
			/* if user already registered redirect to login page */
			if ($user) {
				  
				  $this->addFlash('error','You twitter account is already registered with us, please login with twitter');
				  
				  return $this->redirect($this->generateUrl('fos_user_security_login'));
			}
			
			$this->addFlash('success-msg','You have successfully registered via facebook with us');
			  
			return  $twitterUserService->createUserByTwitter($email,$username,$name[0],$name[1],$twitterId);
	    	}
	  }
    }
    
    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');
        $this->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }

        return $this->render('FOSUserBundle:Registration:checkEmail.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction(Request $request, $token)
    {
//        die('0k');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            return $this->redirectToRoute('fly_platform_home');
//            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $userPassword = $this->generatePassword();
        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $user->setPlainPassword($userPassword);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        $this->sendEmail($user,$userPassword);



        // Here, "public" is the name of the firewall in your security.yml
        $token = new UsernamePasswordToken($user, $user->getPassword(), "public", $user->getRoles());
        // For older versions of Symfony, use security.context here
        $this->get("security.token_storage")->setToken($token);
        // Fire the login event
        // Logging the user in above the way we do it doesn't do this automatically
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);


        return $this->redirectToRoute('fos_user_profile_show');

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $this->redirectToRoute('fos_user_profile_show');

        return $response;
    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('FOSUserBundle:Registration:confirmed.html.twig', array(
            'user' => $user,
        ));
    }

    protected function generatePassword($length=8)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;
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
