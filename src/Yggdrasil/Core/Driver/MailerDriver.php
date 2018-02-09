<?php

namespace Yggdrasil\Core\Driver;

use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

class MailerDriver implements DriverInterface
{
    private static $mailerInstance;

    private function __construct(){}

    private function __clone(){}

    public static function getInstance(ConfigurationInterface $appConfiguration)
    {
        if(self::$mailerInstance === null){
            $configuration = $appConfiguration->getConfiguration();

            if(!$appConfiguration->isConfigured(['host', 'username', 'password'], 'mailer')){
                throw new MissingConfigurationException('There are missing parameters in your configuration. host, username and password are required to make mailer work.');
            }

            $transport = new \Swift_SmtpTransport($configuration['mailer']['host'], $configuration['mailer']['port'] ?? 465, $configuration['mailer']['encryption'] ?? 'ssl');
            $transport->setUsername($configuration['mailer']['username'])
                ->setPassword($configuration['mailer']['password']);

            $mailer = new \Swift_Mailer($transport);

            self::$mailerInstance = $mailer;
        }

        return self::$mailerInstance;
    }
}