<?php
namespace Display\CmsBundle\Twig;

use Display\CmsBundle\Entity\PageRepository;
use Display\CmsBundle\Snippet\AbstractSnippet;
use Display\CmsBundle\Snippet\InterfaceSnippet;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DisplayCmsPathExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @{inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('cms_path', array($this, 'getPath'), array('is_safe' => array('html')))
        );
    }

    /**
     * @param null $slug
     * @param bool $referenceType
     * @return mixed
     * @throws CmsPathException
     */
    public function getPath($slug = null, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $defaultLocale = $this->container->getParameter('locale');
        $locale = $this->container->get('request_stack')->getCurrentRequest()->getLocale();

        if (is_numeric($slug)) {
            $page = $this->getPageById($slug);
        } else {
            $slug = explode('/', $slug);
            $slug = $slug[count($slug) - 1];
            $page = $this->getPage($slug, $locale);
        }

        if (!$page && $slug) {
            throw new CmsPathException(sprintf('The page (%s) does not exist in the language of the parent page (%s)', $slug, $locale));
        }

        $path = null;
        if ($page) {
            $path = $page->getUrl();
            if ($defaultLocale !== $locale) {
                $path = $locale . '/' . $path;
            }
        }

        return $this->container->get('router')->generate('display_cms_frontend', array('slug' => $path), $referenceType);
    }

    /**
     * @param string $slug
     * @param string $locale
     * @return \Display\CmsBundle\Entity\Page
     */
    public function getPage($slug, $locale)
    {
        $em = $this->container->get('doctrine')->getManager();
        /** @var PageRepository $repository */
        $repository = $em->getRepository('DisplayCmsBundle:Page');

        if ($slug) {
            $page = $repository->findBySlug($slug, $locale, null);
        } else {
            $page = $repository->findHome($locale);
        }

        return $page;
    }

    /**
     * @param int $id
     * @return \Display\CmsBundle\Entity\Page
     */
    public function getPageById($id)
    {
        $em = $this->container->get('doctrine')->getManager();
        /** @var PageRepository $repository */
        $repository = $em->getRepository('DisplayCmsBundle:Page');

        return $repository->find($id);
    }

    /**
     * @{inheritDoc}
     */
    public function getName()
    {
        return 'display_cms_path_extension';
    }
} 