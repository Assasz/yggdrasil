<?php

namespace Yggdrasil\Component\TwigComponent;

use HtmlGenerator\HtmlTag;
use Yggdrasil\Core\Debug\DebugProfiler;

/**
 * Class DebugExtension
 *
 * Provides debug extension for Twig
 *
 * @package Yggdrasil\Component\TwigComponent
 * @author Paweł Antosiak <contact@pawelantosiak.com>
 */
class DebugExtension extends \Twig_Extension
{
    /**
     * Instance of debug profiler
     *
     * @var DebugProfiler
     */
    private $profiler;

    /**
     * DebugExtension constructor.
     *
     * @param DebugProfiler $profiler
     */
    public function __construct(DebugProfiler $profiler)
    {
        $this->profiler = $profiler;
    }

    /**
     * Returns set of functions
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_Function('render_debugbar', [$this, 'renderDebugBar'])
        ];
    }

    /**
     * Renders debug bar
     */
    public function renderDebugBar(): void
    {
        $data = $this->profiler->getCollectedData();

        $debugBar = HtmlTag::createElement('div')
            ->set('id', 'debug_bar');

        if (in_array('requestCollector', $data)) {
            $requestDataList = HtmlTag::createElement('ul')
                ->set('id', 'request_data_list');

            foreach ($data['requestCollector'] as $dataGroup => $dataValues) {
                $dataGroupList = HtmlTag::createElement('ul')
                    ->set('id', $dataGroup . '_list');

                foreach ($dataValues as $key => $value) {
                    $listItem = HtmlTag::createElement('li')
                        ->text($key . ': ' . $value);

                    $dataGroupList->addElement($listItem);
                }

                $requestDataList->addElement($dataGroupList);
            }

            $debugBar->addElement($requestDataList);
        }

        echo $debugBar;
    }
}