<?php

namespace Yggdrasil\Utils\Seeds;

use Yggdrasil\Utils\Exception\SeedsStorageException;

/**
 * Class AbstractSeeds
 *
 * Base class for entity seeds
 *
 * @package Yggdrasil\Utils\Seeds
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class AbstractSeeds
{
    /**
     * Seeder instance
     *
     * @var SeederInterface
     */
    protected $seeder;

    /**
     * Number of already persisted seeds
     *
     * @var int
     */
    protected $persistedSeeds;

    /**
     * AbstractSeeds constructor.
     *
     * @param SeederInterface $seeder
     */
    public function __construct(SeederInterface $seeder)
    {
        $this->seeder = $seeder;
        $this->persistedSeeds = 0;
    }

    /**
     * Persists seeds in database
     */
    public function persist(): void
    {
        foreach ($this->create() as $seed) {
            $this->seeder->persist($seed);
            $this->persistedSeeds++;
        }

        $this->seeder->flush();
    }

    /**
     * Returns number of already persisted seeds
     *
     * @return int
     */
    public function getPersistedSeeds(): int
    {
        return $this->persistedSeeds;
    }

    /**
     * Clears given seeds storage
     *
     * @param string? $storage Storage name, if NULL seeds class name will be used to resolve storage name
     * @throws \ReflectionException
     * @throws SeedsStorageException
     */
    protected function clearStorage(string $storage = null): void
    {
        if (empty($storage)) {
            $seedsReflection = new \ReflectionClass(get_class($this));
            $storage = strtolower(str_replace('Seeds', '', $seedsReflection->getShortName()));
        }

        if (!$this->seeder->truncate($storage)) {
            throw new SeedsStorageException("Unable to clear seeds storage: {$storage}");
        }
    }

    /**
     * Creates seeds
     *
     * @return array
     */
    abstract protected function create(): array;
}
