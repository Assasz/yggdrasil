<?php

namespace Yggdrasil\Component\NidhoggComponent;

/**
 * Interface TopicInterface
 *
 * @package Yggdrasil\Component\NidhoggComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
interface TopicInterface
{
    /**
     * Returns allowed origins for topic
     *
     * @return array
     */
    public function getAllowedOrigins(): array;

    /**
     * Returns topic host
     *
     * @return string?
     */
    public function getHost(): ?string;
}