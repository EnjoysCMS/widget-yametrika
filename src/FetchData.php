<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika;


use AXP\YaMetrika\Client;
use AXP\YaMetrika\Exception\FormatException;
use Enjoys\SimpleCache\Cacher\FileCache;
use EnjoysCMS\Core\Entities\Widget;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

final class FetchData
{
    private ?CacheInterface $cacher = null;
    private $client = null;

    public function __construct(private Widget $widget)
    {
        $this->cacher = new FileCache([
            'path' => $_ENV['TEMP_DIR'] . '/cache/yametrika-widgets'
        ]);
    }

    private function getCounterId()
    {
        if ($this->widget->getOptions()['yandex-counter-id']['value'] ?? false) {
            return $this->widget->getOptions()['yandex-counter-id']['value'];
        }

        if (isset($this->widget->getOptions()['yandex-counter-id']) && is_scalar(
                $this->widget->getOptions()['yandex-counter-id']
            )) {
            return $this->widget->getOptions()['yandex-counter-id'];
        }

        return null;
    }

    private function getCacheId(string $prefix): string
    {
        return md5(json_encode([$prefix, $this->widget->getOptions(), $this->widget->getId()]));
    }

    private function getClient(): \AXP\YaMetrika\YaMetrika
    {
        if ($this->client === null) {
            $client = new Client(
                $_ENV['YA_METRIKA_TOKEN'] ?? throw new \InvalidArgumentException(
                    'Set in .env `YA_METRIKA_TOKEN`. See <a href="https://github.com/axp-dev/ya-metrika#Получение-токена">here</a>'
                ),
                $this->getCounterId()
                ?? $_ENV['YA_METRIKA_COUNTER_ID']
                ?? throw new \InvalidArgumentException(
                    'Set counter id of yandex metrika in setting widget or in .env parameter `YA_METRIKA_COUNTER_ID`'
                )
            );
            $this->client = new \AXP\YaMetrika\YaMetrika($client);
        }
        return $this->client;
    }

    /**
     * @throws FormatException
     * @throws InvalidArgumentException
     */
    public function getVisitors(\DateTime $startDate = null, \DateTime $endDate = null)
    {
        $cacheId = $this->getCacheId('visitors');

        if (null === $data = $this->cacher?->get($cacheId)) {
            $data = $this->getClient()->getVisitorsForPeriod(
                $startDate ?? (new \DateTime())->modify(
                    sprintf('-%d days', (int)($this->widget->getOptions()['days']['value'] ?? 30))
                ),
                $endDate ?? new \DateTime()
            )->formatData();
            $this->cacher->set($cacheId, $data, (int)($this->widget->getOptions()['cache']['value'] ?? 0));
        }

        return $data;
    }

    /**
     * @throws FormatException
     * @throws InvalidArgumentException
     */
    public function getBrowsers()
    {
        $cacheId = $this->getCacheId('browsers');

        if (null === $data = $this->cacher?->get($cacheId)) {
            $data = $this->getClient()->getBrowsersForPeriod(
                (new \DateTime())->modify(
                    sprintf('-%d days', (int)($this->widget->getOptions()['days']['value'] ?? 30))
                ),
                new \DateTime(),
                (int)($this->widget->getOptions()['limit']['value'] ?? 10)
            )->formatData();
            $this->cacher->set($cacheId, $data, (int)($this->widget->getOptions()['value'] ?? 0));
        }

        return $data;
    }
}
