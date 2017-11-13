<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Pagination\Pagerfanta;

use EzSystems\EzPlatformLinkManager\API\Repository\URLService;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use Pagerfanta\Adapter\AdapterInterface;

class URLSearchAdapter implements AdapterInterface
{
    /**
     * @var \EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion
     */
    private $criterion;

    /**
     * @var \EzSystems\EzPlatformLinkManager\API\Repository\URLService
     */
    private $urlService;

    /**
     * UrlSearchAdapter constructor.
     *
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion $criterion
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\URLService $urlService
     */
    public function __construct(Criterion $criterion, URLService $urlService)
    {
        $this->criterion = $criterion;
        $this->urlService = $urlService;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return $this->urlService->findUrls($this->criterion, 0, 0)->count;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        return $this->urlService->findUrls($this->criterion, $offset, $length)->items;
    }
}
