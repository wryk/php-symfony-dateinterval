<?php

namespace Herrera\Symfony\DateInterval\Tests;

use Doctrine\DBAL\Types\Type;
use Herrera\Doctrine\DBAL\Types\DateIntervalType;
use Herrera\Symfony\DateInterval\DateIntervalBundle;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\Container;

class DateIntervalBundleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DateIntervalBundle
     */
    private $bundle;

    /**
     * @var Container
     */
    private $container;

    public function testBoot()
    {
        $this->bundle->boot();

        $this->assertTrue(Type::hasType(DateIntervalType::DATEINTERVAL));

        /** @var $manager EntityManager */
        $manager = $this->container->get('doctrine.orm.entity_manager');
        $configuration = $manager->getConfiguration();

        $this->assertNotNull($configuration->getCustomDatetimeFunction(
            'DATE_INTERVAL'
        ));

        $platform = $manager->getConnection()->getDatabasePlatform();

        $this->assertTrue($platform->hasDoctrineTypeMappingFor(
            DateIntervalType::DATEINTERVAL
        ));
    }

    protected function setUp()
    {
        $this->container = new Container();
        $this->container->set(
            'doctrine.orm.entity_manager',
            EntityManager::create(
                array(
                    'driver' => 'pdo_sqlite',
                    'memory' => true
                ),
                Setup::createAnnotationMetadataConfiguration(array())
            )
        );

        $this->bundle = new DateIntervalBundle();
        $this->bundle->setContainer($this->container);
    }
}