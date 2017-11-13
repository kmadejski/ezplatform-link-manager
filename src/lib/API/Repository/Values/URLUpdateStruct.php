<?php


namespace EzSystems\EzPlatformLinkManager\API\Repository\Values;

use eZ\Publish\API\Repository\Values\ValueObject;

class URLUpdateStruct extends ValueObject
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
     * @var \DateTimeInterface
     */
    public $lastChecked;
}
