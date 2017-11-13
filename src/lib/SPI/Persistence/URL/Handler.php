<?php

namespace EzSystems\EzPlatformLinkManager\SPI\Persistence\URL;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;

interface Handler
{
    public function createUrl(URLCreateStruct $urlCreateStruct);

    public function updateUrl($id, URLUpdateStruct $urlUpdateStruct);

    public function find(Criterion $criterion, $offset = 0, $limit = -1);

    public function load($id);

    public function getRelatedContentIds($id);
}
