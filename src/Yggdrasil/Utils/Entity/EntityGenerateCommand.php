<?php

namespace Yggdrasil\Utils\Entity;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Yggdrasil\Core\Configuration\ConfigurationInterface;

/**
 * Class EntityGenerateCommand
 *
 * Console command that generates domain entity
 *
 * @package Yggdrasil\Utils\Entity
 */
class EntityGenerateCommand extends Command
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
    protected function configure(): void
    {
        $this
            ->setName('yggdrasil:entity:generate')
            ->setDescription('Generates basic domain entity')
            ->setHelp('This command allows you to generate basic domain entity.');
    }

    /**
     * Executes command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln([
            '----------------',
            'Domain entity generator',
            '----------------',
            ''
        ]);

        $helper      = $this->getHelper('question');
        $questionSet = $this->createQuestionSet();
        $entityName  = $helper->ask($input, $output, $questionSet['entityName']);

        do {
            $propertyNames[] = $helper->ask($input, $output, $questionSet['propertyName']);
            $propertyTypes[] = $helper->ask($input, $output, $questionSet['propertyType']);
        } while ($helper->ask($input, $output, $questionSet['continue']));

        $properties = array_combine($propertyNames, $propertyTypes);

        $configuration   = $this->appConfiguration->getConfiguration();
        $entityNamespace = $configuration['framework']['root_namespace'] . 'Domain\Entity';

        $entityData = [
            'namespace'  => $entityNamespace,
            'class'      => $entityName,
            'properties' => $properties
        ];

        (new EntityGenerator($entityData))->generate();

        $output->writeln('Entity generated successfully!');
    }

    /**
     * Creates command question set
     *
     * @return array
     */
    private function createQuestionSet(): array
    {
        return [
            'entityName' =>
                new Question('Entity name: '),
            'propertyName' =>
                new Question('Property name: '),
            'propertyType' =>
                new ChoiceQuestion('Property type: ', [
                    'string', 'int', 'float', 'datetime'
                ], 0),
            'continue' =>
                new ConfirmationQuestion('Continue with next property? (y/n)', true)
        ];
    }
}