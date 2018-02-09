<?php

namespace Yggdrasil\Core\Driver;

use Symfony\Component\Validator\Validation;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;

class ValidatorDriver implements DriverInterface
{
    private static $validatorInstance;

    private function __construct(){}

    private function __clone(){}

    public static function getInstance(ConfigurationInterface $appConfiguration)
    {
        if(self::$validatorInstance === null){
            $configuration = $appConfiguration->getConfiguration();
            $validator = Validation::createValidatorBuilder()
                ->addYamlMapping(dirname(__DIR__, 7).'/src/'.$configuration['application']['validation_path'].'/validation.yaml')
                ->getValidator();

            self::$validatorInstance = $validator;
        }

        return self::$validatorInstance;
    }
}