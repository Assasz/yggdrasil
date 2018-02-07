<?php

namespace Yggdrasil\Core\Driver;

use Symfony\Component\Validator\Validation;

class ValidatorDriver implements DriverInterface
{
    private static $validatorInstance;

    private function __construct(){}

    private function __clone(){}

    public static function getInstance($configuration)
    {
        if(self::$validatorInstance === null){
            $validator = Validation::createValidatorBuilder()
                ->addYamlMapping(dirname(__DIR__, 7).'/src/AppModule/Infrastructure/Resource/Validation/validation.yaml')
                ->getValidator();

            self::$validatorInstance = $validator;
        }

        return self::$validatorInstance;
    }
}