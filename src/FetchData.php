<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika;


use AXP\YaMetrika\Client;
use AXP\YaMetrika\Exception\FormatException;
use EnjoysCMS\Core\Entities\Widget;
use Psr\Cache\CacheItemPoolInterface;

final class FetchData
{
    public function __construct(
        private Widget $widget,
        private CacheItemPoolInterface $cache,
        private ?\AXP\YaMetrika\YaMetrika $client = null
    ) {
    }

    private function getOption(string $key)
    {
        if ($this->widget->getOptions()[$key]['value'] ?? false) {
            return $this->widget->getOptions()[$key]['value'];
        }

        if (isset($this->widget->getOptions()[$key]) && is_scalar(
                $this->widget->getOptions()[$key]
            )) {
            return $this->widget->getOptions()[$key];
        }

        return null;
    }

    private function getCounterId()
    {
        return $this->getOption('yandex-counter-id');
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
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getVisitors(\DateTime $startDate = null, \DateTime $endDate = null)
    {
        $item = $this->cache->getItem($this->getCacheId('visitors'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()['cache'] ?? 0))
                ->set(
                    $this->getClient()->getVisitorsForPeriod(
                        $startDate ?? (new \DateTime())->modify(
                        sprintf('-%d days', (int)($this->widget->getOptions()['days']['value'] ?? 30))
                    ),
                        $endDate ?? new \DateTime()
                    )->formatData()
                )
            ;
            $this->cache->save($item);
        }

        return $item->get();
    }

    /**
     * @throws FormatException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getBrowsers()
    {
        $item = $this->cache->getItem($this->getCacheId('browsers'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()['cache'] ?? 0))
                ->set(
                    $this->getClient()->getBrowsersForPeriod(
                        (new \DateTime())->modify(
                            sprintf('-%d days', (int)($this->widget->getOptions()['days']['value'] ?? 30))
                        ),
                        new \DateTime(),
                        (int)($this->widget->getOptions()['limit']['value'] ?? 10)
                    )->formatData()
                )
            ;
            $this->cache->save($item);
        }

        return $item->get();
    }


    /**
     * @throws FormatException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAgeGender()
    {
        $item = $this->cache->getItem($this->getCacheId('age-gender'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()['cache'] ?? 0))
                ->set(
                    $this->getClient()->getAgeGenderForPeriod(
                        (new \DateTime())->modify(
                            sprintf('-%d days', (int)($this->widget->getOptions()['days']['value'] ?? 30))
                        ),
                        new \DateTime(),
                        (int)($this->widget->getOptions()['limit']['value'] ?? 20)
                    )->formatData()
                )
            ;
            $this->cache->save($item);
        }

        return $item->get();
    }


    /**
     * @throws FormatException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getGeo()
    {
        $item = $this->cache->getItem($this->getCacheId('geo'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()['cache'] ?? 0))
                ->set(
                    $this->getClient()->getGeoForPeriod(
                        (new \DateTime())->modify(
                            sprintf('-%d days', (int)($this->widget->getOptions()['days']['value'] ?? 7))
                        ),
                        new \DateTime(),
                        (int)($this->widget->getOptions()['limit']['value'] ?? 20)
                    )->formatData()
                )
            ;
            $this->cache->save($item);
        }

        return $item->get();
    }

    /**
     * @throws FormatException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getMostViewedPages()
    {
        $item = $this->cache->getItem($this->getCacheId('most-view-pages'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()['cache'] ?? 0))
                ->set(
                    $this->getClient()->getMostViewedPagesForPeriod(
                        (new \DateTime())->modify(
                            sprintf('-%d days', (int)($this->widget->getOptions()['days']['value'] ?? 30))
                        ),
                        new \DateTime(),
                        (int)($this->widget->getOptions()['limit']['value'] ?? 10)
                    )->formatData()
                )
            ;
            $this->cache->save($item);
        }

        return $item->get();
    }

    /**
     * @throws FormatException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getSearchPhrases()
    {
        $item = $this->cache->getItem($this->getCacheId('search-phrases'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()['cache'] ?? 0))
                ->set(
                    $this->getClient()->getSearchPhrases(
                        (int)($this->widget->getOptions()['days']['value'] ?? 30),
                        (int)($this->widget->getOptions()['limit']['value'] ?? 20)
                    )->formatData()
                )
            ;

            $this->cache->save($item);
        }

        return $item->get();
    }

    /**
     * @throws FormatException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getUsersSearchEngine()
    {
        $item = $this->cache->getItem($this->getCacheId('users-search-engine'));

        if (!$item->isHit()) {
            $item
                ->expiresAfter((int)($this->widget->getOptions()['cache'] ?? 0))
                ->set(
                    $this->getClient()->getUsersSearchEngineForPeriod(
                        (new \DateTime())->modify(
                            sprintf('-%d days', (int)($this->widget->getOptions()['days']['value'] ?? 30))
                        ),
                        new \DateTime(),
                        (int)($this->widget->getOptions()['limit']['value'] ?? 10)
                    )->formatData()
                )
            ;

            $this->cache->save($item);
        }

        return $item->get();
    }


}
