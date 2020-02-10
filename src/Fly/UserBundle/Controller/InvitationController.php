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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class InvitationController extends Controller
{
   public function unconfirmedAction()
   {
       $em = $this->get('doctrine.orm.entity_manager');
       $qb = $em->getRepository('FlyUserBundle:Invitation')->createQueryBuilder('i')
            ->where('i.email = :email')->setParameter('email',$this->getUser()->getEmail())
           ->andWhere('i.sent = 0')
           ;
       $entities = $qb->getQuery()->getResult();
//       $entities = $this->getUser()->getInvitation();
       return $this->render('FlyPlatformBundle:Invitation:unconfirmed.html.twig', array('entities' => $entities));
   }


}
