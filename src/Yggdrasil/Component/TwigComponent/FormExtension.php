<?php

namespace Yggdrasil\Component\TwigComponent;

use HtmlGenerator\HtmlTag;
use HtmlGenerator\Markup;
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
            new \Twig_Function('select_list', [$this, 'addSelectList']),
            new \Twig_Function('button', [$this, 'addButton']),
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

        $form .= ((bool) $options['is_pjax'] ?? true) ? ' data-pjax' : '';

        if (isset($options['is_pjax'])) {
            unset($options['is_pjax']);
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
     * @param string $name        Form field name, equivalent to ID and name attribute
     * @param array  $options     Set of additional attributes [wrapper|label|input|caption => [attribute_name => value]]
     */
    public function addFormField(string $name, array $options = []): void
    {
        $wrapper = HtmlTag::createElement('div');

        $label = '';

        if (isset($options['label']['text'])) {
            $label = $this->addLabel($options['label']['text'], $name);

            unset($options['label']['text']);
        }

        $input = HtmlTag::createElement('input')
            ->set('type', $options['input']['type'] ?? 'text')
            ->set('id', $name)
            ->set('name', $name);

        $caption = '';

        if (isset($options['caption']['text'])) {
            $input->set('aria-describedby', $name . '_caption');
            $caption = $this->addCaption($options['caption']['text'], $name);

            unset($options['caption']['text']);
        }

        $elements = ['wrapper', 'label', 'input', 'caption'];

        foreach ($options as $option => $attrs) {
            foreach ($elements as $element) {
                if ($element === $option) {
                    if (in_array($element, ['label', 'caption']) && empty($$element)) {
                        continue;
                    }

                    foreach ($attrs as $attr => $value) {
                        $$element->set($attr, $value);
                    }
                }
            }
        }

        if (in_array($options['input']['type'] ?? 'text', ['checkbox', 'radio', 'file'])) {
            $wrapper->addElement($input);
            $wrapper->addElement($label);
            $wrapper->addElement($caption);

            echo $wrapper;

            return;
        }

        $wrapper->addElement($label);
        $wrapper->addElement($input);
        $wrapper->addElement($caption);

        echo $wrapper;
    }

    /**
     * Adds select list to HTML form
     *
     * @param string $name        Select list name, equivalent to ID attribute
     * @param array  $options     Set of additional attributes [wrapper|label|list|item|caption => [attribute_name => value]]
     *                            List items define as [list => [items => [value => text]]]
     */
    public function addSelectList(string $name, array $options = []): void
    {
        $wrapper = HtmlTag::createElement('div');

        $label = '';

        if (isset($options['label']['text'])) {
            $label = $this->addLabel($options['label']['text'], $name);

            unset($options['label']['text']);
        }

        $selectList = HtmlTag::createElement('select')
            ->set('id', $name);

        $caption = '';

        if (isset($options['caption']['text'])) {
            $selectList->set('aria-describedby', $name . '_caption');
            $caption = $this->addCaption($options['caption']['text'], $name);

            unset($options['caption']['text']);
        }

        $items = [];

        foreach ($options['list']['items'] ?? [] as $value => $text) {
            $items[] = $selectList->addElement('option')
                ->set('value', $value)
                ->text($text);
        }

        if (isset($options['list']['items'])) {
            unset($options['list']['items']);
        }

        $elements = ['wrapper', 'label', 'list', 'item', 'caption'];

        foreach ($options as $element => $attrs) {
            foreach ($elements as $element) {
                if (in_array($element, ['label', 'caption']) && empty($$element)) {
                    continue;
                }

                if ('item' === $element) {
                    foreach ($items as $item) {
                        foreach ($attrs as $attr => $value) {
                            $item->set($attr, $value);
                        }
                    }

                    continue;
                }

                foreach ($attrs as $attr => $value) {
                    $$element->set($attr, $value);
                }
            }
        }

        $wrapper->addElement($label);
        $wrapper->addElement($selectList);
        $wrapper->addElement($caption);

        echo $wrapper;
    }

    /**
     * Adds button to HTML form
     *
     * @param string $name    Button name, equivalent to ID attribute
     * @param string $text    Button text
     * @param array  $options Set of additional button attributes [attribute_name => value]
     */
    public function addButton(string $name, string $text, array $options = []): void
    {
        $button = HtmlTag::createElement('button')
            ->set('id', $name)
            ->set('type', $options['type'] ?? 'button')
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

    /**
     * Adds label to form element
     *
     * @param string $text Label text
     * @param string $name Element name
     * @return Markup
     */
    private function addLabel(string $text, string $name): Markup
    {
        return HtmlTag::createElement('label')
            ->set('for', $name)
            ->text($text);
    }

    /**
     * Adds caption to form element
     *
     * @param string $text Caption text
     * @param string $name Element name
     * @return Markup
     */
    private function addCaption(string $text, string $name): Markup
    {
        return HtmlTag::createElement('small')
            ->set('id', $name . '_caption')
            ->text($text);
    }
}