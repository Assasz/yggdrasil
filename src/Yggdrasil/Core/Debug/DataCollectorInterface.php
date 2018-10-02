<?php

namespace Yggdrasil\Core\Debug;

/**
 * Interface DataCollectorInterface
 *
 * @package Yggdrasil\Core\Debug
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
interface DataCollectorInterface
{
    /**
     * Returns data collected from source
     *
     * @return array
     */
    public function collect(): array;

    /**
     * Sets data source
     *
     * @param mixed $source
     * @return DataCollectorInterface
     */
    public function setSource($source): DataCollectorInterface;

    /**
     * Returns collector name
     *
     * @return string
     */
    public function getName(): string;
}