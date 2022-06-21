<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika\Widgets;


use EnjoysCMS\WidgetYaMetrika\YaMetrika;

final class Traffic extends YaMetrika
{
    public function view(): string
    {
        $template = sprintf('@metrika/traffic-%s.twig', $this->widget->getOptions()['chart']['value'] ?? '');

        if (!$this->twigLoader->exists($template)){
            throw new \InvalidArgumentException(sprintf('%s not exist', $template));
        }
        return $this->twig->render($template, [
            'widget' => $this->widget,
        ]);
    }
}
