<?php

namespace Yggdrasil\Utils\Service;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\DriverAccessorTrait;
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
     * @param ConfigurationInterface $appConfiguration Configuration passed by ContainerDriver
     *
     * @throws \ReflectionException
     */
    public function __construct(ConfigurationInterface $appConfiguration)
    {
        $this->drivers = $appConfiguration->loadDrivers();

        $this->registerContracts();
    }

    /**
     * Registers contracts between service and external suppliers
     *
     * @throws BrokenContractException
     * @throws \ReflectionException
     */
    protected function registerContracts(): void
    {
        foreach ($this->getContracts() as $contract => $supplier) {
            if (!is_subclass_of($supplier, $contract)) {
                throw new BrokenContractException($contract);
            }

            $reflection = new \ReflectionClass($contract);
            $property = str_replace('Interface', '', $reflection->getShortName());

            $this->{lcfirst($property)} = $supplier;
        }
    }

    /**
     * Returns contracts between service and external suppliers
     *
     * @example [EntityManagerInterface::class => $this->getEntityManager()]
     *
     * @return array
     */
    abstract protected function getContracts(): array;
}
