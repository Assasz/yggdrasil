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
     * @var array<string>
     *
     * @Required
     */
    public $install;
}
