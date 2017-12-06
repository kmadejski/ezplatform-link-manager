<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Pagination\Pagerfanta;

use EzSystems\EzPlatformLinkManager\API\Repository\URLService;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;
use Pagerfanta\Adapter\AdapterInterface;

class URLSearchAdapter implements AdapterInterface
{
    /**
     * @var \EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery
     */
    private $query;

    /**
     * @var \EzSystems\EzPlatformLinkManager\API\Repository\URLService
     */
    private $urlService;

    /**
     * UrlSearchAdapter constructor.
     *
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery $query
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\URLService $urlService
     */
    public function __construct(URLQuery $query, URLService $urlService)
    {
        $this->query = $query;
        $this->urlService = $urlService;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $query = clone $this->query;
        $query->offset = 0;
        $query->limit = 0;

        return $this->urlService->findUrls($this->query)->count;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $query = clone $this->query;
        $query->offset = $offset;
        $query->limit = $length;

        return $this->urlService->findUrls($query)->items;
    }
}
