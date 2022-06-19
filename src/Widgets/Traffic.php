<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika\Widgets;


use EnjoysCMS\WidgetYaMetrika\YaMetrika;

final class Traffic extends YaMetrika
{
    public function view(): string
    {

        return $this->twig->render('@metrika/traffic.twig', [
            'widget' => $this->widget,
        ]);
    }
}
