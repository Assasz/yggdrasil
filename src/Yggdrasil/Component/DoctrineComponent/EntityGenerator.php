<?php

namespace Yggdrasil\Component\DoctrineComponent;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Helpers;

/**
 * Class EntityGenerator
 *
 * Generates basic Doctrine entity
 *
 * @package Yggdrasil\Component\DoctrineComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class EntityGenerator
{
    /**
     * Data passed by command
     *
     * @var array
     */
    private $entityData;

    /**
     * Generated entity file
     *
     * @var PhpFile
     */
    private $entityFile;

    /**
     * Generated entity class
     *
     * @var ClassType
     */
    private $entityClass;

    /**
     * EntityGenerator constructor.
     *
     * Creates empty entity class
     *
     * @param array $entityData
     */
    public function __construct(array $entityData)
    {
        $this->entityData = $entityData;

        $this->entityFile = (new PhpFile())
            ->addComment('This file is auto-generated.');

        $this->entityClass = $this->entityFile
            ->addNamespace($entityData['namespace'])
            ->addClass($entityData['class'])
            ->addComment($entityData['class'] . ' entity' . PHP_EOL)
            ->addComment('@Entity')
            ->addComment('@Table(name="' . $entityData['table'] . '")' . PHP_EOL)
            ->addComment('@package ' . $entityData['namespace']);
    }

    /**
     * Generates entity properties
     *
     * @return EntityGenerator
     */
    private function generateProperties(): EntityGenerator
    {
        $this->entityClass
            ->addProperty('id')
            ->setVisibility('private')
            ->addComment($this->entityData['class'] . ' ID' . PHP_EOL)
            ->addComment('@Id')
            ->addComment('@Column(type="integer")')
            ->addComment('@GeneratedValue(strategy="AUTO")')
            ->addComment('@var int $id');

        foreach ($this->entityData['properties'] as $name => $type) {
            $property = $this->entityClass
                ->addProperty($name)
                ->setVisibility('private')
                ->addComment($this->entityData['class'] . ' ' . $name . PHP_EOL);

            switch ($type) {
                case 'datetime':
                    $property
                        ->addComment('@Column(type="datetime")')
                        ->addComment('@var \DateTime $' . $name);
                    break;
                case 'int':
                    $property
                        ->addComment('@Column(type="integer")')
                        ->addComment('@var int $' . $name);
                    break;
                case 'string':
                    $property
                        ->addComment('@Column(type="string", length=255)')
                        ->addComment('@var string $' . $name);
                    break;
                case 'text':
                    $property
                        ->addComment('@Column(type="text")')
                        ->addComment('@var string $' . $name);
                    break;
                default:
                    $property
                        ->addComment('@Column(type="' . $type . '")')
                        ->addComment('@var ' . $type . ' $' . $name);
                    break;
            }
        }

        return $this;
    }

    /**
     * Generates entity methods
     *
     * @return EntityGenerator
     */
    private function generateMethods(): EntityGenerator
    {
        $this->entityClass
            ->addMethod('getId')
            ->setVisibility('public')
            ->addComment('Returns ' . strtolower($this->entityData['class']) . ' ID' . PHP_EOL)
            ->addComment('@return int')
            ->addBody('return $this->id;');

        foreach ($this->entityData['properties'] as $name => $type) {
            $this
                ->generateGetter($name, $type)
                ->generateSetter($name, $type);
        }

        return $this;
    }

    /**
     * Generates property getter
     *
     * @param string $name Name of property
     * @param string $type Type of property
     * @return EntityGenerator
     */
    private function generateGetter(string $name, string $type): EntityGenerator
    {
        $getter = $this->entityClass
            ->addMethod('get' . ucfirst($name))
            ->setVisibility('public')
            ->addComment('Returns ' . strtolower($this->entityData['class']) . ' ' . $name . PHP_EOL);

        switch ($type) {
            case 'datetime':
                $getter
                    ->addComment('@return \DateTime')
                    ->setReturnType('\DateTime');
                break;
            case 'int':
                $getter
                    ->addComment('@return int')
                    ->setReturnType('int');
                break;
            case 'string':
                $getter
                    ->addComment('@return string')
                    ->setReturnType('string');
                break;
            case 'text':
                $getter
                    ->addComment('@return string')
                    ->setReturnType('string');
                break;
            default:
                $getter
                    ->addComment('@return ' . $type)
                    ->setReturnType($type);
                break;
        }

        $getter->addBody('return $this->' . $name . ';');

        return $this;
    }

    /**
     * Generates property setter
     *
     * @param string $name Name of property
     * @param string $type Type of property
     * @return EntityGenerator
     */
    private function generateSetter(string $name, string $type): EntityGenerator
    {
        $setter = $this->entityClass
            ->addMethod('set' . ucfirst($name))
            ->setVisibility('public')
            ->addComment('Sets ' . strtolower($this->entityData['class']) . ' ' . $name . PHP_EOL);

        switch ($type) {
            case 'datetime':
                $setter
                    ->addComment('@param \DateTime $' . $name)
                    ->addComment('@return ' . ucfirst($this->entityData['class']))
                    ->addParameter($name)
                    ->setTypeHint('\DateTime');
                break;
            case 'int':
                $setter
                    ->addComment('@param int $' . $name)
                    ->addComment('@return ' . ucfirst($this->entityData['class']))
                    ->addParameter($name)
                    ->setTypeHint('int');
                break;
            case 'string':
                $setter
                    ->addComment('@param string $' . $name)
                    ->addComment('@return ' . ucfirst($this->entityData['class']))
                    ->addParameter($name)
                    ->setTypeHint('string');
                break;
            case 'text':
                $setter
                    ->addComment('@param string $' . $name)
                    ->addComment('@return ' . ucfirst($this->entityData['class']))
                    ->addParameter($name)
                    ->setTypeHint('string');
                break;
            default:
                $setter
                    ->addComment('@param ' . $type . ' $' . $name)
                    ->addComment('@return ' . ucfirst($this->entityData['class']))
                    ->addParameter($name)
                    ->setTypeHint($type);
                break;
        }

        $setter
            ->addBody('$this->' . $name . ' = $' . $name . ';')
            ->addBody('return $this;')
            ->setReturnType($this->entityData['namespace'] . '\\' . ucfirst($this->entityData['class']));

        return $this;
    }

    /**
     * Generates entity
     *
     * @return string
     */
    public function generate(): string
    {
        $this
            ->generateProperties()
            ->generateMethods();

        return Helpers::tabsToSpaces((string) $this->entityFile);
    }
}
