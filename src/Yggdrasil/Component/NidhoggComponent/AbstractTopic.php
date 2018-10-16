<?php

namespace Yggdrasil\Component\NidhoggComponent;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampServerInterface;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;
use Yggdrasil\Core\Driver\Base\DriverCollection;

/**
 * Class AbstractTopic
 *
 * Base class for application WAMP topics
 *
 * @package Yggdrasil\Component\NidhoggComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class AbstractTopic implements TopicInterface, WampServerInterface
{
    /**
     * Trait that provides access to application drivers
     */
    use DriverAccessorTrait;

    /**
     * Allowed origins for topic
     *
     * @var array
     */
    protected $allowedOrigins;

    /**
     * Host overriding one provided for server
     *
     * @var string?
     */
    protected $host;

    /**
     * AbstractTopic constructor.
     *
     * @param DriverCollection $drivers
     */
    public function __construct(DriverCollection $drivers)
    {
        $this->drivers        = $drivers;
        $this->allowedOrigins = ['*'];
        $this->host           = null;
    }

    /**
     * An RPC call has been received
     *
     * @param ConnectionInterface $conn
     * @param string              $id     The unique ID of the RPC, required to respond to
     * @param string|Topic        $topic  The topic to execute the call against
     * @param array               $params Call parameters received from the client
     */
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params): void
    {

    }

    /**
     * A request to subscribe to a topic has been made
     *
     * @param ConnectionInterface $conn
     * @param string|Topic        $topic The topic to subscribe to
     */
    public function onSubscribe(ConnectionInterface $conn, $topic): void
    {

    }

    /**
     * A request to unsubscribe from a topic has been made
     *
     * @param ConnectionInterface $conn
     * @param string|Topic        $topic The topic to unsubscribe from
     */
    public function onUnSubscribe(ConnectionInterface $conn, $topic): void
    {

    }

    /**
     * A client is attempting to publish content to a subscribed connections on a URI
     *
     * @param ConnectionInterface $conn
     * @param string|Topic        $topic    The topic the user has attempted to publish to
     * @param string              $event    Payload of the publish
     * @param array               $exclude  A list of session IDs the message should be excluded from (blacklist)
     * @param array               $eligible A list of session Ids the message should be send to (whitelist)
     */
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible): void
    {

    }

    /**
     * Returns allowed origins for topic
     *
     * @return array
     */
    public function getAllowedOrigins(): array
    {
        return $this->allowedOrigins;
    }

    /**
     * Returns topic host
     *
     * @return string?
     */
    public function getHost(): ?string
    {
        return $this->host;
    }
}