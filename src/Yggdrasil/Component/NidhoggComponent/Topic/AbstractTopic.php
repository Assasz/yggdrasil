<?php

namespace Yggdrasil\Component\NidhoggComponent\Topic;

use Psr\Http\Message\RequestInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServerInterface;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampServerInterface;
use Yggdrasil\Core\Driver\Base\DriverAccessorTrait;
use Yggdrasil\Core\Driver\Base\DriverCollection;

/**
 * Class AbstractTopic
 *
 * Base class for application WAMP topics
 *
 * @package Yggdrasil\Component\NidhoggComponent\Topic
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class AbstractTopic implements TopicInterface, WampServerInterface, HttpServerInterface
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
     * When a new connection is opened it will be passed to this method
     *
     * @param ConnectionInterface $conn The socket/connection that just connected to your application
     * @param RequestInterface $request
     *
     * @throws \Exception
     */
    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null): void
    {
        echo "New connection! ({$conn->resourceId})\n";
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     *
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     *
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn): void
    {
        echo "{$conn->resourceId} closed connection.\n";
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     *
     * @param  ConnectionInterface $conn
     * @param  \Exception          $e
     *
     * @throws \Exception
     */
    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        echo "Error: {$e->getMessage()}";
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