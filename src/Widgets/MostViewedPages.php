<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika\Widgets;


use EnjoysCMS\WidgetYaMetrika\YaMetrika;

final class MostViewedPages extends YaMetrika
{
    public function view(): string
    {
        return $this->twig->render('@metrika/most_viewed_pages.twig', [
            'widget' => $this->widget,
        ]);
    }
}
