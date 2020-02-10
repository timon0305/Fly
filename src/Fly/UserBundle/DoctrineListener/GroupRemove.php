<?php

namespace Fly\UserBundle\DoctrineListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Fly\UserBundle\Entity\Group;

class GroupRemove
{

  public function postRemove(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();

    // On veut envoyer un email que pour les entités Application
    if (!$entity instanceof Group) {
      return;
    }

    if($entity->getPicture()){
        $filePath = $this->getWebPath().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$entity->getPicture();
        @unlink($filePath);
    }

  }


  protected function getWebPath()
  {
      return __DIR__. '/../../../../web';
  }

}