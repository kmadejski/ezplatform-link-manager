<?php

namespace EzSystems\EzPlatformLinkManager\API\Repository;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URL;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLUpdateStruct;

/**
 * URL Service.
 */
interface URLService
{
    /**
     * Instantiates a new URL update struct.
     *
     * @return \EzSystems\EzPlatformLinkManager\API\Repository\Values\URLUpdateStruct
     */
    public function createUpdateStruct();

    /**
     * Find URLs.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion $criteria
     * @param int $offset
     * @param int $limit
     * @return \EzSystems\EzPlatformLinkManager\API\Repository\Values\SearchResult
     */
    public function findUrls(Criterion $criteria, $offset = 0, $limit = -1);

    /**
     * Find content objects using URL.
     *
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\Values\URL $url
     * @param int $offset
     * @param int $limit
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function findUsages(URL $url, $offset = 0, $limit = -1);

    /**
     * Load single URL (by ID).
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @param int $id ID of URL
     * @return \EzSystems\EzPlatformLinkManager\API\Repository\Values\URL
     */
    public function loadById($id);

    /**
     * Load single URL (by URL).
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @param string $url url
     * @return \EzSystems\EzPlatformLinkManager\API\Repository\Values\URL
     */
    public function loadByUrl($url);

    /**
     * Update URL.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException if the url already exists
     *
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\Values\URL $url
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\Values\URLUpdateStruct $struct
     * @return \EzSystems\EzPlatformLinkManager\API\Repository\Values\URL
     */
    public function updateUrl(URL $url, URLUpdateStruct $struct);
}
