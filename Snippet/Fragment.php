<?php
namespace Display\CmsBundle\Snippet;

class Fragment extends AbstractSnippet
{
    /**
     * @{inheritDoc}
     */
    public function getOutput()
    {
        $em = $this->getDoctrine()->getManager();
        /** @var \Display\CmsBundle\Entity\Fragment $fragment */

        $fragment = $em->getRepository('DisplayCmsBundle:Fragment')->find($this->parameters['id']);

        if ($fragment) {
            return $this->render($fragment->getContent());
        }
    }

    /**
     * @param string $content
     * @return string
     */
    protected function render($content)
    {
        return twig_include($this->env, $this->context, twig_template_from_string($this->env, $content));
    }
} 