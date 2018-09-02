<?php

namespace Yggdrasil\Component\TwigComponent;

use HtmlGenerator\HtmlTag;
use HtmlGenerator\Markup;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Yaml\Yaml;

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
     * Form name
     *
     * @var string
     */
    private $formName;

    /**
     * Form options
     *
     * @var array
     */
    private $formOptions;

    /**
     * Path to forms configuration resources
     *
     * @var string
     */
    private $resourcePath;

    /**
     * FormExtension constructor.
     *
     * @param string $resourcePath
     */
    public function __construct(string $resourcePath)
    {
        $this->resourcePath = $resourcePath;
    }

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
     */
    public function beginForm(string $name, string $action): void
    {
        $this->formName = $name;
        $this->formOptions = $this->getFormOptions();

        $form = '<form id="' . $name . '" action="' . $action . '" method="post"';

        $pjaxAttr = ' data-pjax';

        if (isset($this->formOptions['is_pjax'])) {
            $pjaxAttr = (filter_var($this->formOptions['is_pjax'], FILTER_VALIDATE_BOOLEAN)) ?: '';
        }

        foreach ($this->formOptions as $attr => $value) {
            if (in_array($attr, ['is_pjax', 'fields'])) {
                continue;
            }

            $form .= ' ' . $attr . '="' . $value . '"';
        }

        echo $form . $pjaxAttr . '>';
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

        $this->formName = null;
    }

    /**
     * Adds field to HTML form
     *
     * @param string $name Form field name, equivalent to ID and name attributes
     */
    public function addFormField(string $name): void
    {
        $options = $this->formOptions['fields'][$name];

        $wrapper = $this->createWrapper();

        $label = '';

        if (isset($options['label']['text'])) {
            $label = $this->createLabel($options['label']['text'], $name);

            unset($options['label']['text']);
        }

        $input = HtmlTag::createElement('input')
            ->set('type', $options['input']['type'] ?? 'text')
            ->set('id', $name)
            ->set('name', $name);

        $caption = '';

        if (isset($options['caption']['text'])) {
            $input->set('aria-describedby', $name . '_caption');
            $caption = $this->createCaption($options['caption']['text'], $name);

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
        } else {
            $wrapper->addElement($label);
            $wrapper->addElement($input);
        }

        $wrapper->addElement($caption);

        echo $wrapper;
    }

    /**
     * Adds select list to HTML form
     *
     * @param string $name  Select list name, equivalent to ID attribute
     * @param array  $items Select list items [value => text]
     */
    public function addSelectList(string $name, array $items = []): void
    {
        $options = $this->formOptions['fields'][$name];

        $wrapper = $this->createWrapper();

        $label = '';

        if (isset($options['label']['text'])) {
            $label = $this->createLabel($options['label']['text'], $name);

            unset($options['label']['text']);
        }

        $list = HtmlTag::createElement('select')
            ->set('id', $name);

        $caption = '';

        if (isset($options['caption']['text'])) {
            $list->set('aria-describedby', $name . '_caption');
            $caption = $this->createCaption($options['caption']['text'], $name);

            unset($options['caption']['text']);
        }

        $itemsElements = [];

        foreach ($items as $value => $text) {
            $itemsElements[] = $list->addElement('option')
                ->set('value', $value)
                ->text($text);
        }

        $elements = ['wrapper', 'label', 'list', 'item', 'caption'];

        foreach ($options as $element => $attrs) {
            foreach ($elements as $element) {
                if (in_array($element, ['label', 'caption']) && empty($$element)) {
                    continue;
                }

                if ('item' === $element) {
                    foreach ($itemsElements as $item) {
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
        $wrapper->addElement($list);
        $wrapper->addElement($caption);

        echo $wrapper;
    }

    /**
     * Adds button to HTML form
     *
     * @param string $name    Button name, equivalent to ID attribute
     */
    public function addButton(string $name): void
    {
        $options = $this->formOptions['fields'][$name];

        $button = HtmlTag::createElement('button')
            ->set('id', $name)
            ->set('type', $options['type'] ?? 'button')
            ->text($options['text'] ?? '');

        if (isset($options['text'])) {
            unset($options['text']);
        }

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
     * Creates wrapper for form field
     *
     * @return Markup
     */
    private function createWrapper(): Markup
    {
        return HtmlTag::createElement('div');
    }

    /**
     * Creates label for form field
     *
     * @param string $text Label text
     * @param string $name Element name
     * @return Markup
     */
    private function createLabel(string $text, string $name): Markup
    {
        return HtmlTag::createElement('label')
            ->set('for', $name)
            ->text($text);
    }

    /**
     * Creates caption for form field
     *
     * @param string $text Caption text
     * @param string $name Element name
     * @return Markup
     */
    private function createCaption(string $text, string $name): Markup
    {
        return HtmlTag::createElement('small')
            ->set('id', $name . '_caption')
            ->text($text);
    }

    /**
     * Returns form options from resource
     *
     * @return array
     */
    private function getFormOptions(): array
    {
        return Yaml::parseFile($this->resourcePath . '/' . $this->formName . '.yaml');
    }
}