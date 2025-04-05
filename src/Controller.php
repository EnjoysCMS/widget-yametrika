<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika;


use DateTime;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use EnjoysCMS\Core\AbstractController;
use EnjoysCMS\Core\Block\Entity\Widget;
use EnjoysCMS\Core\Routing\Annotation\Route;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Throwable;

#[Route('/yametrika', '@yametrika_')]
final class Controller extends AbstractController
{
    private FetchData $metrika;

    /**
     * @throws NotSupported
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(Container $container, EntityManager $em)
    {
        parent::__construct($container);
        $payload = json_decode($this->request->getBody()->__toString());
        $widget = $em->getRepository(Widget::class)->find(
            $payload->wid ?? 0
        ) ?? throw new InvalidArgumentException('Widget not found');

        $cache = new FilesystemAdapter('widgets', directory: $_ENV['TEMP_DIR'] . '/cache');
        $this->metrika = new FetchData($widget, $cache);
    }

    #[Route(
        path: '/visitors',
        name: 'visitors',
        methods: ['post']
    )]
    public function getVisitors(?DateTime $startDate = null, ?DateTime $endDate = null): ResponseInterface
    {
        try {
            return $this->json($this->metrika->getVisitors($startDate, $endDate));
        } catch (Throwable $e) {
            return $this->json($e->getMessage())->withStatus(500);
        }
    }

    #[Route(
        path: '/browsers',
        name: 'browsers',
        methods: ['post']
    )]
    public function getBrowsers(): ResponseInterface
    {
        try {
            return $this->json($this->metrika->getBrowsers());
        } catch (Throwable $e) {
            return $this->json($e->getMessage())->withStatus(500);
        }
    }

    #[Route(
        path: '/se',
        name: 'se',
        methods: ['post']
    )]
    public function getSE(): ResponseInterface
    {
        try {
            return $this->json($this->metrika->getUsersSearchEngine());
        } catch (Throwable $e) {
            return $this->json($e->getMessage())->withStatus(500);
        }
    }


    #[Route(
        path: '/search_phrases',
        name: 'search_phrases',
        methods: ['post']
    )]
    public function getSearchPhrases(): ResponseInterface
    {
        try {
            return $this->json($this->metrika->getSearchPhrases());
        } catch (Throwable $e) {
            return $this->json($e->getMessage())->withStatus(500);
        }
    }

    #[Route(
        path: '/age_gender',
        name: 'age_gender',
        methods: ['post']
    )]
    public function getAgeGender(): ResponseInterface
    {
        try {
            return $this->json($this->metrika->getAgeGender());
        } catch (Throwable $e) {
            return $this->json($e->getMessage())->withStatus(500);
        }
    }

    #[Route(
        path: '/geo',
        name: 'geo',
        methods: ['post']
    )]
    public function getGeo(): ResponseInterface
    {
        try {
            return $this->json($this->metrika->getGeo());
        } catch (Throwable $e) {
            return $this->json($e->getMessage())->withStatus(500);
        }
    }

    #[Route(
        path: '/most_viewed_pages',
        name: 'most_viewed_pages',
        methods: ['post']
    )]
    public function getMostViewedPages(): ResponseInterface
    {
        try {
            return $this->json($this->metrika->getMostViewedPages());
        } catch (Throwable $e) {
            return $this->json($e->getMessage())->withStatus(500);
        }
    }
}
