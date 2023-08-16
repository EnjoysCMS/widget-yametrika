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
    name: 'Посещаемость',
    options: [
        'title' => 'Посещаемость',
        'yandex-counter-id' => null,
        'cache' => 1800,
        'days' => [
            'value' => 30,
            'title' => 'Период в днях',
            'description' => 'За какое количество дней получить статистику'
        ],
        'chart' => [
            'value' => 'highcharts_js',
            'type' => 'select',
            'data' => ['highcharts_js', 'amcharts_js', 'google_charts']
        ],
        'background' => [
            'value' => 'white',
            'type' => 'select',
            'data' => ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'white', 'transparent']
        ],
        'gs' => [
            'min-h' => 15,
            'min-w' => 5,
            'max-w' => 12,
        ]
    ]
)]
final class Visits extends YaMetrikaAbstractWidget
{

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function view(): string
    {
        $template = sprintf('@metrika/%s/visits.twig', $this->getBlockOptions()->getValue('chart'));

        if (!$this->twig->getLoader()->exists($template)) {
            throw new InvalidArgumentException(sprintf('%s not exist', $template));
        }
        return $this->twig->render($template, [
            'widget' => $this->getEntity(),
        ]);
    }
}
