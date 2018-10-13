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
 * [Symfony Validator] Validator driver
 *
 * @package Yggdrasil\Core\Driver
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
abstract class ValidatorDriver implements DriverInterface
{
    /**
     * Instance of validator
     *
     * @var RecursiveValidator
     */
    protected static $validatorInstance;

    /**
     * Returns instance of validator
     *
     * @param ConfigurationInterface $appConfiguration Configuration needed to configure validator
     * @return RecursiveValidator
     *
     * @throws MissingConfigurationException if resource_path is not configured
     */
    public static function getInstance(ConfigurationInterface $appConfiguration): RecursiveValidator
    {
        if (self::$validatorInstance === null) {
            if (!$appConfiguration->isConfigured(['resource_path'], 'validator')) {
                throw new MissingConfigurationException(['resource_path'], 'validator');
            }

            $configuration = $appConfiguration->getConfiguration();

            $validationPath = dirname(__DIR__, 7) .
                '/src/' . $configuration['validator']['resource_path'] . '/validation.yaml';

            $validator = Validation::createValidatorBuilder()
                ->addYamlMapping($validationPath)
                ->getValidator();

            self::$validatorInstance = $validator;
        }

        return self::$validatorInstance;
    }
}
