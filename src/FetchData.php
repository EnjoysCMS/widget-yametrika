<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika;


use AXP\YaMetrika\Client;
use AXP\YaMetrika\Exception\FormatException;
use AXP\YaMetrika\YaMetrika;
use DateTime;
use EnjoysCMS\Core\Block\Entity\Widget;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

final class FetchData
{
    public function __construct(
        private readonly Widget $widget,
        private readonly CacheItemPoolInterface $cache,
        private ?YaMetrika $client = null
    ) {
    }


    private function getCounterId(): ?string
    {
        return $this->widget->getOptions()->getValue('yandex-counter-id') ?: null;
    }

    private function getCacheId(string $prefix): string
    {
        return md5(json_encode([$prefix, $this->widget->getOptions(), $this->widget->getId()]));
    }

    private function getClient(): YaMetrika
    {
        $token = $_ENV['YA_METRIKA_TOKEN'] ?? throw new \InvalidArgumentException(
            'Set in .env `YA_METRIKA_TOKEN`. See <a href="https://github.com/axp-dev/ya-metrika#Получение-токена">here</a>'
        );

        $counterId = $this->getCounterId() ?? $_ENV['YA_METRIKA_COUNTER_ID'] ?? throw new \InvalidArgumentException(
            'Set counter id of yandex metrika in setting widget or in .env parameter `YA_METRIKA_COUNTER_ID`'
        );

        if ($this->client === null) {
            $client = new Client($token, $counterId);
            $this->client = new YaMetrika($client);
        }
        return $this->client;
    }

    /**
     * @throws FormatException
     * @throws InvalidArgumentException
     */
    public function getVisitors(DateTime $startDate = null, DateTime $endDate = null)
    {
        $item = $this->cache->getItem($this->getCacheId('visitors'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()->getValue('cache') ?: 0))
                ->set(
                    $this->getClient()->getVisitorsForPeriod(
                        $startDate ?? (new DateTime())->modify(
                        sprintf('-%d days', (int)($this->widget->getOptions()->getValue('days') ?: 30))
                    ),
                        $endDate ?? new DateTime()
                    )->formatData()
                );
            $this->cache->save($item);
        }

        return $item->get();
    }

    /**
     * @throws FormatException
     * @throws InvalidArgumentException
     */
    public function getBrowsers()
    {
        $item = $this->cache->getItem($this->getCacheId('browsers'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()->getValue('cache') ?: 0))
                ->set(
                    $this->getClient()->getBrowsersForPeriod(
                        (new DateTime())->modify(
                            sprintf('-%d days', (int)($this->widget->getOptions()->getValue('days') ?: 30))
                        ),
                        new DateTime(),
                        (int)($this->widget->getOptions()->getValue('limit') ?: 10)
                    )->formatData()
                );
            $this->cache->save($item);
        }

        return $item->get();
    }


    /**
     * @throws FormatException
     * @throws InvalidArgumentException
     */
    public function getAgeGender()
    {
        $item = $this->cache->getItem($this->getCacheId('age-gender'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()->getValue('cache') ?: 0))
                ->set(
                    $this->getClient()->getAgeGenderForPeriod(
                        (new DateTime())->modify(
                            sprintf('-%d days', (int)($this->widget->getOptions()->getValue('days') ?: 30))
                        ),
                        new DateTime(),
                        (int)($this->widget->getOptions()->getValue('limit') ?: 20)
                    )->formatData()
                );
            $this->cache->save($item);
        }

        return $item->get();
    }


    /**
     * @throws FormatException
     * @throws InvalidArgumentException
     */
    public function getGeo()
    {
        $item = $this->cache->getItem($this->getCacheId('geo'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()->getValue('cache') ?: 0))
                ->set(
                    $this->getClient()->getGeoForPeriod(
                        (new DateTime())->modify(
                            sprintf('-%d days', (int)($this->widget->getOptions()->getValue('days') ?: 7))
                        ),
                        new DateTime(),
                        (int)($this->widget->getOptions()->getValue('limit') ?: 20)
                    )->formatData()
                );
            $this->cache->save($item);
        }

        return $item->get();
    }

    /**
     * @throws FormatException
     * @throws InvalidArgumentException
     */
    public function getMostViewedPages()
    {
        $item = $this->cache->getItem($this->getCacheId('most-view-pages'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()->getValue('cache') ?: 0))
                ->set(
                    $this->getClient()->getMostViewedPagesForPeriod(
                        (new DateTime())->modify(
                            sprintf('-%d days', (int)($this->widget->getOptions()->getValue('days') ?: 30))
                        ),
                        new DateTime(),
                        (int)($this->widget->getOptions()->getValue('limit') ?: 10)
                    )->formatData()
                );
            $this->cache->save($item);
        }

        return $item->get();
    }

    /**
     * @throws FormatException
     * @throws InvalidArgumentException
     */
    public function getSearchPhrases()
    {
        $item = $this->cache->getItem($this->getCacheId('search-phrases'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()->getValue('cache') ?: 0))
                ->set(
                    $this->getClient()->getSearchPhrases(
                        (int)($this->widget->getOptions()->getValue('days') ?: 30),
                        (int)($this->widget->getOptions()->getValue('limit') ?: 20)
                    )->formatData()
                );

            $this->cache->save($item);
        }

        return $item->get();
    }

    /**
     * @throws FormatException
     * @throws InvalidArgumentException
     */
    public function getUsersSearchEngine()
    {
        $item = $this->cache->getItem($this->getCacheId('users-search-engine'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()->getValue('cache') ?: 0))
                ->set(
                    $this->getClient()->getUsersSearchEngineForPeriod(
                        (new DateTime())->modify(
                            sprintf('-%d days', (int)($this->widget->getOptions()->getValue('days') ?: 30))
                        ),
                        new DateTime(),
                        (int)($this->widget->getOptions()->getValue('limit') ?: 10)
                    )->formatData()
                );

            $this->cache->save($item);
        }

        return $item->get();
    }


}
