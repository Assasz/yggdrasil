<?php

namespace Yggdrasil\Core\Service\Utils;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class ServicePortGenerateCommand
 *
 * Console command that generates service port
 *
 * @package Yggdrasil\Core\Service\Utils
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class ServicePortGenerateCommand extends Command
{
    /**
     * Application configuration
     *
     * @var ConfigurationInterface
     */
    private $appConfiguration;

    /**
     * ServicePortGenerateCommand constructor.
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
            ->setName('yggdrasil:service-port:generate')
            ->setDescription('Generates service port')
            ->setHelp('This command allows you to generate service port.');
    }

    /**
     * Executes command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws MissingConfigurationException if service_namespace is not configured
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if (!$this->appConfiguration->isConfigured(['service_namespace'], 'container')) {
            throw new MissingConfigurationException(['service_namespace'], 'container');
        }

        $output->writeln([
            '----------------',
            'Service port generator',
            '----------------',
            ''
        ]);

        $helper      = $this->getHelper('question');
        $questionSet = $this->createQuestionSet();

        $servicePortName = $helper->ask($input, $output, $questionSet['servicePortName']);
        $servicePortType = $helper->ask($input, $output, $questionSet['servicePortType']);
        $moduleName      = $helper->ask($input, $output, $questionSet['moduleName']);

        do {
            $propertyNames[] = $helper->ask($input, $output, $questionSet['propertyName']);
            $propertyTypes[] = $helper->ask($input, $output, $questionSet['propertyType']);
        } while ($helper->ask($input, $output, $questionSet['continue']));

        $properties = array_combine($propertyNames, $propertyTypes);

        $configuration    = $this->appConfiguration->getConfiguration();
        $serviceNamespace = rtrim($configuration['container']['service_namespace'], '\\');

        $servicePortData = [
            'namespace'  => $serviceNamespace,
            'class'      => $servicePortName,
            'type'       => $servicePortType,
            'module'     => $moduleName,
            'properties' => $properties
        ];

        (new ServicePortGenerator($servicePortData))->generate();

        $output->writeln('Service ' . strtolower($servicePortType) . ' generated successfully!');
    }

    /**
     * Creates command question set
     *
     * @return array
     */
    private function createQuestionSet(): array
    {
        return [
            'servicePortName' =>
                new Question('Service name: '),
            'servicePortType' =>
                new ChoiceQuestion('Service port type: ', [
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