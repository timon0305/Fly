<?php

namespace Fly\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Fly controller.
 *
 */
class FlyController extends Controller
{

    /**
     * Lists all Acc entities.
     *
     */
    public function searchAction(Request $request)
    {



        return $this->render('@FlyPlatform/Fly/search.html.twig', [

        ]);


    }
    /**
     * Creates a new Acc entity.
     *
     */
    public function showAction(Request $request)
    {

    }


}
