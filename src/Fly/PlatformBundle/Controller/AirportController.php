<?php

namespace Fly\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fly\PlatformBundle\Entity\Airport;
use Fly\PlatformBundle\Form\AirportType;
use Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;

/**
 * Airport controller.
 *
 */
class AirportController extends Controller
{

    /**
     * Lists all Airport entities.
     *
     */
    public function indexAction()
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new ForbiddenOverwriteException('Access Denied');
        }

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FlyPlatformBundle:Airport')->findAll();

        return $this->render('FlyPlatformBundle:Airport:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Airport entity.
     *
     */
    public function createAction(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new ForbiddenOverwriteException('Access Denied');
        }
        $entity = new Airport();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('airport', array('id' => $entity->getId())));
        }

        return $this->render('FlyPlatformBundle:Airport:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Airport entity.
     *
     * @param Airport $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Airport $entity)
    {
        $form = $this->createForm(new AirportType(), $entity, array(
            'action' => $this->generateUrl('airport_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Airport entity.
     *
     */
    public function newAction()
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new ForbiddenOverwriteException('Access Denied');
        }
        $entity = new Airport();
        $form   = $this->createCreateForm($entity);

        return $this->render('FlyPlatformBundle:Airport:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Airport entity.
     *
     */
    public function showAction($id)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new ForbiddenOverwriteException('Access Denied');
        }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:Airport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Airport entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyPlatformBundle:Airport:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Airport entity.
     *
     */
    public function editAction($id)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new ForbiddenOverwriteException('Access Denied');
        }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:Airport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Airport entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyPlatformBundle:Airport:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Airport entity.
    *
    * @param Airport $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Airport $entity)
    {
        $form = $this->createForm(new AirportType(), $entity, array(
            'action' => $this->generateUrl('airport_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Airport entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new ForbiddenOverwriteException('Access Denied');
        }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:Airport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Airport entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('airport_edit', array('id' => $id)));
        }

        return $this->render('FlyPlatformBundle:Airport:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Airport entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new ForbiddenOverwriteException('Access Denied');
        }
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FlyPlatformBundle:Airport')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Airport entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('airport'));
    }

    /**
     * Creates a form to delete a Airport entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('airport_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
