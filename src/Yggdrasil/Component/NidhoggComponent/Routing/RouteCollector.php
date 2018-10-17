<?php

namespace Yggdrasil\Component\NidhoggComponent\Routing;

use HaydenPierce\ClassFinder\ClassFinder;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Component\NidhoggComponent\Exception\NotTopicFoundException;
use Yggdrasil\Component\NidhoggComponent\Topic\TopicInterface;

/**
 * Class RouteCollector
 *
 * Collects routes for WampServer
 *
 * @package Yggdrasil\Component\NidhoggComponent\Routing
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
final class RouteCollector
{
    /**
     * Application Configuration
     *
     * @var ConfigurationInterface
     */
    private $appConfiguration;

    /**
     * Sets application configuration
     *
     * @param ConfigurationInterface $appConfiguration
     * @return RouteCollector
     */
    public function setConfiguration(ConfigurationInterface $appConfiguration): RouteCollector
    {
        $this->appConfiguration = $appConfiguration;

        return $this;
    }

    /**
     * Returns routes collected by topics existing in application
     *
     * @return array
     * @example route: /chat/member => ChatMemberTopic
     *
     * @throws \Exception
     * @throws \ReflectionException
     * @throws NotTopicFoundException if found object is not a topic instance
     */
    public function getRouteCollection(): array
    {
        $routeCollection = [];
        $configuration   = $this->appConfiguration->getConfiguration();
        $topics          = ClassFinder::getClassesInNamespace(rtrim($configuration['wamp']['topic_namespace'], '\\'));

        foreach ($topics as $topic) {
            $topicReflection = new \ReflectionClass($topic);
            $topicName       = $topicReflection->getName();
            $topicShortName  = $topicReflection->getShortName();

            $routeParts  = preg_split('/(?=[A-Z])/', $topicShortName);
            $topicInstance = new $topicName($this->appConfiguration->loadDrivers());

            if (!$topicInstance instanceof TopicInterface) {
                throw new NotTopicFoundException($topicShortName . ' is not a topic instance.');
            }

            $route = (new Route())
                ->setPath(implode('/', $routeParts))
                ->setTopic($topicInstance);

            $routeCollection[] = $route;
        }

        return $routeCollection;
    }
}