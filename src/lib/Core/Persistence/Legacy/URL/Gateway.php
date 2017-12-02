<?php

namespace EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URL;

abstract class Gateway
{
    abstract public function insertUrl(URL $url);

    abstract public function updateUrl(URL $url);

    abstract public function find(Criterion $criterion, $offset, $limit, $doCount = true);

    abstract public function loadUrlData($id);

    abstract public function loadUrlDataByUrl($url);

    abstract public function getRelatedContentIds($id);
}
