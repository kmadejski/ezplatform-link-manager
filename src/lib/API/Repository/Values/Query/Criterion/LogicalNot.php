<?php

namespace EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;

class LogicalNot extends LogicalOperator
{
    /**
     * Creates a new NOT logic criterion.
     *
     * Will match of the given criterion doesn't match
     *
     * @param Criterion $criterion criterion
     * @throws \InvalidArgumentException if more than one criterion is given in the array parameter
     */
    public function __construct(Criterion $criterion)
    {
        parent::__construct([$criterion]);
    }
}
