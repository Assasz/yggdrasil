<?php

namespace Yggdrasil\Utils\Service;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\PhpFile;

/**
 * Class ServiceDTOGenerator
 *
 * Generates service DTO (Data Transfer Object) - request as input port or response as output port of the service
 *
 * @package Yggdrasil\Utils\Service
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class ServiceDTOGenerator
{
    /**
     * Data passed by command
     *
     * @var array
     */
    private $inputData;

    /**
     * Generated service DTO file
     *
     * @var PhpFile
     */
    private $DTOFile;

    /**
     * Generated service DTO class
     *
     * @var ClassType
     */
    private $DTOClass;

    /**
     * ServiceDTOGenerator constructor.
     *
     * @param array $inputData
     */
    public function __construct(array $inputData)
    {
        $this->inputData = $inputData;
    }

    /**
     * Generates service DTO
     */
    public function generate(): void
    {
        $this
            ->generateClass()
            ->generateProperties()
            ->generateMethods()
            ->saveFile();
    }

    /**
     * Generates service DTO class
     *
     * @return ServiceDTOGenerator
     */
    private function generateClass(): ServiceDTOGenerator
    {
        $this->DTOFile = (new PhpFile())
            ->addComment('This file is auto-generated.');

        $namespace = $this->inputData['namespace'] . '\\' . $this->inputData['module'] . 'Module\\' . $this->inputData['type'];

        $this->DTOClass = $this->DTOFile
            ->addNamespace($namespace)
            ->addClass($this->inputData['class'] . $this->inputData['type'])
            ->addComment($this->inputData['class'] . ' service ' . strtolower($this->inputData['type']) . PHP_EOL)
            ->addComment('@package ' . $namespace);

        return $this;
    }

    /**
     * Generates service DTO properties
     *
     * @return ServiceDTOGenerator
     */
    private function generateProperties(): ServiceDTOGenerator
    {
        foreach ($this->inputData['properties'] as $name => $type) {
            if ('datetime' === $type) {
                $type = '\DateTime';
            }

            $this->DTOClass
                ->addProperty($name)
                ->setVisibility('private')
                ->addComment($this->inputData['class'] . ' ' . $name . PHP_EOL)
                ->addComment('@var ' . $type . ' $' . $name);
        }

        return $this;
    }

    /**
     * Generates service DTO methods
     *
     * @return ServiceDTOGenerator
     */
    private function generateMethods(): ServiceDTOGenerator
    {
        foreach ($this->inputData['properties'] as $name => $type) {
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
     * @return ServiceDTOGenerator
     */
    private function generateGetter(string $name, string $type): ServiceDTOGenerator
    {
        if ('datetime' === $type) {
            $type = '\DateTime';
        }

        $this->DTOClass
            ->addMethod(('bool' === $type) ? 'is' . ucfirst($name) : 'get' . ucfirst($name))
            ->setVisibility('public')
            ->addComment('Returns ' . strtolower($this->inputData['class']) . ' ' . $name . PHP_EOL)
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
     * @return ServiceDTOGenerator
     */
    private function generateSetter(string $name, string $type): ServiceDTOGenerator
    {
        if ('datetime' === $type) {
            $type = '\DateTime';
        }

        $setter = $this->DTOClass
            ->addMethod('set' . ucfirst($name))
            ->setVisibility('public')
            ->addComment('Sets ' . strtolower($this->inputData['class']) . ' ' . $name . PHP_EOL)
            ->addComment('@param ' . $type . ' $' . $name)
            ->addComment('@return ' . $this->inputData['class'] . $this->inputData['type'])
            ->addBody('$this->' . $name . ' = $' . $name . ';' . PHP_EOL)
            ->addBody('return $this;');

        $setter
            ->addParameter($name)
            ->setTypeHint($type);

        $fullNamespaceParts = [
            $this->inputData['namespace'],
            $this->inputData['module'] . 'Module',
            $this->inputData['type'],
            $this->inputData['class'] . $this->inputData['type']
        ];

        $setter->setReturnType(implode('\\', $fullNamespaceParts));

        return $this;
    }

    /**
     * Saves service DTO file in given path
     */
    private function saveFile(): void
    {
        $sourceCode = Helpers::tabsToSpaces((string) $this->DTOFile);

        $fullPath = implode('/', [
            dirname(__DIR__, 7) . '/src',
            $this->inputData['namespace'],
            $this->inputData['module'] . 'Module',
            $this->inputData['type'],
            $this->inputData['class'] . $this->inputData['type'] . '.php'
        ]);

        $dirname = dirname($fullPath);

        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }

        $handle = fopen($fullPath, 'w');
        fwrite($handle, $sourceCode);
        fclose($handle);
    }
}
