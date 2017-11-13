<?php

namespace EzSystems\EzPlatformLinkManager\SPI\Persistence\URL;

use eZ\Publish\SPI\Persistence\ValueObject;

class URLCreateStruct extends ValueObject
{
    /**
     * @var string
     */
    public $url;

    /**
     * @var bool
     */
    public $isValid;

    /**
     * Last check date.
     *
     * @var int
     */
    public $lastChecked;

    /**
     * Modification date.
     *
     * @var int
     */
    public $modified;
}
