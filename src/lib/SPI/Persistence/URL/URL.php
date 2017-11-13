<?php

namespace EzSystems\EzPlatformLinkManager\SPI\Persistence\URL;

use eZ\Publish\SPI\Persistence\ValueObject;

class URL extends ValueObject
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $originalUrlMd5;

    /**
     * @var bool
     */
    public $isValid;

    /**
     * @var \int
     */
    public $lastChecked;

    /**
     * @var \int
     */
    public $created;

    /**
     * @var \int
     */
    public $modified;
}
