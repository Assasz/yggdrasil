<?php

namespace Yggdrasil\Utils\Service;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\DriverAccessorTrait;
use Yggdrasil\Core\Exception\BrokenContractException;

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
     */
    public function __construct(ConfigurationInterface $appConfiguration)
    {
        $this->drivers = $appConfiguration->loadDrivers();

        $this->validateContracts();
    }

    /**
     * Validates registered contracts between service and external suppliers
     *
     * @throws BrokenContractException
     */
    protected function validateContracts(): void
    {
        foreach ($this->getContracts() as $supplier => $contract) {
            if (!is_subclass_of($supplier, $contract)) {
                throw new BrokenContractException($contract);
            }
        }
    }

    /**
     * Returns contracts between service and external suppliers
     *
     * @example [$this->getEntityManager() => EntityManagerInterface::class]
     *
     * @return array
     */
    abstract protected function getContracts(): array;
}
