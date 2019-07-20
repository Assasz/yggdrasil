<?php

namespace Yggdrasil\Utils\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\DriverAccessorTrait;
use Yggdrasil\Utils\Annotation\Drivers;
use Yggdrasil\Utils\Annotation\Repository;
use Yggdrasil\Utils\Exception\BrokenContractException;

/**
 * Class AbstractService
 *
 * Base class for application services
 *
 * @package Yggdrasil\Utils\Service
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class AbstractService
{
    /**
     * Trait that provides access to drivers
     */
    use DriverAccessorTrait;

    /**
     * AbstractService constructor.
     *
     * Loads drivers from configuration
     *
     * @param ConfigurationInterface $configuration Configuration passed by ContainerDriver
     * @throws \ReflectionException
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->drivers = $configuration->loadDrivers();

        $this->registerContracts();
    }

    /**
     * Registers contracts between service and external suppliers
     * These contracts may include drivers and repositories and are read from class annotations
     *
     * @throws BrokenContractException
     * @throws \ReflectionException
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    protected function registerContracts(): void
    {
        $reflection = new \ReflectionClass($this);
        $reader = new AnnotationReader();

        foreach ($reader->getClassAnnotations($reflection) as $annotation) {
            if ($annotation instanceof Drivers) {
                foreach ($annotation->install as $contract => $driver) {
                    $driverInstance = $this->drivers->get($driver);

                    if (!is_subclass_of($driverInstance, $contract)) {
                        throw new BrokenContractException($contract);
                    }

                    $this->{$driver} = $driverInstance;
                }
            }

            if ($annotation instanceof Repository) {
                $repository = $this->drivers->get($annotation->repositoryProvider)->getRepository($annotation->name);

                if (!is_subclass_of($repository, $annotation->contract)) {
                    throw new BrokenContractException($annotation->contract);
                }

                $repositoryReflection = new \ReflectionClass($annotation->contract);
                $property = str_replace('Interface', '', $repositoryReflection->getShortName());

                $this->{lcfirst($property)} = $repository;
            }
        }
    }
}
