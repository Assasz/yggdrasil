<?php

namespace Yggdrasil\Component\TwigComponent;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class FormExtension
 *
 * Provides form extension for Twig
 *
 * @package Yggdrasil\Component\TwigComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class FormExtension extends \Twig_Extension
{
    /**
     * Returns set of functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('begin_form', [$this, 'beginForm']),
            new \Twig_Function('end_form', [$this, 'endForm']),
            new \Twig_Function('form_field', [$this, 'addFormField']),
            new \Twig_Function('csrf_token', [$this, 'generateCsrfToken']),
        ];
    }

    /**
     * Begins HTML form
     *
     * @param string $name    Form name, equivalent to ID attribute
     * @param string $action  Form action URL, equivalent to action attribute
     * @param array  $options Set of additional form attributes like [attribute_name => value]
     * @param bool   $isPjax  Form will be send with Pjax if true
     */
    public function beginForm(string $name, string $action, array $options = [], bool $isPjax = true): void
    {
        $form = '<form id="' . $name . '" action="' . $action . '" method="post"';

        if ($isPjax) {
            $form .= ' data-pjax';
        }

        foreach ($options as $attr => $value) {
            $form .= ' ' . $attr . '="' . $value . '"';
        }

        echo $form . '>';
    }

    /**
     * Ends HTML form
     *
     * @param bool $csrf CSRF token will be included if true
     *
     * @throws \Exception
     */
    public function endForm(bool $csrf = true): void
    {
        $tokenField = ($csrf) ? '<input type="hidden" id="csrf_token" name="csrf_token" value="' . $this->generateCsrfToken() . '"/>' : '';

        echo $tokenField . '</form>';
    }

    /**
     * Adds field to HTML form
     *
     * @param string $name    Form field name, equivalent to ID and name attribute
     * @param string $label   Form field label
     * @param string $type    Form field type, equivalent to type attribute
     * @param array  $options Set of additional attributes like ['wrapper'|'label'|'input' => [attribute_name => value]]
     */
    public function addFormField(string $name, string $label, string $type = 'text', array $options = []): void
    {
        $wrapperStart = '<div>';
        $wrapperEnd = '</div>';

        $labelStart = '<label for="' . $name . '"';
        $labelEnd = '>' . $label . '</label>';

        $inputStart = '<input type="' . $type . '" id="' . $name . '" name="' . $name . '"';
        $inputEnd = '>';

        foreach ($options as $element => $attrs) {
           switch ($element) {
              case 'wrapper':
                  $wrapperStart = rtrim($wrapperStart, '>');

                  foreach ($attrs as $attr => $value) {
                    $wrapperStart .= ' ' . $attr . '="' . $value . '"';
                  }

                  $wrapperStart .= '>';

                  break;
              case 'label':
                  foreach ($attrs as $attr => $value) {
                      $labelStart .= ' ' . $attr . '="' . $value . '"';
                  }

                  break;
              case 'input':
                  foreach ($attrs as $attr => $value) {
                      $inputStart .= ' ' . $attr . '="' . $value . '"';
                  }

                  break;
           }
        }

        if(in_array($type, ['checkbox', 'radio', 'file'])) {
            echo $wrapperStart . $inputStart . $inputEnd . $labelStart . $labelEnd . $wrapperEnd;

            return;
        }

        echo $wrapperStart . $labelStart . $labelEnd . $inputStart . $inputEnd . $wrapperEnd;
    }

    /**
     * Generates CSRF token
     *
     * @param int $length Number of bytes to use in generating token
     * @return string
     *
     * @throws \Exception
     */
    public function generateCsrfToken(int $length = 32): string
    {
        $token = bin2hex(random_bytes($length));

        $session = new Session();
        $session->set('csrf_token', $token);

        return $token;
    }
}