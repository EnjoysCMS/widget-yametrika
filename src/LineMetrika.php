<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika;


use EnjoysCMS\Core\Components\Widgets\AbstractWidgets;
use Symfony\Component\Yaml\Yaml;

final class LineMetrika extends AbstractWidgets
{
    public static function getWidgetDefinitionFile(): string
    {
        return __DIR__ . '/../widgets.yml';
    }

    public static function getMeta(): array
    {
        return Yaml::parseFile(static::getWidgetDefinitionFile())[static::class];
    }

    public function view(): string
    {
        return '';
    }
}
