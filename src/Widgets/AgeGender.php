<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika\Widgets;


use EnjoysCMS\WidgetYaMetrika\YaMetrikaAbstractWidget;
use InvalidArgumentException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class AgeGender extends YaMetrikaAbstractWidget
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function view(): string
    {
        $template = sprintf('@metrika/%s/age_gender.twig', $this->getBlockOptions()->getValue('chart')?? '');

        if (!$this->twig->getLoader()->exists($template)){
            throw new InvalidArgumentException(sprintf('%s not exist', $template));
        }
        return $this->twig->render($template, [
            'widget' => $this->getEntity(),
        ]);
    }
}
