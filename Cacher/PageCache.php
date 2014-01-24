<?php

namespace Display\CmsBundle\Cacher;

use Display\CmsBundle\Entity\Page;
use Doctrine\Common\Cache\ApcCache;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class PageCache extends ApcCache
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     *
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->setNamespace('DisplayCmsBundle:Page');
    }

    /**
     * @param string $locale
     * @param string $slug
     * @return string
     */
    public function get($locale, $slug = null)
    {
       return $this->fetch($this->getIdentifier($locale, $slug));
    }

    /**
     * @param Page $entity
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param bool    $recursive update cache of children if true
     * @return bool
     */
    public function warmup(Page $entity, $view, $parameters = array(), $recursive = false)
    {
        $this->remove($entity);
        if (!$entity->getCacheable()) return false;

        $this->container->get('request_stack')->getCurrentRequest()->setLocale($entity->getLocale());

        return $this->save(
            $this->getEntityIdentifier($entity),
            $this->container->get('templating')->render($view, array_merge(compact('entity'), $parameters))
        );
    }

    /**
     * @param array $entities
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     * @return bool
     */
    public function warmupAll($entities, $view, $parameters = array())
    {
        /** @var Page $entity */
        foreach($entities as $entity) {
            $params = $parameters;
            $params['menus'] = $parameters['menus'][$entity->getLocale()];
            $this->warmup($entity, $view, $params, false);
        }

        return true;
    }

    /**
     * @param $entity
     * @return bool
     */
    public function remove(Page $entity)
    {
        return $this->delete($this->getEntityIdentifier($entity));
    }

    /**
     * @return string
     */
    private function getEnvironment()
    {
        return $this->container->get('kernel')->getEnvironment();
    }

    /**
     * @param $locale
     * @param $slug
     * @return string
     */
    private function getIdentifier($locale, $slug)
    {
        return $this->getEnvironment() . '/'.  $locale . '/' . $slug;
    }

    /**
     * @param Page $entity
     * @return string
     */
    private function getEntityIdentifier(Page $entity)
    {
        return $this->getIdentifier($entity->getLocale(), $entity->getSlug());
    }
}