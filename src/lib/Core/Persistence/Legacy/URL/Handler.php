<?php

namespace EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler as HandlerInterface;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URLCreateStruct;
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
    public function createUrl(URLCreateStruct $urlCreateStruct)
    {
        $url = $this->urlMapper->createURLFromCreateStruct(
            $urlCreateStruct
        );
        $url->id = $this->urlGateway->insertUrl($url);

        return $url;
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
    public function find(Criterion $criterion, $offset = 0, $limit = -1)
    {
        $results = $this->urlGateway->find($criterion, $offset, $limit);

        return [
            'count' => $results['count'],
            'items' => $this->urlMapper->extractURLsFromRows($results['rows']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load($id)
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
    public function getRelatedContentIds($id)
    {
        $ids = $this->urlGateway->getRelatedContentIds($id);

        return $ids;
    }
}
