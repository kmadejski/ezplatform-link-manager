<?php

namespace EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\SortClause;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\SortClause;

class URL extends SortClause
{
    /**
     * Constructs a new URL SortClause.
     *
     * @param string $sortDirection
     */
    public function __construct($sortDirection = self::SORT_ASC)
    {
        parent::__construct('url', $sortDirection);
    }
}
