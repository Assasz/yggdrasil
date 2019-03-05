<?php

namespace Yggdrasil\Utils\Seeds;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Exception\DriverNotFoundException;
use Yggdrasil\Core\Exception\MissingConfigurationException;
use Yggdrasil\Utils\Exception\InvalidSeedsException;
use Yggdrasil\Utils\Exception\SeedsNotFoundException;

/**
 * Class SeedsPersistCommand
 *
 * Persists seeds in persistance storage
 *
 * @package Yggdrasil\Utils\Seeds
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class SeedsPersistCommand extends Command
{
    /**
     * Application configuration
     *
     * @var ConfigurationInterface
     */
    private $appConfiguration;

    /**
     * EntityGenerateCommand constructor.
     *
     * @param ConfigurationInterface $appConfiguration
     */
    public function __construct(ConfigurationInterface $appConfiguration)
    {
        $this->appConfiguration = $appConfiguration;

        parent::__construct();
    }

    /**
     * Configures command
     */
    protected function configure()
    {
        $this
            ->setName('yggdrasil:seeds:persist')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of seeds to persist.')
            ->setDescription('Persists entity seeds in persistance storage')
            ->setHelp('This command allows you to persist entity seeds in persistance storage');
    }

    /**
     * Executes command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws DriverNotFoundException if entity manager driver cannot be found
     * @throws MissingConfigurationException if seeds_namespace is not configured
     * @throws SeedsNotFoundException if seeds class cannot be found
     * @throws InvalidSeedsException if seeds class is not a subclass of AbstractSeeds
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if (!$this->appConfiguration->hasDriver('entityManager')) {
            throw new DriverNotFoundException('To persist seeds entity manager driver is required.');
        }

        if (!$this->appConfiguration->isConfigured(['seeds_namespace'], 'entity_manager')) {
            throw new MissingConfigurationException(['seeds_namespace'], 'entity_manager');
        }

        $configuration = $this->appConfiguration->getConfiguration();
        $seedsName = $configuration['entity_manager']['seeds_namespace'] . $input->getArgument('name') . 'Seeds';

        if (!class_exists($seedsName)) {
            throw new SeedsNotFoundException($seedsName . ' class doesn\'t exist.');
        }

        $seeds = new $seedsName($this->appConfiguration->loadDriver('entityManager'));

        if (!$seeds instanceof AbstractSeeds) {
            throw new InvalidSeedsException($seedsName . ' class is not a valid seeds class');
        }

        $seeds->persist();

        $output->writeln("{$seeds->getPersistedSeeds()} {$input->getArgument('name')} seeds persisted successfully.");
    }
}
