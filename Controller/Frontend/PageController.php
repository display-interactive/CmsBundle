<?php

namespace Display\CmsBundle\Controller\Frontend;

use Display\CmsBundle\Entity\Page;
use Display\CmsBundle\Entity\PageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;

/**
 * Page controller.
 *
 */
class PageController extends Controller
{
    /**
     * @param Request $request
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function pageAction(Request $request, $slug)
    {
        // Get corresponding page
        if (!$slug) {
            $html = $this->get('cms.cache')->get($request->getLocale(), $slug);
            $page = $this->getPage($request->getLocale(), $slug);
        } else {
            $slugs = explode('/', $slug);
            $slugs = array_filter($slugs);

            $locales = Intl::getLocaleBundle()->getLocaleNames();
            if (isset($locales[$slugs[0]])) $request->setLocale($slugs[0]);

            if ($slugs[0] === $this->container->getParameter('locale')) { //if locale in url is default then we remove it
                array_shift($slugs);
                return $this->redirect($this->generateUrl('display_cms_frontend', array('slug' => implode('/', $slugs))), 301);
            }

            $slugs = $slugs[count($slugs) - 1];
            if (!$slugs || isset($locales[$slugs])) { //if the url part is a locale so we try to call the homepage
                $html = $this->get('cms.cache')->get($request->getLocale());
                $page = $this->getPage($request->getLocale());
            } else {
                $html = $this->get('cms.cache')->get($request->getLocale(), $slugs);
                $page = $this->getPage($request->getLocale(), $slugs);
            }
        }

        if ($page && !$this->isValidUrl($page, $slug)) {
            return $this->redirect($this->generateUrl('display_cms_frontend', array('slug' => $page->getUrl())), 301);
        }

        $response = new Response();
        if (!$page) {
            $response->setStatusCode(404);
            throw $this->createNotFoundException(sprintf('There is no page corresponding to "%s"', $slug));
        }

        if (!$html && $page) {
            $html = $this->getHtml($page);
        }

        $response->setEtag(md5($html));

        if ($page->getCacheable()) {
            $maxAge = $this->container->getParameter('maxage');
            $response->setPublic();
            $response->setSharedMaxAge($maxAge);
            $response->setMaxAge($maxAge);
            $response->setLastModified($page->getCachedAt());
        } else {
            $response->headers->removeCacheControlDirective('Cache-Control');
            $response->headers->set('Cache-Control', 'no-cache');
        }

        if ($response->isNotModified($request)) {
            return $response;
        }

        $response->setContent($html);

        return $response;
    }

    /**
     * @param Page $entity
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getHtml($entity)
    {
        $menus = $this->getMenus($entity->getLocale());

        $cache = $this->get('cms.cache');
        if ($cache->warmup($entity, 'DisplayCmsBundle:Cms:page.html.twig', compact('menus'))) {
            $html = $cache->get($entity->getLocale(), $entity->getSlug());
        } else {
            $html = $this->renderView('DisplayCmsBundle:Cms:page.html.twig', compact('entity', 'menus'));
        }

        return $html;
    }

    /**
     * @param string $locale
     * @param string $slug
     * @return Page
     */
    private function getPage($locale, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PageRepository $repository */
        $repository = $em->getRepository('DisplayCmsBundle:Page');

        if (!$slug) {
            $entity = $repository->findHome($locale);
        } else {
            $entity = $repository->findBySlug($slug, $locale);
        }

        return $entity;
    }

    /**
     * @param string $locale
     * @return array
     */
    private function getMenus($locale = 'fr')
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PageRepository $repository */
        $repository = $em->getRepository('DisplayCmsBundle:Menu');

        return $repository->findByLocale($locale);
    }

    /**
     * @param Page $page
     * @param string $slug
     * @return bool
     */
    private function isValidUrl(Page $page, $slug)
    {
        $valid = false;
        if ($slug !== $page->getSlug()
            || null === $page->getParent()
            || null === $page->getParent()->getParent()
        ) {
            $valid = true;
        }

        return $valid;
    }
}