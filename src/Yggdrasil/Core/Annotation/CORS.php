<?php

namespace Yggdrasil\Core\Annotation;

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
     * @var string
     */
    public $origins;

    /**
     * @var string
     */
    public $methods;

    /**
     * @var string
     */
    public $headers;

    /**
     * @var bool
     */
    public $credentials;

    /**
     * @var integer
     */
    public $maxAge;
}
