<?php

namespace Yggdrasil\Utils\Service;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class ServiceDTOGenerateCommand
 *
 * Console command that generates service DTO (Data Transfer Object)
 *
 * @package Yggdrasil\Utils\Service
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class ServiceDTOGenerateCommand extends Command
{
    /**
     * Application configuration
     *
     * @var ConfigurationInterface
     */
    private $appConfiguration;

    /**
     * ServiceDTOGenerateCommand constructor.
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
    protected function configure(): void
    {
        $this
            ->setName('yggdrasil:service-dto:generate')
            ->setDescription('Generates service DTO')
            ->setHelp('This command allows you to generate service DTO.');
    }

    /**
     * Executes command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws MissingConfigurationException if service_namespace is not configured
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if (!$this->appConfiguration->isConfigured(['service_namespace'], 'container')) {
            throw new MissingConfigurationException(['service_namespace'], 'container');
        }

        $output->writeln([
            '----------------',
            'Service DTO generator',
            '----------------',
            ''
        ]);

        $helper      = $this->getHelper('question');
        $questionSet = $this->createQuestionSet();

        $serviceDTOName = $helper->ask($input, $output, $questionSet['serviceDTOName']);
        $serviceDTOType = $helper->ask($input, $output, $questionSet['serviceDTOType']);
        $moduleName     = $helper->ask($input, $output, $questionSet['moduleName']);

        do {
            $propertyNames[] = $helper->ask($input, $output, $questionSet['propertyName']);
            $propertyTypes[] = $helper->ask($input, $output, $questionSet['propertyType']);
        } while ($helper->ask($input, $output, $questionSet['continue']));

        $properties = array_combine($propertyNames, $propertyTypes);

        $configuration    = $this->appConfiguration->getConfiguration();
        $serviceNamespace = rtrim($configuration['container']['service_namespace'], '\\');

        $serviceDTOData = [
            'namespace'  => $serviceNamespace,
            'class'      => $serviceDTOName,
            'type'       => $serviceDTOType,
            'module'     => $moduleName,
            'properties' => $properties
        ];

        (new ServiceDTOGenerator($serviceDTOData))->generate();

        $output->writeln('Service ' . strtolower($serviceDTOType) . ' generated successfully!');
    }

    /**
     * Creates command question set
     *
     * @return array
     */
    private function createQuestionSet(): array
    {
        return [
            'serviceDTOName' =>
                new Question('Service name: '),
            'serviceDTOType' =>
                new ChoiceQuestion('Service DTO type: ', [
                    'Request', 'Response'
                ], 0),
            'moduleName' =>
                new Question('Module name: '),
            'propertyName' =>
                new Question('Property name: '),
            'propertyType' =>
                new ChoiceQuestion('Property type: ', [
                    'string', 'int', 'float', 'datetime', 'bool', 'array'
                ], 0),
            'continue' =>
                new ConfirmationQuestion('Continue with next property? (y/n)', true)
        ];
    }
}