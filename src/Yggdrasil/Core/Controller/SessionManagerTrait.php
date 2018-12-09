<?php

namespace Yggdrasil\Core\Controller;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Trait SessionManagerTrait
 *
 * Makes session management easy
 *
 * @package Yggdrasil\Core\Controller
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
trait SessionManagerTrait
{
    /**
     * Starts user session and returns session instance
     *
     * @param object $user Authenticated user instance
     * @return Session
     */
    protected function startUserSession(object $user): Session
    {
        $session = new Session();

        $session->set('is_granted', true);
        $session->set('user', $user);

        return $session;
    }

    /**
     * Checks if user is authenticated
     *
     * @return bool
     */
    protected function isGranted(): bool
    {
        return (new Session())->get('is_granted', false);
    }

    /**
     * Returns authenticated user instance from session
     *
     * @return object
     */
    protected function getUser(): object
    {
        return (new Session())->get('user');
    }

    /**
     * Adds flash to session flash bag
     *
     * @param string       $type    Type of flash bag
     * @param string|array $message Message of flash
     */
    protected function addFlash(string $type, $message): void
    {
        (new Session())->getFlashBag()->set($type, $message);
    }

    /**
     * Clears all session data and regenerates session ID
     */
    protected function invalidateSession(): void
    {
        (new Session())->invalidate();
    }
}
