<?php

namespace Fly\UserBundle\Controller;


use Fly\PlatformBundle\Entity\Act;
use Fly\PlatformBundle\Entity\ActItem;
use Fly\PlatformBundle\Form\AccItemType;
use Fly\PlatformBundle\Form\AccType;
use Fly\PlatformBundle\Form\ActItemType;
use Fly\PlatformBundle\Form\ActType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



/**
 * Feed controller.
 *
 */
class PackageController extends Controller
{
    public function indexAction(Request $request)
    {

        // Accommodation Forms
        $accForm = $this->createForm(new AccType());
        $accItemForm = $this->createForm(new AccItemType());
        // Activities Form
        $act = new Act();
        $actItem = new ActItem();
        $act->addActItem($actItem);

        $actForm = $this->createForm(new ActType(),$act);

        return $this->render('FlyUserBundle:Package:index.html.twig', [
            'accForm'=>$accForm->createView(),
            'accItemForm'=>$accItemForm->createView(),
            'actForm' => $actForm->createView(),
        ]);
    }





}
