<?php

namespace Yggdrasil\Core\Service\Utils;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\PhpFile;

/**
 * Class ServicePortGenerator
 *
 * Generates service port - request or response
 *
 * @package Yggdrasil\Core\Service\Utils
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class ServicePortGenerator
{
    /**
     * Data passed by command
     *
     * @var array
     */
    private $portData;

    /**
     * Generated service port file
     *
     * @var PhpFile
     */
    private $portFile;

    /**
     * Generated service port class
     *
     * @var ClassType
     */
    private $portClass;

    /**
     * ServicePortGenerator constructor.
     *
     * @param array $portData
     */
    public function __construct(array $portData)
    {
        $this->portData = $portData;
    }

    /**
     * Generates service port class
     *
     * @return ServicePortGenerator
     */
    private function generateClass(): ServicePortGenerator
    {
        $this->portFile = (new PhpFile())
            ->addComment('This file is auto-generated.');

        $namespace = $this->portData['namespace'] . '\\' . $this->portData['module'] . 'Module\\' . $this->portData['type'];

        $this->portClass = $this->portFile
            ->addNamespace($namespace)
            ->addUse('Yggdrasil\Core\Service\Service' . $this->portData['type'] . 'Interface')
            ->addClass($this->portData['class'] . $this->portData['type'])
            ->addImplement('Service' . $this->portData['type'] . 'Interface')
            ->addComment($this->portData['class'] . ' service ' . strtolower($this->portData['type']) . PHP_EOL)
            ->addComment('@package ' . $namespace);

        return $this;
    }

    /**
     * Generates service port properties
     *
     * @return ServicePortGenerator
     */
    private function generateProperties(): ServicePortGenerator
    {
        foreach ($this->portData['properties'] as $name => $type) {
            if ('datetime' === $type) {
                $type = '\DateTime';
            }

            $this->portClass
                ->addProperty($name)
                ->setVisibility('private')
                ->addComment($this->portData['class'] . ' ' . $name . PHP_EOL)
                ->addComment('@var ' . $type . ' $' . $name);
        }

        return $this;
    }

    /**
     * Generates service port methods
     *
     * @return ServicePortGenerator
     */
    private function generateMethods(): ServicePortGenerator
    {
        foreach ($this->portData['properties'] as $name => $type) {
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
     * @return ServicePortGenerator
     */
    private function generateGetter(string $name, string $type): ServicePortGenerator
    {
        if ('datetime' === $type) {
            $type = '\DateTime';
        }

        $this->portClass
            ->addMethod(('bool' === $type) ? 'is' . ucfirst($name) : 'get' . ucfirst($name))
            ->setVisibility('public')
            ->addComment('Returns ' . strtolower($this->portData['class']) . ' ' . $name . PHP_EOL)
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
     * @return ServicePortGenerator
     */
    private function generateSetter(string $name, string $type): ServicePortGenerator
    {
        if ('datetime' === $type) {
            $type = '\DateTime';
        }

        $setter = $this->portClass
            ->addMethod('set' . ucfirst($name))
            ->setVisibility('public')
            ->addComment('Sets ' . strtolower($this->portData['class']) . ' ' . $name . PHP_EOL)
            ->addComment('@param ' . $type . ' $' . $name)
            ->addComment('@return ' . $this->portData['class'] . $this->portData['type'])
            ->addBody('$this->' . $name . ' = $' . $name . ';' . PHP_EOL)
            ->addBody('return $this;');

        $setter
            ->addParameter($name)
            ->setTypeHint($type);

        $fullNamespaceParts = [
            $this->portData['namespace'],
            $this->portData['module'] . 'Module',
            $this->portData['type'],
            $this->portData['class'] . $this->portData['type']
        ];

        $setter->setReturnType(implode('\\', $fullNamespaceParts));

        return $this;
    }

    /**
     * Saves service port file in given path
     */
    private function saveFile(): void
    {
        $sourceCode = Helpers::tabsToSpaces((string) $this->portFile);

        $fullPath = implode('/', [
            dirname(__DIR__, 8) . '/src',
            $this->portData['namespace'],
            $this->portData['module'] . 'Module',
            $this->portData['type'],
            $this->portData['class'] . $this->portData['type'] . '.php'
        ]);

        $dirname = dirname($fullPath);

        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }

        $handle = fopen($fullPath, 'w');
        fwrite($handle, $sourceCode);
        fclose($handle);
    }

    /**
     * Generates service port
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