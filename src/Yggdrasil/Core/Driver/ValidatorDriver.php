<?php

namespace Yggdrasil\Core\Driver;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Yggdrasil\Core\Configuration\ConfigurationInterface;
use Yggdrasil\Core\Driver\Base\DriverInterface;
use Yggdrasil\Core\Exception\MissingConfigurationException;

/**
 * Class ValidatorDriver
 *
 * Validator driver, necessary for validation to work
 * Symfony validator is framework default validator
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class ValidatorDriver implements DriverInterface
{
    /**
     * Instance of validator
     *
     * @var RecursiveValidator
     */
    private static $validatorInstance;

    /**
     * ValidatorDriver constructor.
     *
     * Should be private to prevent object creation. Same with __clone
     */
    private function __construct() {}

    private function __clone() {}

    /**
     * Returns instance of validator
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure validator
     * @return RecursiveValidator
     *
     * @throws MissingConfigurationException if validation path is not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): RecursiveValidator
    {
        if (self::$validatorInstance === null) {
            $configuration = $appConfiguration->getConfiguration();

            if (!$appConfiguration->isConfigured(['validation_path'], 'validator')) {
                throw new MissingConfigurationException('There is missing parameter in your configuration: validation_path in validator section.');
            }

            $validator = Validation::createValidatorBuilder()
                ->addYamlMapping(dirname(__DIR__, 7) . '/src/' . $configuration['validator']['validation_path'] . '/validation.yaml')
                ->getValidator();

            self::$validatorInstance = $validator;
        }

        return self::$validatorInstance;
    }
}
