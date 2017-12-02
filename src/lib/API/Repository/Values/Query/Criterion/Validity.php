<?php

namespace EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;

class Validity extends Matcher
{
    /**
     * @var bool|null
     */
    public $isValid;

    /**
     * Validity constructor.
     *
     * @param bool|null $isValid
     */
    public function __construct($isValid = null)
    {
        $this->isValid = $isValid;
    }
}
