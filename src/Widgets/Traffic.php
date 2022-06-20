<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika\Widgets;


use EnjoysCMS\WidgetYaMetrika\YaMetrika;

final class Traffic extends YaMetrika
{
    public function view(): string
    {

        return $this->twig->render('@metrika/traffic-highcharts_js.twig', [
            'widget' => $this->widget,
        ]);
    }
}
