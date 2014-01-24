micro cms not tested but working online !
===

add in AppKernel.php
--------------------


<class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
            new Display\CmsBundle\DisplayCmsBundle(),
            new YourOrg\YourWebsiteBundle\YourOrgYourWebsiteBundle(),
            ...>

extends DisplayCmsBundle
------------------------
<

namespace YourProject\MyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class YourProject\MyBundle extends Bundle
{
    public function getParent()
    {
        return 'DisplayCmsBundle';
    }
}
>

create your layout
------------------

create in view YourProject\MyBundle\Resources\views
a front_layout.html.twig file who will overriding the the cms layout

parameters.yml
--------------
need a "maxage" integer for <Response> in parameters.yml


add in routing.yml
------------------
"
display_cms:
    resource: "@DisplayCmsBundle/Resources/config/routing.yml"
    prefix:   /
"


Make your own skin !