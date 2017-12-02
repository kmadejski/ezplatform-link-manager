<?php

namespace EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\SortClause;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\SortClause;

class Id extends SortClause
{
    /**
     * Constructs a new Id SortClause.
     *
     * @param string $sortDirection
     */
    public function __construct($sortDirection = self::SORT_ASC)
    {
        parent::__construct('id', $sortDirection);
    }
}
