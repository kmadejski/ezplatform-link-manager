<?php

namespace EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\Core\Persistence\Database\SelectQuery;

class CriteriaConverter
{
    /**
     * Criterion handlers.
     *
     * @var \EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler[]
     */
    protected $handlers;

    /**
     * Construct from an optional array of Criterion handlers.
     *
     * @param \EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler[] $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * Adds handler.
     *
     * @param \EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler $handler
     */
    public function addHandler(CriterionHandler $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * Generic converter of criteria into query fragments.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotImplementedException if Criterion is not applicable to its target
     *
     * @param \eZ\Publish\Core\Persistence\Database\SelectQuery $query
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion $criterion
     * @return \eZ\Publish\Core\Persistence\Database\Expression
     */
    public function convertCriteria(SelectQuery $query, Criterion $criterion)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->accept($criterion)) {
                return $handler->handle($this, $query, $criterion);
            }
        }

        throw new NotImplementedException(
            'No visitor available for: ' . get_class($criterion)
        );
    }
}
