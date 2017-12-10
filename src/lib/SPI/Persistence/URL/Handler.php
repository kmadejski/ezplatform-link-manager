<?php

namespace EzSystems\EzPlatformLinkManager\SPI\Persistence\URL;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;

/**
 * The URL Handler interface defines operations on URLs in the storage engine.
 */
interface Handler
{
    /**
     * Updates a existing URL.
     *
     * @param int $id
     * @param \EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URLUpdateStruct $urlUpdateStruct
     * @return \EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URL
     */
    public function updateUrl($id, URLUpdateStruct $urlUpdateStruct);

    /**
     * Selects URLs data using $query.
     *
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery $query
     * @return array
     */
    public function find(URLQuery $query);

    /**
     * Loads the data for the URL identified by $id.
     *
     * @param int $id
     * @return \EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URL
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function loadById($id);

    /**
     * Loads the data for the URL identified by $url.
     *
     * @param string $url
     * @return \EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URL
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function loadByUrl($url);

    /**
     * Returns IDs of Content Objects using URL identified by $id.
     *
     * @param int $id
     * @return array
     */
    public function getRelatedContentIds($id);
}
