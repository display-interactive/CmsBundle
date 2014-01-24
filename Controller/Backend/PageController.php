<?php

namespace Display\CmsBundle\Controller\Backend;

use Display\CmsBundle\Cacher\PageCacher;
use Display\CmsBundle\Entity\PageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Display\CmsBundle\Entity\Page;
use Display\CmsBundle\Form\PageType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Page controller.
 */
class PageController extends Controller
{

    /**
     * Lists all Page entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DisplayCmsBundle:Page')->findAll();

        return $this->render('DisplayCmsBundle:Page:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     *
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PageRepository $repository */
        $repository = $em->getRepository('DisplayCmsBundle:Page');

        /** @var Page $entity */
        $entity = $repository->find($id);
        $request->setLocale($entity->getLocale());
        $menus = $em->getRepository('DisplayCmsBundle:Menu')->findByLocale($entity->getLocale());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        return $this->render('DisplayCmsBundle:Cms:page.html.twig', array(
            'entity' => $entity,
            'menus' => $menus,
        ));
    }

    /**
     * Displays a form to create a new Page entity.
     */
    public function newAction()
    {
        $entity = new Page();
        $entity->setLocale($this->container->getParameter('locale'));
        $form   = $this->createCreateForm($entity);

        return $this->render('DisplayCmsBundle:Page:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Page entity.
     */
    public function createAction(Request $request)
    {
        $entity = new Page();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $flashBag  */
            $flashBag = $this->get('session')->getFlashBag();

            $em = $this->getDoctrine()->getManager();

            try {
                $menus = $em->getRepository('DisplayCmsBundle:Menu')->findByLocale($entity->getLocale());
                $this->get('cms.cache')->warmup($entity, 'DisplayCmsBundle:Cms:page.html.twig', compact('menus'));
                $entity->setCachedAt(new \DateTime());
                $em->persist($entity);
                $em->flush();

                $flashBag->set('alert', array('alert-success' => 'Page created.'));

                return $this->redirect($this->generateUrl('display_cms_backend_page'));
            } catch (\Exception $e) {
                $line = $e->getLine();
                if ($e->getPrevious()) {
                    $e = $e->getPrevious();
                }
                $flashBag->set('alert', array('alert-danger' => 'There is an error in the template ('.$line.'): "' . $e->getMessage().'"'. '<br/><b>Change it</b>'));
            }
        }

        return $this->render('DisplayCmsBundle:Page:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Page entity.
    *
    * @param Page $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Page $entity)
    {
        $form = $this->createForm(new PageType(), $entity, array(
            'action' => $this->generateUrl('display_cms_backend_page_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Create',
            'attr' => array('class' => 'btn btn-default')
        ));

        return $form;
    }

    /**
     * Displays a form to edit an existing Page entity.
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Page $entity */
        $entity = $em->getRepository('DisplayCmsBundle:Page')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DisplayCmsBundle:Page:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Page entity.
    *
    * @param Page $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Page $entity)
    {
        $form = $this->createForm(new PageType(), $entity, array(
            'action' => $this->generateUrl('display_cms_backend_page_update', array('id' => $entity->getId())),
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array('class' => 'btn btn-default')));

        return $form;
    }
    /**
     * Edits an existing Page entity.
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Page $entity */
        $entity = $em->getRepository('DisplayCmsBundle:Page')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $flashBag  */
            $flashBag = $this->get('session')->getFlashBag();
            try {
                $menus = $em->getRepository('DisplayCmsBundle:Menu')->findByLocale($entity->getLocale());
                $this->get('cms.cache')->warmup($entity, 'DisplayCmsBundle:Cms:page.html.twig', compact('menus'));
                $entity->setCachedAt(new \DateTime());
                //$em->persist($entity);
                $em->flush();

                $flashBag->set('alert', array('alert-success' => 'Page updated.'));

                return $this->redirect($this->generateUrl('display_cms_backend_page'));
            } catch (\Exception $e) {
                $line = $e->getLine();
                if ($e->getPrevious()) {
                    $e = $e->getPrevious();
                }

                $flashBag->set('alert', array('alert-danger' => 'There is an error in the template ('.$line.'): "' . $e->getMessage().'"'. '<br/><b>Change it</b>'));
            }
        }

        return $this->render('DisplayCmsBundle:Page:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Page entity.
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var Page $entity */
            $entity = $em->getRepository('DisplayCmsBundle:Page')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Page entity.');
            }

            if ($entity->getChildren()->count() > 0) {
                throw new AccessDeniedException('You can\'t delete a page with children');
            }
            $this->get('cms.cache')->remove($entity);
            $em->remove($entity);
            $em->flush();


            /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $flashBag  */
            $flashBag = $this->get('session')->getFlashBag();
            $flashBag->set('alert', array('alert-success' => 'Page deleted.'));
        }

        return $this->redirect($this->generateUrl('display_cms_backend_page'));
    }

    /**
     * Cache a Page html.
     */
    public function cacheAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Page $entity */
        $entity = $em->getRepository('DisplayCmsBundle:Page')->find($id);
        $menus = $em->getRepository('DisplayCmsBundle:Menu')->findByLocale($entity->getLocale());

        $success = $this->get('cms.cache')->warmup($entity, 'DisplayCmsBundle:Cms:page.html.twig', compact('menus'));
        $entity->setCachedAt(new \DateTime());
        $em->flush();
        /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $flashBag  */
        $flashBag = $this->get('session')->getFlashBag();
        if ($success) {
            $flashBag->set('alert', array('alert-success' => 'Page cache created.'));
        } else {
            $flashBag->set('alert', array('alert-danger' => 'Page cache failed.'));
        }

        return $this->redirect($this->generateUrl('display_cms_backend_page'));
    }

    /**
     * Cache all page cache
     */
    public function cacheAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Page $entity */
        $entities = $em->getRepository('DisplayCmsBundle:Page')->findAll();
        $menus = array();
        foreach ($entities as $entity) {
            if (!isset($menus[$entity->getLocale()])) {
                $menus[$entity->getLocale()] = $em->getRepository('DisplayCmsBundle:Menu')->findByLocale($entity->getLocale());
            }
            $entity->setCachedAt(new \DateTime());
        }

        try {
            $success = $this->get('cms.cache')->warmupAll($entities, 'DisplayCmsBundle:Cms:page.html.twig', compact('menus'));
        } catch(\Exception $e) {
            $success = false;
        }
        $em->flush();

        /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $flashBag  */
        $flashBag = $this->get('session')->getFlashBag();
        if (!$success) {
            $flashBag->set('alert', array('alert-danger' => 'Cache generation failed.'));
        } else {
            $flashBag->set('alert', array('alert-success' => 'Cache generation succeed.'));
        }

        return $this->redirect($this->generateUrl('display_cms_backend_page'));
    }

    /**
     * Remove all Cache all page
     */
    public function removeAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Page $entity */
        $entities = $em->getRepository('DisplayCmsBundle:Page')->findAll();

        foreach ($entities as $entity) {
            $this->get('cms.cache')->remove($entity);
        }

        /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $flashBag  */
        $flashBag = $this->get('session')->getFlashBag();
        $flashBag->set('alert', array('alert-danger' => 'Cache removed.'));


        return $this->redirect($this->generateUrl('display_cms_backend_page'));
    }

    /**
     * Creates a form to delete a Page entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('display_cms_backend_page_delete', array('id' => $id)))
            ->add('submit', 'submit', array('label' => 'Delete', 'attr' => array('class' => 'btn btn-default')))
            ->getForm()
        ;
    }
}
