<?php

namespace EzSystems\EzPlatformLinkManager\API\Repository\Values;

use eZ\Publish\API\Repository\Values\ValueObject;

class SearchResult extends ValueObject implements \IteratorAggregate
{
    /**
     * @var int
     */
    protected $count;

    /**
     * @var \EzSystems\EzPlatformLinkManager\API\Repository\Values\URL[]
     */
    protected $items;

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}
