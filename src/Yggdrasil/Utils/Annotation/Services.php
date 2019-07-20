<?php

namespace Yggdrasil\Utils\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\Annotation\Required;

/**
 * Class Services
 *
 * @package Yggdrasil\Utils\Annotation
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 *
 * @Annotation
 * @Target({"CLASS"})
 */
class Services
{
    /**
     * Services to install: {ModuleName} => {ServiceClass}
     *
     * @var array<string>
     * @example ['User' => RegisterService::class]
     *
     * @Required
     */
    public $install;
}
