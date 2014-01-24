<?php

namespace Display\CmsBundle\Controller\Backend;

use Display\CmsBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FileExplorerController extends Controller
{
    /**
     *
     */
    public function indexAction(Request $request)
    {
        $list = $this->getList($request);

        return $this->render('DisplayCmsBundle:FileExplorer:index.html.twig', array(
            'list' => $list
        ));
    }

    /**
     * Get list of files and directories
     *
     * @param Request $request
     * @return array
     */
    private function getList($request)
    {
        $directories = $files = array();
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('DisplayCmsBundle:Media')->findByType($request->query->get('type', null));
        /** @var Media $entity */
        foreach ($entities as $entity) {
            $files[] = array(
                'return' => '{{ snippet(\'DisplayCmsBundle:Image\',{\'id\': '.$entity->getId().'}) }}',
                'name' => $entity->getName(),
                'path' => $entity->getPath()
            );
        }

        return array('directories' => $directories, 'files' => $files);
    }
}
