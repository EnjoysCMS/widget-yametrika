<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika\Widgets;


use EnjoysCMS\WidgetYaMetrika\YaMetrika;

final class Browsers extends YaMetrika
{
    public function view(): string
    {
        $template = sprintf('@metrika/%s/browsers.twig', $this->widget->getOptions()['chart']['value'] ?? '');

        if (!$this->twigLoader->exists($template)){
            throw new \InvalidArgumentException(sprintf('%s not exist', $template));
        }
        return $this->twig->render($template, [
            'widget' => $this->widget,
        ]);
    }
}
