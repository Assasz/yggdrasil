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
            new \Twig_Function('text_area', [$this, 'addTextArea']),
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
        $this->formOptions = $this->getFormOptions($name);

        $form = "<form id=\"{$name}\" action=\"{$action}\" method=\"post\"";

        $usePjax = (isset($this->formOptions['use_pjax'])) ?
            filter_var($this->formOptions['use_pjax'], FILTER_VALIDATE_BOOLEAN)
            : true;

        $pjaxAttr = ($usePjax) ? ' data-pjax' : '';

        foreach ($this->formOptions as $attr => $value) {
            if (in_array($attr, ['use_pjax', 'use_csrf', 'fields'])) {
                continue;
            }

            $form .= " {$attr}=\"{$value}\"";
        }

        echo $form . $pjaxAttr . '>';
    }

    /**
     * Ends HTML form
     *
     * @throws \Exception
     */
    public function endForm(): void
    {
        $useCsrf = (isset($this->formOptions['use_csrf'])) ?
            filter_var($this->formOptions['use_csrf'], FILTER_VALIDATE_BOOLEAN)
            : true;

        $tokenField = ($useCsrf) ? HtmlTag::createElement('input')
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
     * @param string $name Form field name, equivalent to ID and name attributes
     */
    public function addFormField(string $name): void
    {
        $options = $this->formOptions['fields'][$name];

        $wrapper = (isset($options['wrapper'])) ? $this->createWrapper() : '';

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

        $elements = [
            'wrapper' => $wrapper,
            'label' => $label,
            'input' => $input,
            'caption' => $caption
        ];

        foreach ($elements as $name => $element) {
            $$name = $this->assignOptionsToElement($options, $name, $element);
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
     * Adds textarea to HTML form
     *
     * @param string $name Textarea name, equivalent to ID and name attributes
     */
    public function addTextArea(string $name): void
    {
        $options = $this->formOptions['fields'][$name];

        $wrapper = (isset($options['wrapper'])) ? $this->createWrapper() : '';

        $label = '';

        if (isset($options['label']['text'])) {
            $label = $this->createLabel($options['label']['text'], $name);

            unset($options['label']['text']);
        }

        $textarea = HtmlTag::createElement('textarea')
            ->set('id', $name)
            ->set('name', $name);

        $caption = '';

        if (isset($options['caption']['text'])) {
            $textarea->set('aria-describedby', $name . '_caption');
            $caption = $this->createCaption($options['caption']['text'], $name);

            unset($options['caption']['text']);
        }

        $elements = [
            'wrapper' => $wrapper,
            'label' => $label,
            'textarea' => $textarea,
            'caption' => $caption
        ];

        foreach ($elements as $name => $element) {
            $$name = $this->assignOptionsToElement($options, $name, $element);
        }

        $wrapper->addElement($label);
        $wrapper->addElement($textarea);
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

        $wrapper = (isset($options['wrapper'])) ? $this->createWrapper() : '';

        $label = '';

        if (isset($options['label']['text'])) {
            $label = $this->createLabel($options['label']['text'], $name);

            unset($options['label']['text']);
        }

        $list = HtmlTag::createElement('select')
            ->set('id', $name)
            ->set('name', $name);

        $caption = '';

        if (isset($options['caption']['text'])) {
            $list->set('aria-describedby', $name . '_caption');
            $caption = $this->createCaption($options['caption']['text'], $name);

            unset($options['caption']['text']);
        }

        foreach ($items as $value => $text) {
            $item[] = $list->addElement('option')
                ->set('value', $value)
                ->text($text);
        }

        $elements = [
            'wrapper' => $wrapper,
            'label' => $label,
            'list' => $list,
            'item' => $item ?? [],
            'caption' => $caption
        ];

        foreach ($elements as $name => $element) {
            $$name = $this->assignOptionsToElement($options, $name, $element);
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
     * Assigns options to form field element and returns ready markup
     *
     * @param array        $options       Form options for given field
     * @param string       $elementName   Form field element name, e.g. label
     * @param Markup|array $elementMarkup Form field element markup, or array of markup objects (select list items)
     * @return Markup|array
     */
    private function assignOptionsToElement(array $options, string $elementName, $elementMarkup)
    {
        if (in_array($elementName, ['label', 'caption', 'wrapper']) && empty($elementMarkup)) {
            return $elementMarkup;
        }

        foreach ($options[$elementName] as $attr => $value) {
            if ('item' === $elementName) {
                foreach ($elementMarkup as $item) {
                    $item->set($attr, $value);
                }

                return $elementMarkup;
            }

            $elementMarkup->set($attr, $value);
        }

        return $elementMarkup;
    }

    /**
     * Returns form options from resource
     *
     * @param string $formName Name of form
     * @return array
     */
    private function getFormOptions(string $formName): array
    {
        return Yaml::parseFile($this->resourcePath . '/' . $formName . '.yaml');
    }
}