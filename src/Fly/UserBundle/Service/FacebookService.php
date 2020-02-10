<?php
namespace Fly\UserBundle\Service;

use Doctrine\ORM\EntityManager;
use Fly\UserBundle\Entity\Invitation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Created by PhpStorm.
 * User: swaroge
 * Date: 02.05.15
 * Time: 13:10
 */
class FacebookService
{
    private $mailer;
    private $em;
    private $router;
    private $templating;

    public function __construct($mailer, Router $router, EntityManager $em, TwigEngine $templating)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
    }
    
    public function getLoginUrl($callback_url) {


	$fb = new \Facebook\Facebook([
	  'app_id' => '1656032164658444',
	  'app_secret' => 'f5eb0d25e8c27fcae24a63754f053a5a',
	  'default_graph_version' => 'v2.4',
	  //'app_id' => '968769936539052',
	 // 'app_secret' => 'b197e32cfd0dc1867b44b7336a7229f1',
	  //'default_graph_version' => 'v2.5',
	]);
	
	$helper = $fb->getRedirectLoginHelper();
	$permissions = ['email','user_birthday','user_relationships'];
		  
	try {
	    $accessToken = $helper->getAccessToken();
		
	} catch(\Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(\Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	
	return $loginUrl = $helper->getLoginUrl($callback_url, $permissions);
    }
    
    public function getUserInfo() {
    
    
	$fb = new \Facebook\Facebook([
	  'app_id' => '1656032164658444',
	  'app_secret' => 'f5eb0d25e8c27fcae24a63754f053a5a',
	  'default_graph_version' => 'v2.4',
	 // 'app_id' => '968769936539052',
	  //'app_secret' => 'b197e32cfd0dc1867b44b7336a7229f1',
	  //'default_graph_version' => 'v2.5',
	]);
	
	$helper = $fb->getRedirectLoginHelper();
	$permissions = ['email'];
		  
	try {
	    $accessToken = $helper->getAccessToken();
		
	} catch(\Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(\Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}

	if (isset($accessToken)) {
	
		  if (isset($_SESSION['facebook_access_token'])) {
			  $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
		  } else {
			  // getting short-lived access token
			  $_SESSION['facebook_access_token'] = (string) $accessToken;
			  // OAuth 2.0 client handler
			  $oAuth2Client = $fb->getOAuth2Client();
			  // Exchanges a short-lived access token for a long-lived one
			  $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
			  $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
			  // setting default access token to be used in script
			  $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
		  }
		  // getting basic info about user
		  try {
			  $profile_request = $fb->get('/me?fields=id,name,first_name,last_name,email,gender,birthday,relationship_status',$accessToken);
			  $profile = $profile_request->getGraphNode()->asArray();
		  } catch(\Facebook\Exceptions\FacebookResponseException $e) {
			  // When Graph returns an error
			  echo 'Graph returned an error: ' . $e->getMessage();
			  session_destroy();
			  // redirecting user back to app login page
			  header("Location: ");
			  exit;
		  } catch(\Facebook\Exceptions\FacebookSDKException $e) {
			  // When validation fails or other local issues
			  echo 'Facebook SDK returned an error: ' . $e->getMessage();
			  exit;
		  }
		  
		  return $profile;

		  
		 // return $this->registerUser($email, $firstName, $lastName, $socialId, $request);
		  // Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
	} else {
		  // replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
		  $loginUrl = $helper->getLoginUrl($this->generateUrl('fos_user_security_login',array(), true), $permissions);
	}
    }
}