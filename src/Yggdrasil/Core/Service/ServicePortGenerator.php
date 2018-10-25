<?php

namespace Yggdrasil\Core\Service;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\PhpFile;

/**
 * Class ServicePortGenerator
 *
 * Generates service port - request or response
 *
 * @package Yggdrasil\Core\Service
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
            ->addClass($this->portData['class'] . $this->portData['type'])
            ->addImplement('Yggdrasil\Core\Service\Service' . $this->portData['type'] . 'Interface')
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
            $property = $this->portClass
                ->addProperty($name)
                ->setVisibility('private')
                ->addComment($this->portData['class'] . ' ' . $name . PHP_EOL);

            switch ($type) {
                case in_array($type, ['string', 'int', 'float']):
                    $property->addComment('@var ' . $type . ' $' . $name);
                    break;
                case 'datetime':
                    $property->addComment('@var \DateTime $' . $name);
                    break;
            }
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
        $getter = $this->portClass
            ->addMethod('get' . ucfirst($name))
            ->setVisibility('public')
            ->addComment('Returns ' . strtolower($this->portData['class']) . ' ' . $name . PHP_EOL);

        switch (true) {
            case in_array($type, ['string', 'int', 'float']):
                $getter
                    ->addComment('@return ' . $type)
                    ->setReturnType($type);
                break;
            case 'datetime' === $type:
                $getter
                    ->addComment('@return \DateTime')
                    ->setReturnType('\DateTime');
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
     * @return ServicePortGenerator
     */
    private function generateSetter(string $name, string $type): ServicePortGenerator
    {
        $setter = $this->portClass
            ->addMethod('set' . ucfirst($name))
            ->setVisibility('public')
            ->addComment('Sets ' . strtolower($this->portData['class']) . ' ' . $name . PHP_EOL);

        switch (true) {
            case in_array($type, ['string', 'int', 'float']):
                $setter
                    ->addComment('@param ' . $type . ' $' . $name)
                    ->addComment('@return ' . $this->portData['class'] . $this->portData['type'])
                    ->addParameter($name)
                    ->setTypeHint($type);
                break;
            case 'datetime' === $type:
                $setter
                    ->addComment('@param \DateTime $' . $name)
                    ->addComment('@return ' . $this->portData['class'] . $this->portData['type'])
                    ->addParameter($name)
                    ->setTypeHint('\DateTime');
                break;
        }

        $fullNamespaceParts = [
            $this->portData['namespace'],
            $this->portData['module'] . 'Module',
            $this->portData['type'],
            $this->portData['class'] . $this->portData['type']
        ];

        $setter
            ->addBody('$this->' . $name . ' = $' . $name . ';' . PHP_EOL)
            ->addBody('return $this;')
            ->setReturnType(implode('\\', $fullNamespaceParts));

        return $this;
    }

    /**
     * Saves service port file in given path
     */
    private function saveFile(): void
    {
        $sourceCode = Helpers::tabsToSpaces((string) $this->portFile);
        $portPath = dirname(__DIR__, 7) . '/src/' . $this->portData['namespace'] . '/' . $this->portData['module'] . 'Module/' . $this->portData['type'] . '/';

        $handle = fopen($portPath . $this->portData['class'] . $this->portData['type'] . '.php', 'w');
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