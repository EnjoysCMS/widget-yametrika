<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika\Widgets;


use EnjoysCMS\Core\Block\Annotation\Widget;
use EnjoysCMS\WidgetYaMetrika\YaMetrikaAbstractWidget;
use InvalidArgumentException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Widget(
    name: 'Поисковые системы',
    options: [
        'title' => 'Поисковые системы',
        'yandex-counter-id' => null,
        'cache' => 1800,
        'days' => [
            'value' => 30,
            'title' => 'Период в днях',
            'description' => 'За какое количество дней получить статистику'
        ],
        'limit' => 10,
        'chart' => [
            'value' => 'highcharts_js',
            'type' => 'select',
            'data' => ['highcharts_js']
        ],
        'background' => [
            'value' => 'white',
            'type' => 'select',
            'data' => ['light', 'white', 'transparent']
        ],
        'gs' => [
            'min-h' => 15,
            'min-w' => 3,
            'h' => 21,
            'w' => 5,
        ]
    ]
)]
final class SE extends YaMetrikaAbstractWidget
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function view(): string
    {
        $template = sprintf('@metrika/%s/search_engines.twig', $this->getBlockOptions()->getValue('chart') ?? '');

        if (!$this->twig->getLoader()->exists($template)) {
            throw new InvalidArgumentException(sprintf('%s not exist', $template));
        }
        return $this->twig->render($template, [
            'widget' => $this->getEntity(),
        ]);
    }
}
