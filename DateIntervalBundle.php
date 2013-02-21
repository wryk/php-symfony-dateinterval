<?php

namespace Herrera\Symfony\DateInterval;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Herrera\Doctrine\DBAL\Types\DateIntervalType;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The DateInterval bundle class.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class DateIntervalBundle extends Bundle
{
    /**
     * @override
     */
    public function boot()
    {
        if (false === Type::hasType(DateIntervalType::DATEINTERVAL)) {
            Type::addType(
                DateIntervalType::DATEINTERVAL,
                'Herrera\\Doctrine\\DBAL\\Types\\DateIntervalType'
            );
        }

        if ($this->container->has('doctrine.orm.entity_manager')) {
            /** @var $manager EntityManager */
            $manager = $this->container->get('doctrine.orm.entity_manager');
            $configuration = $manager->getConfiguration();

            if (null === $configuration->getCustomDatetimeFunction('DATE_INTERVAL')) {
                $configuration->addCustomDatetimeFunction(
                    'DATE_INTERVAL',
                    'Herrera\\Doctrine\\ORM\\Query\\AST\\Functions\\DateIntervalFunction'
                );
            }

            $platform = $manager->getConnection()->getDatabasePlatform();

            if (false === $platform->hasDoctrineTypeMappingFor(
                DateIntervalType::DATEINTERVAL
            )){
                $platform->registerDoctrineTypeMapping(
                    DateIntervalType::DATEINTERVAL,
                    DateIntervalType::DATEINTERVAL
                );
            }
        }
    }
}