<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika;


use EnjoysCMS\Core\Block\AbstractWidget;
use Twig\Environment;

abstract class YaMetrikaAbstractWidget extends AbstractWidget
{
    public function __construct(protected readonly Environment $twig)
    {
        $this->twig->getLoader()->addPath(__DIR__ . '/template', 'metrika');
    }


}
