<?php

namespace Yggdrasil\Utils\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\Annotation\Required;

/**
 * Class Repository
 *
 * @package Yggdrasil\Utils\Annotation
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 *
 * @Annotation
 * @Target({"CLASS"})
 */
class Repository
{
    /**
     * Repository name
     *
     * @var string
     *
     * @Required
     */
    public $name;

    /**
     * Corresponding contract class name
     *
     * @var string
     *
     * @Required
     */
    public $contract;

    /**
     * Driver name marked as repository provider
     *
     * @var string
     *
     * @Required
     */
    public $repositoryProvider;
}
