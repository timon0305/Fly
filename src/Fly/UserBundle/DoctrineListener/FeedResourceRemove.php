<?php
// src/Fly/PlatformBundle/DoctrineListener/ApplicationNotification.php

namespace Fly\UserBundle\DoctrineListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Fly\UserBundle\Entity\FeedResource;

class FeedResourceRemove
{

  public function postRemove(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();

    // On veut envoyer un email que pour les entités Application
    if (!$entity instanceof FeedResource) {
      return;
    }

    if($entity->getType() == 'uploadImage'){
        $filePath = $this->getWebPath().$entity->getImage();
        $folderPath = $this->getWebPath().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'feeds'.DIRECTORY_SEPARATOR.$entity->getFeed()->getGroup()->getId();
        @unlink($filePath);
        if($this->is_dir_empty($folderPath)){
            @rmdir($folderPath);
        }
    }

  }


  protected function getWebPath()
  {
      return __DIR__. '/../../../../web';
  }

  protected function is_dir_empty($dir) {
      if (!is_readable($dir)) return NULL;
      return (count(scandir($dir)) == 2);
  }
}