<?php

namespace EzSystems\EzPlatformLinkManager\URLChecker;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;

interface URLCheckerInterface
{
    public function check(URLQuery $query);
}
