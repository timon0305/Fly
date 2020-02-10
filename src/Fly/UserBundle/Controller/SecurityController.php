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
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;


class SecurityController extends Controller
{   
    public function loginAction(Request $request)
    {
	 /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
	$session = $request->getSession();
//$this->addFlash('error','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi sed aliquet quam. Suspendisse nec nisl mauris.');
//$this->addFlash('notice','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi sed aliquet quam. Suspendisse nec nisl mauris.');
	/* fetch social networking login url */
//        $twitterService = $this->get('twitter_service');
//        $twitter_url = $twitterService->getLoginUrl($this->generateUrl('fly_twitter_callback_url',array(),true));
//
//        $facebookService = $this->get('fly.user.facebook.service');
//        $facebookUrl = $facebookService->getLoginUrl($this->generateUrl('fly_facebook_callback_url',array(), true));
        
        
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
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);
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
        $registerForm = $formFactory->createForm();
        
        return $this->renderLogin(array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'csrf_token' => $csrfToken,
            'registerForm'=>$registerForm->createView(),
//            'fb_login_url' =>$facebookUrl,
//            'twitter_url' => $twitter_url
        ));
    }
    
    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        return $this->render('FOSUserBundle:Security:login.html.twig', $data);
    }
    
    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }
    
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    public function facebookLoginBackAction() 
    {

        $facebookService = $this->get('fly.user.facebook.service');
        $userInfo = $facebookService->getUserInfo();
        
        $url = $this->generateUrl('fos_user_profile_show');
        
        return $userService = $this->get('fly.user.service')->createUserByFacebook($userInfo, $url);
    }
    
    public function twitterLoginBackAction(Request $request)
    {
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

			$url = $this->generateUrl('fos_user_profile_show');
			
			return  $twitterUserService->createUserByTwitter($email,$username,$name[0],$name[1],$twitterId, $url);
	    	}
	  }
    }

    public function twitterLoginCheckAction(Request $request)
    {
        die('twitterLoginCheckAction');
    }
}