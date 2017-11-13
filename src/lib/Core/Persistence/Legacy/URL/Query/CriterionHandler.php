<?php

namespace EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use eZ\Publish\Core\Persistence\Database\SelectQuery;

interface CriterionHandler
{
    /**
     * Check if this criterion handler accepts to handle the given criterion.
     *
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion $criterion
     * @return bool
     */
    public function accept(Criterion $criterion);

    /**
     * Generate query expression for a Criterion this handler accepts.
     *
     * accept() must be called before calling this method.
     *
     * @param \EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter $converter
     * @param \eZ\Publish\Core\Persistence\Database\SelectQuery $query
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion $criterion
     */
    public function handle(CriteriaConverter $converter, SelectQuery $query, Criterion $criterion);
}
