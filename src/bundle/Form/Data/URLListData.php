<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Form\Data;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * DTO for URL list.
 */
class URLListData extends ValueObject
{
    /** @var string|null */
    public $searchQuery;

    /** @var bool|null */
    public $status;

    /** @var int */
    public $page = 1;

    /** @var int */
    public $limit = 10;
}
