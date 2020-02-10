<?php

namespace Fly\UserBundle\Service;

use Doctrine\ORM\EntityManager;
use Fly\UserBundle\Entity\Group;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Fly\UserBundle\Form\Type\GroupFormType;


class StepForm
{

    private $em;
    private $formData;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;

    }

    public function createSteps($formData)
    {
        $this->formData = $formData;
    }

    public function getSteps()
    {
        $groupManager = $this->container->get('fos_user.group_manager');
        $group = $groupManager->createGroup('');
        $steps = [];
    }

    public function save()
    {

    }

}