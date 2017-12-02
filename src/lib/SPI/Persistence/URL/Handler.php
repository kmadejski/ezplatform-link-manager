<?php

namespace EzSystems\EzPlatformLinkManager\SPI\Persistence\URL;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;

interface Handler
{
    public function createUrl(URLCreateStruct $urlCreateStruct);

    public function updateUrl($id, URLUpdateStruct $urlUpdateStruct);

    public function find(URLQuery $query);

    public function loadById($id);

    public function loadByUrl($url);

    public function getRelatedContentIds($id);
}
