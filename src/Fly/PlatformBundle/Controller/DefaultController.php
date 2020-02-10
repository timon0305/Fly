<?php

namespace Fly\PlatformBundle\Controller;

use Fly\PlatformBundle\Entity\FlyOrder;
use Fly\PlatformBundle\Form\FlyOrderType;
use Fly\UserBundle\Entity\Group;
use Fly\UserBundle\Entity\GroupInvitation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

class DefaultController extends Controller
{

    public function homeAction()
    {

//        $groupQuery = $this->get('doctrine.orm.entity_manager')->getRepository('FlyUserBundle:Group')->findGroupsWithInvites($this->getUser());
//        $paginator = $this->get('knp_paginator');
//        $pagination = $paginator->paginate(
//            $groupQuery,
//            $this->get('request')->query->get('page', 1)/*page number*/,
//            $this->container->getParameter('pagination_limit')/*limit per page*/
//        );
//        var_dump($_SERVER['HTTP_REFERER']);
//        var_dump($_SERVER['HTTP_X_FORWARDED_FOR']);
//        die;
        return $this->render('FlyPlatformBundle:Default:home.html.twig');
    }

    public function indexAction()
    {

        $groupQuery = $this->get('doctrine.orm.entity_manager')->getRepository('FlyUserBundle:Group')->findGroupsWithInvites($this->getUser());
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $groupQuery,
            $this->get('request')->query->get('page', 1)/*page number*/,
            $this->container->getParameter('pagination_limit')/*limit per page*/
        );
//        var_dump($_SERVER['HTTP_REFERER']);
//        var_dump($_SERVER['HTTP_X_FORWARDED_FOR']);
//        die;
        return $this->render('FlyPlatformBundle:Default:index.html.twig', ['pagination'=>$pagination]);
    }



    public function loginTemplateAction()
    {

        return $this->render('FlyPlatformBundle:Default:loginTemplate.html.twig');
    }
    
    public function privacyAction()
    {
        return $this->render('FlyPlatformBundle:Default:privacy.html.twig');
    }

    public function termsAction()
    {
        return $this->render('FlyPlatformBundle:Default:terms.html.twig');
    }

    public function flyAction(Request $request)
    {
        $session = $this->get('session');

        $paramsSearch = [];
        $session->set('lowFareSearch',$paramsSearch);
        $flyRes = [];

        $formErrors = false;
        $data = [];
        $form = $this->get('form.factory')->createNamedBuilder('LowFareSearch', 'form', $data, [])

            ->add('whereFromSelect', 'hidden')
            ->add('whereToSelect', 'hidden')
            ->add('searchStart', 'hidden')
            ->add('searchEnd', 'hidden')
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){


                $formData = $form->getData();

                $depAirp = $this->get('doctrine.orm.entity_manager')->getRepository('FlyPlatformBundle:Airport')->find($formData['whereFromSelect']);
                if(!$depAirp){
                    $form->addError(new FormError('Departure Airport no found'));
                    $formErrors = true;
                }
                $arrAirp = $this->get('doctrine.orm.entity_manager')->getRepository('FlyPlatformBundle:Airport')->find($formData['whereToSelect']);
                if(!$arrAirp){
                    $form->addError(new FormError('Arrival Airport no found'));
                    $formErrors = true;
                }

                if(!$formErrors){
//dump($depAirp);
                    $paramsSearch['departureAid'] = $depAirp->getCityCode();
                    $paramsSearch['arrivalAid'] = $arrAirp->getCityCode();
                    $paramsSearch['departureDate'] = $formData['searchStart'];
                    $paramsSearch['arrivalDate'] = $formData['searchEnd'];

                    $roundTrip = true;
                    $stopsAllowed =  true;

                    if(!$roundTrip){
                        $paramsSearch['roundTrip'] = false;
                    }
                    if(!$stopsAllowed){
                        $paramsSearch['stopsAllowed'] = false;
                    }

                    $session->set('lowFareSearch',$paramsSearch);

                    $flyRes = $this->get('fly_platform.fly')->lowFareSearch($paramsSearch);



                }

            }




//       $flyRes = [];

//       dump($flyRes);die;

        if($request->isXmlHttpRequest() ){
            $searchResRaw =  $this->renderView('@FlyPlatform/Fly/searchres.html.twig',['flyRes'=>$flyRes,'lowFareSearchData'=>$session->get('lowFareSearch')]);
            if(!$formErrors){
                return JsonResponse::create(['asc'=>'success','html'=>$searchResRaw]);
            }else{
                return JsonResponse::create(['asc'=>'error','formErrors'=>$formErrors]);
            }

        }


        return $this->render('@FlyPlatform/Fly/index.html.twig', [
           'flyRes'=>$flyRes,
            'form'=>$form->createView(),
            'formErrors'=>$formErrors,
        ]);
    }





    public function bookFlyAction(Request $request){

        $formData = $request->request->get('booking_form_quick');
        $form = $this->createForm(new FlyOrderType());
        $insurances = $this->get('doctrine.orm.entity_manager')->getRepository('FlyPlatformBundle:FlyInsurance')
            ->getArrayed();

        $form->handleRequest($request);

        if($form->isSubmitted()){
            dump($formData,$form);
        }
        if($form->isSubmitted() && $form->isValid()){

            // create order
            $em = $this->get('doctrine.orm.entity_manager');
            $order = $form->getData();
            dump($order);

            $outbound = $formData['outbound'];
            $inbound = $formData['inbound'];
            $priceStr = explode(' ',$formData['price']);
            $priceAmount = (float)$priceStr[0];
            $priceCurrency = $priceStr[1];


            $order->setInbound($inbound);
            $order->setOutbound($outbound);
            $order->setPrice($priceAmount);
            $order->setCurrency($priceCurrency);
            $order->setEmail($this->getUser()->getEmail());
            $order->setIsConfirmed(1);

            dump($order);
            //flush
            $em->persist($order);
            $em->flush();

            //send email


            //redirect

            return $this->redirectToRoute('fly_payment_confirm');

//            die('ok');
        }

        return $this->render('@FlyPlatform/Fly/payment.html.twig', [
            'formData' => $formData,
            'form'=> $form->createView(),
            'insurances'=>$insurances,
        ]);
    }

    public function bookFlyConfirmAction(Request $request){



        return $this->render('@FlyPlatform/Fly/payment_confirm.html.twig', [

        ]);
    }

}
