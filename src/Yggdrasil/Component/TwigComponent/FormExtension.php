<?php

namespace Yggdrasil\Component\TwigComponent;

use HtmlGenerator\HtmlTag;
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
            new \Twig_Function('button', [$this, 'addButton']),
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

        $form .= ($isPjax) ? ' data-pjax' : '';

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
        $tokenField = ($csrf) ? HtmlTag::createElement('input')
            ->set('id', 'csrf_token')
            ->set('name', 'csrf_token')
            ->set('type', 'hidden')
            ->set('value', $this->generateCsrfToken())
            : '';

        echo $tokenField . '</form>';
    }

    /**
     * Adds field to HTML form
     *
     * @param string $name      Form field name, equivalent to ID and name attribute
     * @param string $labelText Form field label text
     * @param string $type      Form field type, equivalent to type attribute
     * @param array  $options   Set of additional attributes like [wrapper|label|input => [attribute_name => value]]
     */
    public function addFormField(string $name, string $labelText = '', string $type = 'text', array $options = []): void
    {
        $wrapper = HtmlTag::createElement('div');

        $label = (!empty($labelText)) ? HtmlTag::createElement('label')
            ->set('for', $name)
            ->text($labelText)
            : '';

        $input = HtmlTag::createElement('input')
            ->set('type', $type)
            ->set('id', $name)
            ->set('name', $name);

        foreach ($options as $element => $attrs) {
           switch ($element) {
              case 'wrapper':
                  foreach ($attrs as $attr => $value) {
                      $wrapper->set($attr, $value);
                  }

                  break;
              case 'label':
                  if (empty($label)) {
                      break;
                  }

                  foreach ($attrs as $attr => $value) {
                      $label->set($attr, $value);
                  }

                  break;
              case 'input':
                  foreach ($attrs as $attr => $value) {
                      $input->set($attr, $value);
                  }

                  break;
           }
        }

        if(in_array($type, ['checkbox', 'radio', 'file'])) {
            $wrapper = $wrapper->addElement($input);

            echo $wrapper->addElement($label);

            return;
        }

        $wrapper = $wrapper->addElement($label);

        echo $wrapper->addElement($input);
    }

    /**
     * Adds button to HTML form
     *
     * @param string $name    Button name, equivalent to ID attribute
     * @param string $text    Button text
     * @param string $type    Button type, equivalent to type attribute
     * @param array  $options Set of additional button attributes like [attribute_name => value]
     */
    public function addButton(string $name, string $text, string $type = 'button', array $options = []): void
    {
        $button = HtmlTag::createElement('button')
            ->set('id', $name)
            ->set('type', $type)
            ->text($text);

        foreach ($options as $attr => $value) {
            $button->set($attr, $value);
        }

        echo $button;
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