<?php

namespace Yggdrasil\Utils\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Utils\Annotation\Services;

/**
 * Trait ServiceAwareTrait
 *
 * Enables services installation
 *
 * @package Yggdrasil\Utils\Service
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
trait ServiceAwareTrait
{
    /**
     * Installs services enabled by class annotation by generating magic properties
     * Hint type of these properties by using '@property' tag
     *
     * @param ConfigurationInterface $configuration
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    protected function installServicesIfEnabled(ConfigurationInterface $configuration): void
    {
        $reflection = new \ReflectionClass($this);
        $reader = new AnnotationReader();

        $annotation = $reader->getClassAnnotation($reflection, Services::class);

        if (!$annotation instanceof Services) {
            return;
        }

        foreach ($annotation->install as $service) {
            $serviceReflection = new \ReflectionClass($service);
            $prefix = lcfirst(str_replace('Module', '', explode('\\', $serviceReflection->getNamespaceName())[3]));

            $this->{$prefix . $serviceReflection->getShortName()} = new $service($configuration);
        }
    }
}
