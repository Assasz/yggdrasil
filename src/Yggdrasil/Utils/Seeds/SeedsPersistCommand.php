<?php

namespace Yggdrasil\Utils\Seeds;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Exception\DriverNotFoundException;
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
    private $configuration;

    /**
     * EntityGenerateCommand constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;

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
     * @throws SeedsNotFoundException if seeds class cannot be found
     * @throws InvalidSeedsException if seeds class is not a subclass of AbstractSeeds
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if (!$this->configuration->hasDriver('entityManager')) {
            throw new DriverNotFoundException('To persist seeds entity manager driver is required.');
        }

        $seedsName = $this->configuration->get('root_namespace', 'framework') . 'Infrastructure\Seeds\\' . $input->getArgument('name') . 'Seeds';

        if (!class_exists($seedsName)) {
            throw new SeedsNotFoundException($seedsName . ' class doesn\'t exist.');
        }

        $seeds = new $seedsName($this->configuration->installDriver('entityManager'));

        if (!$seeds instanceof AbstractSeeds) {
            throw new InvalidSeedsException($seedsName . ' class is not a valid seeds class');
        }

        $seeds->persist();

        $output->writeln("{$seeds->getPersistedSeeds()} {$input->getArgument('name')} seeds persisted successfully.");
    }
}
