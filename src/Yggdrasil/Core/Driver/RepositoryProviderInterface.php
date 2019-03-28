<?php

namespace Yggdrasil\Core\Driver;

/**
 * Interface RepositoryProviderInterface
 *
 * Driver able to provide repositories
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
interface RepositoryProviderInterface
{
    /**
     * Returns given repository
     *
     * @param string $name Name of repository
     * @return mixed Repository object
     */
    public function getRepository(string $name);
}
