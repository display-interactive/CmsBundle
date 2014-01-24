extends DisplayCmsBundle

"
<?php

namespace YourProject\MyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class YourProject\MyBundle extends Bundle
{
    public function getParent()
    {
        return 'DisplayCmsBundle';
    }
}
"


create in view YourProject\MyBundle\Resources\views
a front_layout.html.twig file who will overriding the the cms layout