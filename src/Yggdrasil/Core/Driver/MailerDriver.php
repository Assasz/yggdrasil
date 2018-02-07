<?php

namespace Yggdrasil\Core\Driver;

class MailerDriver implements DriverInterface
{
    private static $mailerInstance;

    private function __construct(){}

    private function __clone(){}

    public static function getInstance($configuration)
    {
        if(self::$mailerInstance === null){
            $transport = new \Swift_SmtpTransport($configuration['mailer']['host'], $configuration['mailer']['port'], $configuration['mailer']['encryption']);
            $transport->setUsername($configuration['mailer']['username'])
                ->setPassword($configuration['mailer']['password']);

            $mailer = new \Swift_Mailer($transport);

            self::$mailerInstance = $mailer;
        }

        return self::$mailerInstance;
    }
}