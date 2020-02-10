<?php

namespace Fly\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Fly\PlatformBundle\Entity\AccItem;
use Fly\PlatformBundle\Form\AccItemType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * AccItem controller.
 *
 */
class AccItemController extends Controller
{

    /**
     * Lists all AccItem entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FlyPlatformBundle:AccItem')->getItemsByMonth();
        if($request->isXmlHttpRequest()){
            return JsonResponse::create(['asc'=>'success','data'=>$entities]);
        }


        return $this->render('FlyPlatformBundle:AccItem:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lists all AccItem entities.
     *
     */
    public function calendarItemsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FlyPlatformBundle:AccItem')->getItemsByMonth();

        return JsonResponse::create(['asc'=>'success','data'=>$entities]);
    }

    /**
     * Creates a new AccItem entity.
     *
     */
    public function createAction(Request $request)
    {

        $entity = new AccItem();


        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            if($request->isXmlHttpRequest()){
                return JsonResponse::create(['asc'=>'success','data'=>$entity]);
            }
            return $this->redirect($this->generateUrl('accitem_show', array('id' => $entity->getId())));
        }

        if($request->isXmlHttpRequest()){
            return JsonResponse::create(['asc'=>'error','msg'=>'Error']);
        }
        return $this->render('FlyPlatformBundle:AccItem:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccItem entity.
     *
     * @param AccItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccItem $entity)
    {
        $form = $this->createForm(new AccItemType(), $entity, array(
            'action' => $this->generateUrl('accitem_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new AccItem entity.
     *
     */
    public function newAction()
    {
        $entity = new AccItem();
        $form   = $this->createCreateForm($entity);

        return $this->render('FlyPlatformBundle:AccItem:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccItem entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:AccItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyPlatformBundle:AccItem:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AccItem entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:AccItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccItem entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyPlatformBundle:AccItem:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AccItem entity.
    *
    * @param AccItem $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AccItem $entity)
    {
        $form = $this->createForm(new AccItemType(), $entity, array(
            'action' => $this->generateUrl('accitem_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing AccItem entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyPlatformBundle:AccItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccItem entity.');
        }

//        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            if($request->isXmlHttpRequest()){
                return JsonResponse::create(['asc'=>'success','data'=>$entity]);
            }

            return $this->redirect($this->generateUrl('accitem_edit', array('id' => $id)));
        }

        return $this->render('FlyPlatformBundle:AccItem:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a AccItem entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FlyPlatformBundle:AccItem')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AccItem entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('accitem'));
    }

    /**
     * Creates a form to delete a AccItem entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('accitem_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
