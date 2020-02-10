<?php
namespace Fly\UserBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\UserEvent;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Event\FilterUserResponseEvent;

/**
 * Created by PhpStorm.
 * User: swaroge
 * Date: 02.05.15
 * Time: 13:10
 */
class UserService
{
    private $router;
    private $em;



    public function __construct( Router $router, EntityManager $em)
    {
        $this->router = $router;
        $this->em = $em;

    }



    public function setFriendsStatus($friendOne, $friendTwo, $status)
    {
        $friendObj = $this->em->getRepository('FlyUserBundle:Friends')->getFriendObject($friendOne,$friendTwo);


    }

}