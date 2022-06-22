<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika\Widgets;


use EnjoysCMS\WidgetYaMetrika\YaMetrika;

final class SearchPhrases extends YaMetrika
{
    public function view(): string
    {
        return $this->twig->render('@metrika/search_phrases.twig', [
            'widget' => $this->widget,
        ]);
    }
}
