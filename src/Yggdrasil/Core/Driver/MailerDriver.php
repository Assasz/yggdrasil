<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class MailerDriver
 *
 * [SwiftMailer] Mailer driver
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class MailerDriver implements DriverInterface
{
    /**
     * Instance of mailer
     *
     * @var \Swift_Mailer
     */
    protected static $mailerInstance;

    /**
     * Returns instance of mailer
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure mailer
     * @return \Swift_Mailer
     *
     * @throws MissingConfigurationException if host, username or password are not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): \Swift_Mailer
    {
        if (self::$mailerInstance === null) {
            if (!$appConfiguration->isConfigured(['host', 'username', 'password'], 'mailer')) {
                throw new MissingConfigurationException('There are missing parameters in your configuration. host, username or password in section mailer');
            }

            $configuration = $appConfiguration->getConfiguration();

            $transport = new \Swift_SmtpTransport(
                $configuration['mailer']['host'],
                $configuration['mailer']['port'] ?? 465,
                $configuration['mailer']['encryption'] ?? 'ssl'
            );

            $transport
                ->setUsername($configuration['mailer']['username'])
                ->setPassword($configuration['mailer']['password']);

            $mailer = new \Swift_Mailer($transport);

            self::$mailerInstance = $mailer;
        }

        return self::$mailerInstance;
    }
}
