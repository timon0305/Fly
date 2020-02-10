<?php
namespace Fly\UserBundle\Service;

use Doctrine\ORM\EntityManager;
use Fly\UserBundle\Entity\Invitation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * Created by PhpStorm.
 * User: swaroge
 * Date: 02.05.15
 * Time: 13:10
 */
class RegisterService
{
    private $mailer;
    private $em;
    private $router;
    private $templating;
    private $message = "You have been invited to join a group %s in Fly, please click on the link : %s. Invitation Code: %s";

    public function __construct($mailer, Router $router, EntityManager $em, TwigEngine $templating)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
    }

    public function sendRegisterationEmail($userEntity, $pwd)
    {
        if ($userEntity->getEmail()=='') {
            return false;
        }
	
	$resetPasswordUrl = $this->router->generate('fos_user_resetting_reset', array('token' => $userEntity->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);
	
	
	$body = $this->templating->render(
	'FlyPlatformBundle:Emails:signUp.html.twig',
	array('user'=>$userEntity, 'pwd'=>$pwd, 'resetPasswordUrl'=>$resetPasswordUrl));
        $message = $this->mailer->createMessage()
            ->setSubject('You have successfully registered on Fly Platform')
            ->setFrom('noreply@fly.com')
            ->setTo($userEntity->getEmail())
            ->setBody($body,'text/html')
        ;
        $this->mailer->send($message);
    }
    
    public function generateRandomPassword() 
    {
	    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 8; $i++) {
		    $n = rand(0, $alphaLength);
		    $pass[] = $alphabet[$n];
	    }
	    
	    return implode($pass); //turn the array into a string
    }
}