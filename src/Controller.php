<?php

declare(strict_types=1);


namespace EnjoysCMS\WidgetYaMetrika;


use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\BaseController;
use EnjoysCMS\Core\Entities\Widget;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Annotation\Route;

final class Controller extends BaseController
{
    private FetchData $metrika;

    public function __construct(EntityManager $em, private ServerRequestInterface $request)
    {
        parent::__construct();
        $payload = json_decode($this->request->getBody()->getContents());
        $widget = $em->getRepository(Widget::class)->find(
                $payload->wid ?? 0
            ) ?? throw new \InvalidArgumentException('Widget not found');

        $this->metrika = new FetchData($widget);
    }


    #[Route(
        path: '/yametrika/visitors',
        name: 'yametrika/visitors',
        methods: ['post']
    )]
    public function getVisitors(\DateTime $startDate = null, \DateTime $endDate = null): ResponseInterface
    {
        try {
            return $this->responseJson($this->metrika->getVisitors($startDate, $endDate));
        } catch (\Throwable $e) {
            return $this->responseJson($e->getMessage())->withStatus(500);
        }
    }

    /**
     * @throws \AXP\YaMetrika\Exception\FormatException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    #[Route(
        path: '/yametrika/browsers',
        name: 'yametrika/browsers',
        methods: ['post']
    )]
    public function getBrowsers(): ResponseInterface {
        try {
            return $this->responseJson($this->metrika->getBrowsers());
        } catch (\Throwable $e) {
            return $this->responseJson($e->getMessage())->withStatus(500);
        }
    }

    #[Route(
        path: '/yametrika/se',
        name: 'yametrika/se',
        methods: ['post']
    )]
    public function getSE(): ResponseInterface {
        try {
            return $this->responseJson($this->metrika->getUsersSearchEngine());
        } catch (\Throwable $e) {
            return $this->responseJson($e->getMessage())->withStatus(500);
        }
    }


    #[Route(
        path: '/yametrika/search_phrases',
        name: 'yametrika/search_phrases',
        methods: ['post']
    )]
    public function getSearchPhrases(): ResponseInterface {
        try {
            return $this->responseJson($this->metrika->getSearchPhrases());
        } catch (\Throwable $e) {
            return $this->responseJson($e->getMessage())->withStatus(500);
        }
    }

    #[Route(
        path: '/yametrika/age_gender',
        name: 'yametrika/age_gender',
        methods: ['post']
    )]
    public function getAgeGender(): ResponseInterface {
        try {
            return $this->responseJson($this->metrika->getAgeGender());
        } catch (\Throwable $e) {
            return $this->responseJson($e->getMessage())->withStatus(500);
        }
    }

    #[Route(
        path: '/yametrika/geo',
        name: 'yametrika/geo',
        methods: ['post']
    )]
    public function getGeo(): ResponseInterface {
        try {
            return $this->responseJson($this->metrika->getGeo());
        } catch (\Throwable $e) {
            return $this->responseJson($e->getMessage())->withStatus(500);
        }
    }

    #[Route(
        path: '/yametrika/most_viewed_pages',
        name: 'yametrika/most_viewed_pages',
        methods: ['post']
    )]
    public function getMostViewedPages(): ResponseInterface {
        try {
            return $this->responseJson($this->metrika->getMostViewedPages());
        } catch (\Throwable $e) {
            return $this->responseJson($e->getMessage())->withStatus(500);
        }
    }
}
