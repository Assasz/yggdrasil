<?php

namespace Yggdrasil\Core\Debug;

/**
 * Class DebugProfiler
 *
 * @package Yggdrasil\Core\Debug
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class DebugProfiler
{
    /**
     * Set of data collectors [collector_name => collector]
     *
     * @var array
     */
    private $dataCollectors;

    /**
     * DebugProfiler constructor.
     */
    public function __construct()
    {
        $this->dataCollectors = [];
    }

    /**
     * Adds data collector
     *
     * @param DataCollectorInterface $dataCollector
     * @param mixed $source Source of data collector
     * @return DebugProfiler
     */
    public function addDataCollector(DataCollectorInterface $dataCollector, $source): DebugProfiler
    {
        $this->dataCollectors[$dataCollector->getName()] = $dataCollector->setSource($source);

        return $this;
    }

    /**
     * Returns data collected by all data collectors [collector_name => collector_data]
     *
     * @return array
     */
    public function getCollectedData(): array
    {
        $collectedData = [];

        foreach ($this->dataCollectors as $collectorName => $collector) {
            $collectedData[$collectorName] = $collector->collect();
        }

        return $collectedData;
    }
}