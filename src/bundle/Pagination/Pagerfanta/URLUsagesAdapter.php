<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Pagination\Pagerfanta;

use EzSystems\EzPlatformLinkManager\API\Repository\URLService;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URL;
use Pagerfanta\Adapter\AdapterInterface;

class URLUsagesAdapter implements AdapterInterface
{
    /**
     * @var \EzSystems\EzPlatformLinkManager\API\Repository\URLService
     */
    private $urlService;

    /**
     * @var \EzSystems\EzPlatformLinkManager\API\Repository\Values\URL
     */
    private $url;

    /**
     * UrlUsagesAdapter constructor.
     *
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\Values\URL $url
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\URLService $urlService
     */
    public function __construct(URL $url, URLService $urlService)
    {
        $this->urlService = $urlService;
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return $this->urlService->findUsages($this->url, 0, 0)->totalCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        return $this->urlService->findUsages($this->url, $offset, $length)->searchHits;
    }
}
