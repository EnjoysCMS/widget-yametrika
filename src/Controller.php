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
        return $this->responseJson($this->metrika->getVisitors($startDate, $endDate));
    }

    #[Route(
        path: '/yametrika/browsers',
        name: 'yametrika/browsers',
        methods: ['post']
    )]
    public function getBrowsers(
        \DateTime $startDate = null,
        \DateTime $endDate = null,
        int $limit = 10
    ): ResponseInterface {
        return $this->responseJson($this->metrika->getBrowsers($startDate, $endDate, $limit));
    }
}
