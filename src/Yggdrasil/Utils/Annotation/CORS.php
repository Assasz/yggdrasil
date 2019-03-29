<?php

namespace Yggdrasil\Utils\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class CORS
 *
 * @package Yggdrasil\Core\Controller
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 *
 * @Annotation
 * @Target({"CLASS"})
 */
class CORS
{
    /**
     * Allowed origins
     *
     * @var string
     */
    public $origins;

    /**
     * Allowed methods
     *
     * @var string
     */
    public $methods;

    /**
     * Allowed headers
     *
     * @var string
     */
    public $headers;

    /**
     * Allow credentials
     *
     * @var bool
     */
    public $credentials;

    /**
     * Cache max age
     *
     * @var integer
     */
    public $maxAge;
}
