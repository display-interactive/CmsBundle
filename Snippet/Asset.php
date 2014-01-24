<?php
namespace Display\CmsBundle\Snippet;


class Asset extends Image
{
    /**
     * @{inheritDoc}
     */
    public function getOutput()
    {
        $image = $this->getMedia();
        $helper = $this->container->get('templating.helper.assets');

        return $helper->getUrl($image->getWebPath());
    }
} 