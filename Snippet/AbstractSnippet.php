<?php

namespace Display\CmsBundle\Snippet;

use Doctrine\Common\Cache\ApcCache;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;

abstract class AbstractSnippet extends ContainerAware implements InterfaceSnippet
{
    /**
     * @var \Twig_Environment
     */
    protected $env;

    /**
     * @var array Twig context
     */
    protected $context;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @param \Twig_Environment $env
     * @param array $context
     * @param array $paramaters
     */
    public function __construct(\Twig_Environment $env, array $context, array $paramaters)
    {
        $this->env = $env;
        $this->context = $context;
        $this->parameters = $paramaters;
    }

    /**
     * Concat attributes
     *
     * @return string
     */
    public function contactAttributes()
    {
        if (isset($this->parameters['attr'])) {
            $attr = array();
            foreach ($this->parameters['attr'] as $key => $value) {
                $attr[] = $key . '="' . $value . '"';
            }

            return implode (' ', $attr);
        }
    }

    /**
     * Returns a rendered view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     */
    public function renderView($view, array $parameters = array())
    {
        return $this->container->get('templating')->render($view, $parameters);
    }

    /**
     * Returns a NotFoundHttpException.
     *
     * This will result in a 404 response code. Usage example:
     *
     *     throw $this->createNotFoundException('Page not found!');
     *
     * @param string    $message  A message
     * @param \Exception $previous The previous exception
     *
     * @return NotFoundHttpException
     */
    public function createNotFoundException($message = 'Not Found', \Exception $previous = null)
    {
        return new NotFoundHttpException($message, $previous);
    }

    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return Registry
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    public function getDoctrine()
    {
        if (!$this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine');
    }

    /**
     * Returns true if the service id is defined.
     *
     * @param string $id The service id
     *
     * @return Boolean true if the service id is defined, false otherwise
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Gets a service by id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }
}