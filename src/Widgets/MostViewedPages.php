<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika\Widgets;


use EnjoysCMS\Core\Block\Annotation\Widget;
use EnjoysCMS\WidgetYaMetrika\YaMetrikaAbstractWidget;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Widget(
    name: 'Популярные страницы',
    options: [
        'title' => 'Популярные страницы',
        'yandex-counter-id' => null,
        'cache' => 1800,
        'days' => [
            'value' => 30,
            'title' => 'Период в днях',
            'description' => 'За какое количество дней получить статистику'
        ],
        'limit' => 20,
        'background' => [
            'value' => 'white',
            'type' => 'select',
            'data' => [
                'primary',
                'secondary',
                'success',
                'danger',
                'warning',
                'info',
                'light',
                'dark',
                'white',
                'transparent'
            ]
        ],
        'gs' => [
            'min-h' => 15,
            'min-w' => 3,
            'h' => 21,
            'w' => 5,
        ]
    ]
)]
final class MostViewedPages extends YaMetrikaAbstractWidget
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function view(): string
    {
        return $this->twig->render('@metrika/most_viewed_pages.twig', [
            'widget' => $this->getEntity(),
        ]);
    }
}
