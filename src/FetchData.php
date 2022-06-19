<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika;


use AXP\YaMetrika\Client;
use EnjoysCMS\Core\Entities\Widget;
use Psr\SimpleCache\CacheInterface;

final class FetchData
{
    private \AXP\YaMetrika\YaMetrika $metrika;
    private ?CacheInterface $cacher = null;

    public function __construct(private Widget $widget)
    {
        $client = new Client(
            $_ENV['YA_METRIKA_TOKEN'] ?? throw new \InvalidArgumentException(
                'Set in .env `YA_METRIKA_TOKEN`. See <a href="https://github.com/axp-dev/ya-metrika#Получение-токена">here</a>'
            ),
            $this->widget->getOptions(
            )['yandex-counter-id'] ?? $_ENV['YA_METRIKA_COUNTER_ID'] ?? throw new \InvalidArgumentException(
                'Set counter id of yandex metrika in setting widget or in .env parameter `YA_METRIKA_COUNTER_ID`'
            )
        );
        $this->metrika = new \AXP\YaMetrika\YaMetrika($client);
        $this->cacher = include __DIR__ . '/Cacher.php';
    }

    private function getCacheId(string $prefix): string
    {
        return md5(json_encode([$prefix, $this->widget->getOptions(), $this->widget->getId()]));
    }

    public function getVisitors(\DateTime $startDate = null, \DateTime $endDate = null)
    {
        $cacheId = $this->getCacheId('visitors');

        if (null === $data = $this->cacher?->get($cacheId)) {
            $data = $this->metrika->getVisitorsForPeriod(
                $startDate ?? (new \DateTime())->modify('-30 days'),
                $endDate ?? new \DateTime()
            )->formatData();
            $this->cacher->set($cacheId, $data, $this->widget->getOptions()['cache'] ?? 60);
        }

        return $data;
    }

    public function getBrowsers(\DateTime $startDate = null, \DateTime $endDate = null, int $limit = 10)
    {
        $cacheId = $this->getCacheId('browsers');

        if (null === $data = $this->cacher?->get($cacheId)) {
            $data = $this->metrika->getBrowsersForPeriod(
                $startDate ?? (new \DateTime())->modify('-30 days'),
                $endDate ?? new \DateTime(),
                $limit
            )->formatData();
            $this->cacher->set($cacheId, $data, $this->widget->getOptions()['cache'] ?? 60);
        }

        return $data;
    }
}
