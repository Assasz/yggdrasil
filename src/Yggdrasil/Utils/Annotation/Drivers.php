<?php

namespace Yggdrasil\Utils\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\Annotation\Required;

/**
 * Class Drivers
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 *
 * @Annotation
 * @Target({"CLASS"})
 */
class Drivers
{
    /**
     * Drivers to install: {ContractClass} => {DriverName}
     * Be aware that services require indexed array, where ContractClass is the index
     *
     * @var array<string>
     * @example [ValidatorInterface::class => 'validator']
     *
     * @Required
     */
    public $install;
}
