<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika\Widgets;


use EnjoysCMS\WidgetYaMetrika\YaMetrika;

final class Geo extends YaMetrika
{
    public function view(): string
    {
        return $this->twig->render('@metrika/geo.twig', [
            'widget' => $this->widget,
        ]);
    }
}
