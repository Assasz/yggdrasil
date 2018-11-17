<?php

namespace Yggdrasil\Core\Entity;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\PhpFile;

/**
 * Class EntityGenerator
 *
 * Generates basic domain entity
 *
 * @package Yggdrasil\Core\Entity
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
     * @param array $entityData
     */
    public function __construct(array $entityData)
    {
        $this->entityData = $entityData;
    }

    /**
     * Generates entity class
     *
     * @return EntityGenerator
     */
    private function generateClass(): EntityGenerator
    {
        $this->entityFile = (new PhpFile())
            ->addComment('This file is auto-generated.');

        $this->entityClass = $this->entityFile
            ->addNamespace($this->entityData['namespace'])
            ->addClass($this->entityData['class'])
            ->addComment($this->entityData['class'] . ' entity' . PHP_EOL)
            ->addComment('@package ' . $this->entityData['namespace']);

        return $this;
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
            ->addComment('@var int $id');

        foreach ($this->entityData['properties'] as $name => $type) {
            if ('datetime' === $type) {
                $type = '\DateTime';
            }

            $this->entityClass
                ->addProperty($name)
                ->setVisibility('private')
                ->addComment($this->entityData['class'] . ' ' . $name . PHP_EOL)
                ->addComment('@var ' . $type . ' $' . $name);
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
            ->addBody('return $this->id;')
            ->setReturnType('int');

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
        if ('datetime' === $type) {
            $type = '\DateTime';
        }

        $this->entityClass
            ->addMethod('get' . ucfirst($name))
            ->setVisibility('public')
            ->addComment('Returns ' . strtolower($this->entityData['class']) . ' ' . $name . PHP_EOL)
            ->addComment('@return ' . $type)
            ->setReturnType($type)
            ->addBody('return $this->' . $name . ';');

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
        if ('datetime' === $type) {
            $type = '\DateTime';
        }

        $setter = $this->entityClass
            ->addMethod('set' . ucfirst($name))
            ->setVisibility('public')
            ->addComment('Sets ' . strtolower($this->entityData['class']) . ' ' . $name . PHP_EOL)
            ->addComment('@param ' . $type . ' $' . $name)
            ->addComment('@return ' . ucfirst($this->entityData['class']))
            ->addBody('$this->' . $name . ' = $' . $name . ';' . PHP_EOL)
            ->addBody('return $this;')
            ->setReturnType($this->entityData['namespace'] . '\\' . ucfirst($this->entityData['class']));

        $setter
            ->addParameter($name)
            ->setTypeHint($type);

        return $this;
    }

    /**
     * Saves entity file in given path
     */
    private function saveFile(): void
    {
        $sourceCode = Helpers::tabsToSpaces((string) $this->entityFile);
        $entityPath = dirname(__DIR__, 7) . '/src/';

        $handle = fopen($entityPath . $this->entityData['class'] . '.php', 'w');
        fwrite($handle, $sourceCode);
        fclose($handle);
    }

    /**
     * Generates entity
     */
    public function generate(): void
    {
        $this
            ->generateClass()
            ->generateProperties()
            ->generateMethods()
            ->saveFile();
    }
}
