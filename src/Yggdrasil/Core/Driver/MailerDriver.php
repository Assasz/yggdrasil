<?php

namespace Yggdrasil\Core\Driver;

use AppModule\Infrastructure\Config\AppConfiguration;
use Yggdrasil\Core\Driver\Base\DriverInterface;

class MailerDriver implements DriverInterface
{
    private static $mailerInstance;

    private function __construct(){}

    private function __clone(){}

    public static function getInstance(AppConfiguration $appConfiguration)
    {
        if(self::$mailerInstance === null){
            $configuration = $appConfiguration->getConfiguration();

            if(!$appConfiguration->isConfigured(['host', 'username', 'password'], 'mailer')){
                //exception
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