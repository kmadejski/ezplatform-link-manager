<?php

namespace EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler;
use eZ\Publish\Core\Persistence\Database\SelectQuery;
use PDO;

class VisibleOnly implements CriterionHandler
{
    /**
     * @inheritdoc
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof Query\VisibleOnly;
    }

    /**
     * @inheritdoc
     */
    public function handle(CriteriaConverter $converter, SelectQuery $query, Criterion $criterion)
    {
        // TODO: The following query requires optimization
        $subSelect = $query->subSelect();
        $subSelect
            ->selectDistinct('ezurl_object_link.url_id')
            ->from('ezurl_object_link')
            ->innerJoin(
                'ezcontentobject_attribute',
                $query->expr->lAnd(
                    $query->expr->eq('ezurl_object_link.contentobject_attribute_id', 'ezcontentobject_attribute.id'),
                    $query->expr->eq('ezurl_object_link.contentobject_attribute_version', 'ezcontentobject_attribute.version')
                )
            )
            ->innerJoin(
                'ezcontentobject_tree',
                $query->expr->lAnd(
                    $query->expr->eq('ezcontentobject_tree.contentobject_id', 'ezcontentobject_attribute.contentobject_id'),
                    $query->expr->eq('ezcontentobject_tree.contentobject_version', 'ezcontentobject_attribute.version')
                )
            )
            ->where(
                $query->expr->eq(
                    'ezcontentobject_tree.is_invisible',
                    $query->bindValue(0, null, PDO::PARAM_INT)
                )
            );

        return $query->expr->in('ezurl.id', $subSelect);
    }
}
