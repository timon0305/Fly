<?php

namespace Fly\PlatformBundle\Controller;

use Fly\PlatformBundle\Entity\ActItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fly\PlatformBundle\Entity\Act;
use Fly\PlatformBundle\Form\ActType;

/**
 * Act controller.
 *
 */
class ActController extends Controller
{

    /**
     * Lists all Act entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FlyPlatformBundle:Act')->findAll();

        return $this->render('FlyPlatformBundle:Act:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Act entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Act();
        $actItem = new ActItem();
        $actItem->setAct($entity);
        $entity->addActItem($actItem);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $act = $em->getRepository('FlyPlatformBundle:Act')->find($entity->getId());
            $actItems = $em->getRepository('FlyPlatformBundle:ActItem')->findBy(['act'=>$entity->getId()]);
//            var_dump($entity->getActItem());die();
            if($request->isXmlHttpRequest()){
                return JsonResponse::create(['asc'=>'success', 'data'=>(count($actItems))?$actItems[0]:null,'actItems'=>(count($actItems))?$actItems[0]:null]);
            }
            return $this->redirect($this->generateUrl('act_show', array('id' => $entity->getId())));
        }

//        var_dump( $form->createView());die();
        if($request->isXmlHttpRequest()){
            return JsonResponse::create(['asc'=>'error','msg'=>'Error']);
        }

        return $this->render('FlyPlatformBundle:Act:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Act entity.
     *
     * @param Act $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Act $entity)
    {
        $form = $this->createForm(new ActType(), $entity, array(
            'action' => $this->generateUrl('act_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Act entity.
     *
     */
    public function newAction()
    {
        $entity = new Act();
        $form   = $this->createCreateForm($entity);

        return $this->render('FlyPlatformBundle:Act:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Act entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:Act')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Act entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyPlatformBundle:Act:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Act entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:Act')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Act entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyPlatformBundle:Act:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Act entity.
    *
    * @param Act $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Act $entity)
    {
        $form = $this->createForm(new ActType(), $entity, array(
            'action' => $this->generateUrl('act_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Act entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:Act')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Act entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
//dump($editForm->getErrors(true));die;
        if ($editForm->isValid()) {
            $em->flush();

            $act = $em->getRepository('FlyPlatformBundle:Act')->find($entity->getId());
            $actItems = $em->getRepository('FlyPlatformBundle:ActItem')->findBy(['act'=>$entity->getId()]);

            if($request->isXmlHttpRequest()){
                return JsonResponse::create(['asc'=>'success', 'data'=>(count($actItems))?$actItems[0]:null,'actItems'=>(count($actItems))?$actItems[0]:null]);
            }

            return $this->redirect($this->generateUrl('act_edit', array('id' => $id)));
        }

        if($request->isXmlHttpRequest()){
            return JsonResponse::create(['asc'=>'error','msg'=>'Error']);
        }

        return $this->render('FlyPlatformBundle:Act:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Act entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FlyPlatformBundle:Act')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Act entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('act'));
    }

    /**
     * Creates a form to delete a Act entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('act_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
