<?php

namespace Yggdrasil\Component\NidhoggComponent\Routing;

use Yggdrasil\Component\NidhoggComponent\Topic\TopicInterface;
/**
 * Class Route
 *
 * Object representation of route
 *
 * @package Yggdrasil\Component\NidhoggComponent\Routing
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class Route
{
    /**
     * Path to topic
     *
     * @var string
     */
    private $path;

    /**
     * Instance of topic
     *
     * @var TopicInterface
     */
    private $topic;

    /**
     * Returns path to topic
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Sets path to topic
     *
     * @param string $path
     * @return Route
     */
    public function setPath(string $path): Route
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Returns instance of topic
     *
     * @return TopicInterface
     */
    public function getTopic(): TopicInterface
    {
        return $this->topic;
    }

    /**
     * Sets instance of topic
     *
     * @param TopicInterface $topic
     * @return Route
     */
    public function setTopic(TopicInterface $topic): Route
    {
        $this->topic = $topic;

        return $this;
    }
}
