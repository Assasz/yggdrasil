<?php

namespace Yggdrasil\Utils\Seeds;

/**
 * Interface SeederInterface
 *
 * Persistence mechanism able to manage seeds
 *
 * @package Yggdrasil\Utils\Seeds
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
interface SeederInterface
{
    /**
     * Persists given seed object
     *
     * @param object $seed Seed object to persist
     */
    public function persist(object $seed): void;

    /**
     * Flushes all changes to seeds objects
     */
    public function flush(): void;

    /**
     * Truncates seeds storage
     *
     * @param string $storage Name of seeds storage
     * @return bool
     */
    public function truncate(string $storage): bool;
}
