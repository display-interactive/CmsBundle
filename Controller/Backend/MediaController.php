<?php

namespace Display\CmsBundle\Controller\Backend;

use Display\CmsBundle\Entity\Media;
use Display\CmsBundle\Form\MediaType;
use Display\CmsBundle\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Media controller.
 *
 * @Route("/media")
 */
class MediaController extends Controller
{
    /**
     * Lists all Page entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DisplayCmsBundle:Media')->findAll();

        return $this->render('DisplayCmsBundle:Media:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Request $request)
    {
        $entity = new Media();
        $form = $this->createForm(new MediaType(), $entity, array(
            'action' => $this->generateUrl('display_cms_backend_media_upload')
        ));

        $form->add('submit', 'submit', array('label' => 'Upload', 'attr' => array('class' => 'btn btn-default')));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('display_cms_backend_media'));
        }

        return $this->render('DisplayCmsBundle:Media:upload.html.twig', array('form' => $form->createView()));
    }


    /**
     * Displays a form to edit an existing Page entity.
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DisplayCmsBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('DisplayCmsBundle:Media:upload.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing Page entity.
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Media $entity */
        $entity = $em->getRepository('DisplayCmsBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $form = $this->createEditForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
             $em->flush();

            return $this->redirect($this->generateUrl('display_cms_backend_media'));
        }

        return $this->render('DisplayCmsBundle:Media:upload.html.twig', array(
            'entity'      => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a form to edit a Page entity.
     *
     * @param Media $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Media $entity)
    {
        $form = $this->createForm(new MediaType(), $entity, array(
            'action' => $this->generateUrl('display_cms_backend_media_update', array('id' => $entity->getId())),
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array('class' => 'btn btn-default')));

        return $form;
    }

    /**
     * Deletes a Page entity.
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('DisplayCmsBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $em->remove($entity);
        $em->flush();


        return $this->redirect($this->generateUrl('display_cms_backend_media'));
    }
}
