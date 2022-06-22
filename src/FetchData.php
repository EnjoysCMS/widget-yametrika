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


    public function getAgeGender()
    {
        $cacheId = $this->getCacheId('age-gender');

        if (null === $data = $this->cacher?->get($cacheId)) {
            $data = $this->getClient()->getAgeGenderForPeriod(
                (new \DateTime())->modify(
                    sprintf('-%d days', (int)($this->widget->getOptions()['days']['value'] ?? 30))
                ),
                new \DateTime(),
                (int)($this->widget->getOptions()['limit']['value'] ?? 20)
            )->formatData();
            $this->cacher->set($cacheId, $data, (int)($this->widget->getOptions()['value'] ?? 0));
        }

        return $data;
    }


    public function getGeo()
    {
        $cacheId = $this->getCacheId('geo');

        if (null === $data = $this->cacher?->get($cacheId)) {
            $data = $this->getClient()->getGeo(
                (int)($this->widget->getOptions()['days']['value'] ?? 7),
                (int)($this->widget->getOptions()['limit']['value'] ?? 20)
            )->formatData();
            $this->cacher->set($cacheId, $data, (int)($this->widget->getOptions()['value'] ?? 0));
        }

        return $data;
    }

    public function getMostViewedPages()
    {
        $cacheId = $this->getCacheId('most-view-pages');

        if (null === $data = $this->cacher?->get($cacheId)) {
            $data = $this->getClient()->getMostViewedPagesForPeriod(
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

    public function getSearchPhrases()
    {
        $cacheId = $this->getCacheId('search-phrases');

        if (null === $data = $this->cacher?->get($cacheId)) {
            $data = $this->getClient()->getSearchPhrases(
                (int)($this->widget->getOptions()['days']['value'] ?? 30),
                (int)($this->widget->getOptions()['limit']['value'] ?? 20)
            )->formatData();
            $this->cacher->set($cacheId, $data, (int)($this->widget->getOptions()['value'] ?? 0));
        }

        return $data;
    }

    public function getUsersSearchEngine()
    {
        $cacheId = $this->getCacheId('users-search-engine');

        if (null === $data = $this->cacher?->get($cacheId)) {
            $data = $this->getClient()->getUsersSearchEngineForPeriod(
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
