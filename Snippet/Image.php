<?php
namespace Display\CmsBundle\Snippet;


class Image extends AbstractSnippet
{
    /**
     * @{inheritDoc}
     */
    public function getOutput()
    {
            $image = $this->getMedia();
            $helper = $this->container->get('templating.helper.assets');

            return '<img src="'.$helper->getUrl($image->getWebPath()).'" alt="'.$image->getName().'" '.$this->contactAttributes().' />';
    }

    /**
     * @return \Display\CmsBundle\Entity\Media
     */
    public function getMedia()
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('DisplayCmsBundle:Media')->findById($this->parameters['id']);
    }
} 