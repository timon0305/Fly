<?php

namespace Fly\UserBundle\Service;

use Fly\UserBundle\Entity\Group;
use Fly\UserBundle\FlyUserBundle;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Fly\UserBundle\Entity\User;
use Fly\UserBundle\Entity\Notification;

class NotificationService
{


    private $em;
    private $router;
    private $noticeTypes;


    public function __construct(Router $router, EntityManager $em)
    {
        $this->router = $router;
        $this->em = $em;
        $this->noticeTypes = FlyUserBundle::NoticeTypeNames();
    }

    public function addNotice(User $user, User $sender = null,  $type, Group $group = null,  $params = [])
    {
        $entity = new Notification();
        $entity->setType($type['name']);
        $entity->setUser($user);
        if($sender){
            $entity->setSender($sender);
        }
        if($group){
            $entity->setGroup($group);
        }
        $this->em->persist($entity);
        $this->em->flush();


    }

   public function setIsRead(Notification $entity)
   {
       $entity->setIsRead(true);
       $this->em->persist($entity);
       $this->em->flush();
   }

    public function getUserNotifications(User $user)
    {
        $new = $this->em->getRepository('FlyUserBundle:Notification')->findBy(['is_read'=>0, 'user'=>$user]);
        $lastNotices = $this->em->getRepository('FlyUserBundle:Notification')
            ->findBy(['user'=>$user],['created'=>'DESC'],5);
        return ['newNoticesCount'=>count($new),'lastNotices'=>$lastNotices];
    }


    public function removeGroupNotice($user, $group)
    {

        $notice = $this->em->getRepository('FlyUserBundle:Notification')
            ->findOneBy(['user'=>$user,'group'=>$group]);
        if($notice){
            $this->em->remove($notice);
            $this->em->flush();

            return true;
        }

        return false;
    }

    public function removeFriendNotice($user, $sender)
    {

        $notice = $this->em->getRepository('FlyUserBundle:Notification')
            ->findOneBy(['user'=>$user,'sender'=>$sender]);
        if($notice){
            $this->em->remove($notice);
            $this->em->flush();

            return true;
        }

        return false;
    }





}