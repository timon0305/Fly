<?php
namespace Fly\UserBundle\Service;

use Doctrine\ORM\EntityManager;
use Fly\UserBundle\Entity\Invitation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * Created by PhpStorm.
 * User: swaroge
 * Date: 02.05.15
 * Time: 13:10
 */
class InvitationSender
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
//        $this->templating->render()
//        dump($this->router->getContext()->getHost());die;
    }

    public function send($data)
    {
        if($data->getInvitation()){

            foreach($data->getInvitation() as $item){

                $this->sendEmail($item->getEmail(),$data->getName(), $item->getCode());

            }


        }


    }

    public function sendEmail($to,$groupName,$code){


        if($this->existUser($to)){
            $body = $this->templating->render(
                'FlyPlatformBundle:Emails:invitation.html.twig',
                array(
                    'groupname' => $groupName,
                    'invitationLink' => $this->router->getContext()->getHost().$this->router->generate('fos_user_group_show', array('groupName'=>$groupName,'code' => $code) )
                ));
        }else{
            $body = $this->templating->render(
                'FlyPlatformBundle:Emails:registration.html.twig',
                array(
                    'groupname' => $groupName,
                    'invitationLink' => $this->router->getContext()->getHost().$this->router->generate('fly_group_register_invited_user', array('code' => $code) )
                ));
        }



        $message = $this->mailer->createMessage()
            ->setSubject('You have Invitation to Registration!')
            ->setFrom('send@example.com')
            ->setTo($to)
            ->setBody($body,'text/html')
        ;
        $this->mailer->send($message);


    }

    protected function existUser($email)
    {
        return $this->em->getRepository('FlyUserBundle:User')->findOneByEmail($email);

    }
}