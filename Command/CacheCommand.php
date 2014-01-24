<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 13/12/13
 * Time: 15:49
 */

namespace Display\CmsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CacheCommand extends ContainerAwareCommand
{
    /**
     * @see ContainerAwareCommand
     */
    protected function configure()
    {
        $this
            ->setName('cms:cache:warmup')
            ->setDescription('Generate cache')
        ;
    }

    /**
     * @see ContainerAwareCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('not finish because of APC in CLI');
        return;

        $container = $this->getContainer();
        $cache = $container->get('cms.cache');
        $em = $container->get('doctrine')->getManager();
        $entities = $em->getRepository('DisplayCmsBundle:Page')->findAll();
        $success = $cache->warmupAll($entities, 'DisplayCmsBundle:Cms:page.html.twig');
        $env = $container->get('kernel')->getEnvironment();
        if ($success) {
            $output->writeln('cache has been generated for '. $env);
        } else {
            $output->writeln('cache generation failed for '. $env);
        }
    }
}