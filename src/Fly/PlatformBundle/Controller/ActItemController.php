<?php

namespace Fly\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fly\PlatformBundle\Entity\ActItem;
use Fly\PlatformBundle\Form\ActItemType;

/**
 * ActItem controller.
 *
 */
class ActItemController extends Controller
{

    /**
     * Lists all ActItem entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FlyPlatformBundle:ActItem')->findAll();

        return $this->render('FlyPlatformBundle:ActItem:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ActItem entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ActItem();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('actitem_show', array('id' => $entity->getId())));
        }

        return $this->render('FlyPlatformBundle:ActItem:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ActItem entity.
     *
     * @param ActItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ActItem $entity)
    {
        $form = $this->createForm(new ActItemType(), $entity, array(
            'action' => $this->generateUrl('actitem_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ActItem entity.
     *
     */
    public function newAction()
    {
        $entity = new ActItem();
        $form   = $this->createCreateForm($entity);

        return $this->render('FlyPlatformBundle:ActItem:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ActItem entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:ActItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ActItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyPlatformBundle:ActItem:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ActItem entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:ActItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ActItem entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyPlatformBundle:ActItem:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ActItem entity.
    *
    * @param ActItem $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ActItem $entity)
    {
        $form = $this->createForm(new ActItemType(), $entity, array(
            'action' => $this->generateUrl('actitem_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ActItem entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:ActItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ActItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('actitem_edit', array('id' => $id)));
        }

        return $this->render('FlyPlatformBundle:ActItem:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ActItem entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FlyPlatformBundle:ActItem')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ActItem entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('actitem'));
    }

    /**
     * Creates a form to delete a ActItem entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('actitem_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
