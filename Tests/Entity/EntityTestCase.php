<?php

namespace Display\CmsBundle\Tests\Entity;

use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

abstract class EntityTestCase extends WebTestCase
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Container
     */
    protected $container;


    public function setUp()
    {
        static::$kernel = static::createKernel();
        // Boot the AppKernel in the test environment and with the debug.
        static::$kernel->boot();

        // Store the container and the entity manager in test case properties
        $this->container = static::$kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();

        // Build the schema for sqlite
        $this->generateSchema();

        parent::setUp();
    }

    public function tearDown()
    {
        // Shutdown the kernel.
        static::$kernel->shutdown();

        parent::tearDown();
    }

    protected function generateSchema()
    {
        // Get the metadata of the application to create the schema.
        $metadata = $this->getMetadata();

        if ( ! empty($metadata)) {
            // Create SchemaTool
            $tool = new SchemaTool($this->entityManager);
            $tool->createSchema($metadata);
        } else {
            throw new SchemaException('No Metadata Classes to process.');
        }
    }

    /**
     * Overwrite this method to get specific metadata.
     *
     * @return Array
     */
    protected function getMetadata()
    {
        return $this->entityManager->getMetadataFactory()->getAllMetadata();
    }
}
