<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika;


use AXP\YaMetrika\Client;
use AXP\YaMetrika\YaMetrika;
use EnjoysCMS\Core\Components\Widgets\AbstractWidgets;
use EnjoysCMS\Core\Entities\Widget as Entity;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Yaml\Yaml;

final class LineMetrika extends AbstractWidgets
{
    private ?string $error;
    private ?YaMetrika $metrika;
    private ?CacheInterface $cacher = null;

    public function __construct(ContainerInterface $container, Entity $widget)
    {
        parent::__construct($container, $widget);
        $this->twig->getLoader()->addPath(__DIR__.'/template', 'metrika');


        $this->error = null;
        $this->metrika = null;
        try {
            $this->cacher = include __DIR__ . '/Cacher.php';
            $client = new Client(
                $_ENV['YA_METRIKA_TOKEN'] ?? throw new \InvalidArgumentException(
                    'Set in .env `YA_METRIKA_TOKEN`. See <a href="https://github.com/axp-dev/ya-metrika#Получение-токена">here</a>'
                ),
                $this->widget->getOptions()['yandex-counter-id'] ?? $_ENV['YA_METRIKA_COUNTER_ID'] ?? throw new \InvalidArgumentException(
                    'Set counter id of yandex metrika in setting widget or in .env parameter `YA_METRIKA_COUNTER_ID`'
                )
            );
            $this->metrika = new YaMetrika($client);
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }

    }

    public function view(): string
    {
        $cacheId = md5(json_encode([$this->widget->getId(), $this->widget->getOptions()]));

        if (null === $data = $this->cacher?->get($cacheId)) {
            $metrics = $this->metrika->getVisitors()->customFormat(function ($data){
                $result = [];
                foreach ($data['data'] as $key => $item) {
                    $result[$key] = [
                        'date' => (new \DateTimeImmutable($item['dimensions'][0]['name']))->getTimestamp() * 1000,
                        'visits' => $item['metrics'][0],
                        'pageviews' => $item['metrics'][1],
                        'users' => $item['metrics'][2],
                    ];
                }
                return $result;
            });

            $data = json_encode($metrics, JSON_UNESCAPED_UNICODE);
            $this->cacher->set($cacheId, $data, 0);
        }

        return $this->twig->render('@metrika/line_metrika.twig', [
            'options' => $this->widget->getOptions(),
            'data' => $data,
            'error' => $this->error
        ]);
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
