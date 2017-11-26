<?php

namespace EzSystems\EzPlatformLinkManager\URLChecker;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;

interface URLCheckerInterface
{
    public function check(Criterion $criterion, $offset = 0, $limit = -1);
}
