<?php
namespace Display\CmsBundle\Twig;

use Display\CmsBundle\Snippet\AbstractSnippet;
use Display\CmsBundle\Snippet\InterfaceSnippet;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DisplayCmsSnippetExtension extends \Twig_Extension
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
            new \Twig_SimpleFunction('snippet', array($this, 'snippet'), array('needs_environment' => true, 'needs_context' => true, 'is_safe' => array('html')))
        );
    }

    /**
     * @param \Twig_Environment $env
     * @param array $context
     * @param $shortClass
     * @param $parameters
     * @return mixed
     */
    public function snippet($env, $context, $shortClass, $parameters)
    {
        if (class_exists($shortClass)) {
            $class = $shortClass;
        } else {
            $class = $this->getFullQualifiedClass($shortClass);
        }

        /** @var AbstractSnippet $snippet */
        $snippet = new $class($env, $context, $parameters);
        $snippet->setContainer($this->container);

        return $snippet->getOutput();
    }

    /**
     * @{inheritDoc}
     */
    public function getName()
    {
        return 'display_cms_snippet_extension';
    }

    /**
     * Get Class from short class
     *
     * @param $shortClass
     * @return string
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    private function getFullQualifiedClass($shortClass)
    {
        list($bundle, $className) = explode(':', $shortClass);

        $kernelBundles = $this->container->getParameter('kernel.bundles');
        if (!isset($kernelBundles[$bundle])) {
            throw new \InvalidArgumentException(sprintf('Unknown bundle %s', $bundle));
        }

        $namespacePart = explode('\\', $kernelBundles[$bundle]);
        array_pop($namespacePart);

        $fullQualifiedClassName = implode('\\', $namespacePart).'\\Snippet\\'.$className;
        if (!class_exists($fullQualifiedClassName)) {
            throw new \InvalidArgumentException(sprintf('Unknown class %s', $fullQualifiedClassName));
        }

        $interface = 'Display\CmsBundle\Snippet\InterfaceSnippet';
        if (!in_array($interface, class_implements($fullQualifiedClassName))) {
            throw new \LogicException(sprintf('The class % must implements', $fullQualifiedClassName, $interface));
        }

        return $fullQualifiedClassName;
    }
} 