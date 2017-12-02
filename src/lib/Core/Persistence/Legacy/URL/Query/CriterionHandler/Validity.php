<?php

namespace EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler;
use eZ\Publish\Core\Persistence\Database\SelectQuery;

class Validity implements CriterionHandler
{
    /**
     * {@inheritdoc}
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\Validity;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(CriteriaConverter $converter, SelectQuery $query, Criterion $criterion)
    {
        /** @var Criterion\Validity $criterion */
        return $query->expr->eq(
            'is_valid',
            $query->bindValue($criterion->isValid)
        );
    }
}
