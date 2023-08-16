<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika\Widgets;


use EnjoysCMS\Core\Block\Annotation\Widget;
use EnjoysCMS\WidgetYaMetrika\YaMetrikaAbstractWidget;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Widget(
    name: 'Поисковые запросы',
    options: [
        'title' => 'Поисковые запросы',
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
final class SearchPhrases extends YaMetrikaAbstractWidget
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function view(): string
    {
        return $this->twig->render('@metrika/search_phrases.twig', [
            'widget' => $this->getEntity(),
        ]);
    }
}
