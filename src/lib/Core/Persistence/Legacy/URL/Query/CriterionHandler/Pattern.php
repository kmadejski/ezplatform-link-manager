<?php

namespace EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler;
use eZ\Publish\Core\Persistence\Database\SelectQuery;

class Pattern implements CriterionHandler
{
    /**
     * {@inheritdoc}
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Criterion\Pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(CriteriaConverter $converter, SelectQuery $query, Criterion $criterion)
    {
        /** @var Criterion\Pattern $criterion */
        return $query->expr->like(
            'url',
            $query->bindValue('%' . $criterion->pattern . '%')
        );
    }
}
