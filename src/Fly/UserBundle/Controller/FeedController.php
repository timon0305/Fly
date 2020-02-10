<?php

namespace Fly\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fly\UserBundle\Entity\Feed;
use Fly\UserBundle\Form\Type\FeedType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Feed controller.
 *
 */
class FeedController extends Controller
{

    /**
     * Lists all Feed entities.
     *
     */
    public function indexAction($groupName)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FlyUserBundle:Feed')->findAll();

        return $this->render('FlyUserBundle:Feed:index.html.twig', array(
            'entities' => $entities,
            'groupName'=>$groupName
        ));
    }
    /**
     * Creates a new Feed entity.
     *
     */
    public function createAction(Request $request, $groupName)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $group = $em->getRepository('FlyUserBundle:Group')->findOneByName($groupName);
        if(!$group){
            $this->addFlash('error', 'Server error: Group not found');
            return $this->redirect($this->generateUrl('fos_user_group_show', array('groupName' => $groupName)));
        }
        $entity = new Feed();
        $entity->setGroup($group);
        $entity->setUser($this->getUser());

        $form = $this->createCreateForm($entity,$groupName);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('fos_user_group_show', array('groupName' => $groupName)));
        }

        return $this->render('FlyUserBundle:Feed:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }



    /**
     * Creates a form to create a Feed entity.
     *
     * @param Feed $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Feed $entity, $groupName)
    {
        $form = $this->createForm(new FeedType(), $entity, array(
            'action' => $this->generateUrl('group_feed_create',['groupName'=>$groupName]),
            'method' => 'POST',
        ));

//        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Feed entity.
     *
     */
    public function newAction($groupName)
    {
        throw new AccessDeniedException();

        $em = $this->get('doctrine.orm.entity_manager');
        $group = $em->getRepository('FlyUserBundle:Group')->findOneByName($groupName);
        if(!$group){
            $this->addFlash('error', 'Server error: Group not found');
            return $this->redirect($this->generateUrl('fos_user_group_show', array('groupName' => $groupName)));
        }
        $entity = new Feed();
        $entity->setGroup($group);
        $entity->setUser($this->getUser());
        $form   = $this->createCreateForm($entity,$groupName);

        return $this->render('FlyUserBundle:Feed:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'group' => $group,
        ));
    }

    /**
     * Finds and displays a Feed entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyUserBundle:Feed')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feed entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyUserBundle:Feed:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Feed entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyUserBundle:Feed')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feed entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyUserBundle:Feed:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Feed entity.
    *
    * @param Feed $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Feed $entity)
    {
        $form = $this->createForm(new FeedType(), $entity, array(
            'action' => $this->generateUrl('group_feed_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Feed entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyUserBundle:Feed')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feed entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('group_feed_edit', array('id' => $id)));
        }

        return $this->render('FlyUserBundle:Feed:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Feed entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FlyUserBundle:Feed')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Feed entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('group_feed'));
    }

    /**
     * Creates a form to delete a Feed entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('group_feed_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
