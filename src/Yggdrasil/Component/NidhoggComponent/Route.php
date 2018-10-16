<?php

namespace Yggdrasil\Component\NidhoggComponent;

/**
 * Class Route
 *
 * Object representation of route
 *
 * @package Yggdrasil\Component\NidhoggComponent
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
     * Allowed origins for route
     *
     * @var array
     */
    private $allowedOrigins;

    /**
     * Host overriding one provided for server
     *
     * @var string?
     */
    private $host;

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

    /**
     * Returns allowed origins for route
     *
     * @return array
     */
    public function getAllowedOrigins(): array
    {
        return $this->allowedOrigins;
    }

    /**
     * Sets allowed origins for route
     *
     * @param array $allowedOrigins
     * @return Route
     */
    public function setAllowedOrigins(array $allowedOrigins): Route
    {
        $this->allowedOrigins = $allowedOrigins;

        return $this;
    }

    /**
     * Returns route host
     *
     * @return string?
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * Sets route host
     *
     * @param string? $host
     * @return Route
     */
    public function setHost(?string $host): Route
    {
        $this->host = $host;

        return $this;
    }
}
