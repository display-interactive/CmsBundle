<?php

namespace Display\CmsBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Display\CmsBundle\Entity\Fragment;
use Display\CmsBundle\Form\FragmentType;

/**
 * Fragment controller.
 */
class FragmentController extends Controller
{

    /**
     * Lists all Fragment entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DisplayCmsBundle:Fragment')->findAll();

        return $this->render('DisplayCmsBundle:Fragment:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Fragment entity.
     */
    public function createAction(Request $request)
    {
        $entity = new Fragment();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $flashBag  */
            $flashBag = $this->get('session')->getFlashBag();
            $flashBag->set('alert', array('alert-success' => 'Fragment created.'));

            return $this->redirect($this->generateUrl('display_cms_backend_fragment'));
        }

        return $this->render('DisplayCmsBundle:Fragment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Fragment entity.
    *
    * @param Fragment $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Fragment $entity)
    {
        $form = $this->createForm(new FragmentType(), $entity, array(
            'action' => $this->generateUrl('display_cms_backend_fragment_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create', 'attr' => array('class' => 'btn btn-default')));

        return $form;
    }

    /**
     * Displays a form to create a new Fragment entity.
     */
    public function newAction()
    {
        $entity = new Fragment();
        $form   = $this->createCreateForm($entity);

        return $this->render('DisplayCmsBundle:Fragment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Fragment entity.
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DisplayCmsBundle:Fragment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fragment entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DisplayCmsBundle:Fragment:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Fragment entity.
    *
    * @param Fragment $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Fragment $entity)
    {
        $form = $this->createForm(new FragmentType(), $entity, array(
            'action' => $this->generateUrl('display_cms_backend_fragment_update', array('id' => $entity->getId())),
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array('class' => 'btn btn-default')));

        return $form;
    }
    /**
     * Edits an existing Fragment entity.
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DisplayCmsBundle:Fragment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fragment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $flashBag  */
            $flashBag = $this->get('session')->getFlashBag();
            $flashBag->set('alert', array('alert-success' => 'Fragment updated.'));

            return $this->redirect($this->generateUrl('display_cms_backend_fragment'));
        }

        return $this->render('DisplayCmsBundle:Fragment:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Fragment entity.
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DisplayCmsBundle:Fragment')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Fragment entity.');
            }

            $em->remove($entity);
            $em->flush();

            /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $flashBag  */
            $flashBag = $this->get('session')->getFlashBag();
            $flashBag->set('alert', array('alert-success' => 'Fragment deleted.'));
        }

        return $this->redirect($this->generateUrl('display_cms_backend_fragment'));
    }

    /**
     * Creates a form to delete a Fragment entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('display_cms_backend_fragment_delete', array('id' => $id)))
            ->add('submit', 'submit', array('label' => 'Delete', 'attr' => array('class' => 'btn btn-default')))
            ->getForm()
        ;
    }
}
