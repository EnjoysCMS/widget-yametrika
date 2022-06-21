<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika;


use EnjoysCMS\Core\Components\Widgets\AbstractWidgets;
use EnjoysCMS\Core\Entities\Widget as Entity;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Loader\LoaderInterface;

abstract class YaMetrika extends AbstractWidgets
{
    protected LoaderInterface $twigLoader;

    public function __construct(ContainerInterface $container, Entity $widget)
    {
        parent::__construct($container, $widget);
        $this->twigLoader = $this->twig->getLoader();
        $this->twigLoader->addPath(__DIR__ . '/template', 'metrika');
    }

    public static function getWidgetDefinitionFile(): string
    {
        return __DIR__ . '/../widgets.yml';
    }

    public static function getMeta(): array
    {
        return Yaml::parseFile(static::getWidgetDefinitionFile())[static::class];
    }
}
