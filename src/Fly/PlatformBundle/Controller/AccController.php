<?php

namespace Fly\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fly\PlatformBundle\Entity\Acc;
use Fly\PlatformBundle\Form\AccType;

/**
 * Acc controller.
 *
 */
class AccController extends Controller
{

    /**
     * Lists all Acc entities.
     *
     */
    public function indexAction()
    {

        $qb = $this->get('doctrine.orm.entity_manager')->getRepository('FlyPlatformBundle:Acc')->createQueryBuilder('acc');

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $this->get('request')->query->get('page', 1)/*page number*/, 15
//            $this->container->getParameter('pagination_limit')/*limit per page*/
        );
//dump($pagination->getItems());die;
        if($this->get('request')->isXmlHttpRequest()){
            return JsonResponse::create(['asc'=>'success','acc'=>$pagination->getItems(), 'next'=>$pagination->next(), 'page'=>$this->get('request')->query->get('page', 1)]);
        }else{
            return $this->render('FlyPlatformBundle:Acc:index.html.twig', array(
                'pagination' => $pagination,
            ));
        }


    }
    /**
     * Creates a new Acc entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Acc();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /*Upload file*/
            $fileBadge = $request->files->get('fly_platformbundle_acc');
            $res = $this->get('fly.local.uploader')->uploadAccImage($fileBadge['picture']);
            if ($res['asc'] == 'error') {
                return JsonResponse::create(['asc' => 'error', 'message' => $res['msg']]);
            }
            $entity->setPicture($res['url']);

            $em->persist($entity);
            $em->flush();


            if($request->isXmlHttpRequest()){
                return JsonResponse::create(['asc'=>'success','data'=>$entity]);
            }

            return $this->redirect($this->generateUrl('acc_show', array('id' => $entity->getId())));
        }

        if($request->isXmlHttpRequest()){
            return JsonResponse::create(['asc'=>'error','msg'=>'Error']);
        }

        return $this->render('FlyPlatformBundle:Acc:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Acc entity.
     *
     * @param Acc $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Acc $entity)
    {
        $form = $this->createForm(new AccType(), $entity, array(
            'action' => $this->generateUrl('acc_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Acc entity.
     *
     */
    public function newAction()
    {
        $entity = new Acc();
        $form   = $this->createCreateForm($entity);

        return $this->render('FlyPlatformBundle:Acc:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Acc entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:Acc')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Acc entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyPlatformBundle:Acc:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Acc entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:Acc')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Acc entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyPlatformBundle:Acc:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Acc entity.
    *
    * @param Acc $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Acc $entity)
    {
        $form = $this->createForm(new AccType(), $entity, array(
            'action' => $this->generateUrl('acc_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Acc entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:Acc')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Acc entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('acc_edit', array('id' => $id)));
        }

        return $this->render('FlyPlatformBundle:Acc:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Acc entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FlyPlatformBundle:Acc')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Acc entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('acc'));
    }

    /**
     * Creates a form to delete a Acc entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('acc_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
