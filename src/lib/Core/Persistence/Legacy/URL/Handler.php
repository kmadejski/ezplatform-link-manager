<?php

namespace EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler as HandlerInterface;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URLUpdateStruct;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;

class Handler implements HandlerInterface
{
    /** @var \EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Gateway */
    private $urlGateway;

    /** @var \EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Mapper */
    private $urlMapper;

    /**
     * Handler constructor.
     *
     * @param \EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Gateway $gateway
     * @param \EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Mapper $mapper
     */
    public function __construct(Gateway $gateway, Mapper $mapper)
    {
        $this->urlGateway = $gateway;
        $this->urlMapper = $mapper;
    }

    /**
     * {@inheritdoc}
     */
    public function updateUrl($id, URLUpdateStruct $urlUpdateStruct)
    {
        $url = $this->urlMapper->createURLFromUpdateStruct(
            $urlUpdateStruct
        );
        $url->id = $id;

        $this->urlGateway->updateUrl($url);

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function find(URLQuery $query)
    {
        $results = $this->urlGateway->find(
            $query->filter,
            $query->offset,
            $query->limit,
            $query->sortClauses
        );

        return [
            'count' => $results['count'],
            'items' => $this->urlMapper->extractURLsFromRows($results['rows']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function loadById($id)
    {
        $url = $this->urlMapper->extractURLsFromRows(
            $this->urlGateway->loadUrlData($id)
        );

        if (count($url) < 1) {
            throw new NotFoundException('URL', $id);
        }

        return reset($url);
    }

    /**
     * {@inheritdoc}
     */
    public function loadByUrl($url)
    {
        $url = $this->urlMapper->extractURLsFromRows(
            $this->urlGateway->loadUrlDataByUrl($url)
        );

        if (count($url) < 1) {
            throw new NotFoundException('URL', $url);
        }

        return reset($url);
    }

    /**
     * {@inheritdoc}
     */
    public function findUsages($id)
    {
        $ids = $this->urlGateway->getRelatedContentIds($id);

        return $ids;
    }
}
