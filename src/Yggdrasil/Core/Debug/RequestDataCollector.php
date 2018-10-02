<?php

namespace Yggdrasil\Core\Debug;

use Symfony\Component\HttpFoundation\Request;
use Yggdrasil\Core\Exception\BadSourceProvidedException;

/**
 * Class RequestDataCollector
 *
 * @package Yggdrasil\Core\Debug
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class RequestDataCollector implements DataCollectorInterface
{
    /**
     * Collector name
     *
     * @var string
     */
    private const NAME = 'requestCollector';

    /**
     * Data source
     *
     * @var Request
     */
    private $source;

    /**
     * Returns data collected from source
     *
     * @return array
     */
    public function collect(): array
    {
        return [
            'post_data' => $this->source->request->all(),
            'get_data'  => $this->source->query->all(),
            'cookies'  => $this->source->cookies->all()
        ];
    }

    /**
     * Sets data source
     *
     * @param mixed $source Request object
     * @return DataCollectorInterface
     *
     * @throws BadSourceProvidedException if provided source is of wrong type
     */
    public function setSource($source): DataCollectorInterface
    {
        if (!$source instanceof Request) {
            throw new BadSourceProvidedException('Source of type Request expected, got ' . (is_object($source)) ? get_class($source) : gettype($source) . ' instead.');
        }

        $this->source = $source;

        return $this;
    }

    /**
     * Returns collector name
     *
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }
}