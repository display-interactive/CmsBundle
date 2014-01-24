<?php

namespace Display\CmsBundle\Controller\Backend;

use Display\CmsBundle\Entity\Menu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Display\CmsBundle\Form\MenuType;

/**
 * Menu controller.
 */
class MenuController extends Controller
{
    /**
     * Lists all Page entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('DisplayCmsBundle:Menu')->getList();

        return $this->render('DisplayCmsBundle:Menu:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Displays a form to create a new Menu
     */
    public function newAction($locale)
    {
        if (null === $locale) {
            $locale = $this->container->getParameter('locale');
        }

        $em = $this->getDoctrine()->getManager();
        $pages = $em->getRepository('DisplayCmsBundle:Page')->findByLocale($locale);

        $form = $this->createMenuForm(compact('locale'));

        return $this->render('DisplayCmsBundle:Menu:new.html.twig', array(
            'menus' => array(),
            'pages' => $pages,
            'form' => $form->createView()
        ));
    }

    /**
     * Displays a form to create a new Menu
     */
    public function createAction(Request $request)
    {
        $form = $this->createMenuForm();
        $form->handleRequest($request);

        /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $flashBag  */
        $flashBag = $this->get('session')->getFlashBag();

        $data = $form->getData();
        $menus = $this->buildMenus(json_decode($data['menus'], true));
        $em = $this->getDoctrine()->getManager();
        if (count($menus) > 0) {
            $oldMenus = $em->getRepository('DisplayCmsBundle:Menu')->findByLocale($data['locale']);
            /** @var Menu $oldMenu */
            foreach ($oldMenus as $oldMenu) {
                $em->remove($oldMenu);
            }
            $em->flush();

            foreach ($menus as $menu) {
                $em->persist($menu);
            }
            $em->flush();

            $flashBag->set('alert', array('alert-success' => 'Menu saved.'));
            return $this->redirect($this->generateUrl('display_cms_backend_menu'));
        }

        $flashBag->set('alert', array('alert-danger' => 'You have to specify items'));
        $pages = $em->getRepository('DisplayCmsBundle:Page')->findForMenu($data['locale'], $this->getIds($menus));

        return $this->render('DisplayCmsBundle:Menu:new.html.twig', array(
            'menus' => $menus,
            'pages' => $pages,
            'form' => $form->createView()
        ));
    }


    /**
     * Displays a form to edit menu
     */
    public function editAction($locale)
    {
        if (null === $locale) {
            $locale = $this->container->getParameter('locale');
        }

        $em = $this->getDoctrine()->getManager();

        $menus = $em->getRepository('DisplayCmsBundle:Menu')->findByLocale($locale);
        $pages = $em->getRepository('DisplayCmsBundle:Page')->findByLocale($locale, $this->getIds($menus));


        $form = $this->createMenuForm(array('locale' => $locale, 'menus' => json_encode($this->buildMenuArray($menus))), 'Update');

        return $this->render('DisplayCmsBundle:Menu:new.html.twig', array(
            'menus' => $menus,
            'pages' => $pages,
            'form' => $form->createView()
        ));
    }

    /**
     * @param array $data
     * @param string $label
     * @return \Symfony\Component\Form\Form
     */
    private function createMenuForm($data = array(), $label = 'Create')
    {
        $form = $this->createForm(new MenuType(), $data, array(
            'action' => $this->generateUrl('display_cms_backend_menu_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => $label,
            'attr' => array('class' => 'btn btn-default')
        ));

        return $form;
    }

    /**
     * @param array $data
     * @param Menu $parent
     * @return array
     */
    private function buildMenus(array $data, Menu $parent = null)
    {
        $menus = array();
        $em = $this->getDoctrine()->getManager();

        foreach ($data as $pos => $d) {
            if ($d['id'] > 0) {
                $menu = new Menu();
                $menu->setPage($em->find('DisplayCmsBundle:Page', $d['id']));
                $menu->setPosition($pos);
                $menu->setParent($parent);

                if (isset($d['children'])) {
                    foreach ($this->buildMenus($d['children'], $menu) as $child) {
                        $menu->addChild($child);
                    }
                }

                $menus[] = $menu;
            }
        }

        return $menus;
    }

    /**
     * @param mixed $menus
     * @return array
     */
    private function getIds($menus)
    {
        $ids = array();

        /** @var Menu $menu */
        foreach ($menus as $menu) {
            $ids[] = $menu->getPage()->getId();
            if ($menu->getChildren()->count()) {
                $ids = array_merge($ids, $this->getIds($menu->getChildren()));
            }
        }

        return $ids;
    }

    /**
     * @param mixed $menus
     * @return array
     */
    private function buildMenuArray($menus)
    {
        $data = array();
        /** @var Menu $menu */
        foreach ($menus as $menu) {
            $item = array('id' => $menu->getPage()->getId());
            if ($menu->getChildren()->count() > 0) {
                $item['children'] = $this->buildMenuArray($menu->getChildren());
            }
            $data[] = $item;
        }

        return $data;
    }
}
