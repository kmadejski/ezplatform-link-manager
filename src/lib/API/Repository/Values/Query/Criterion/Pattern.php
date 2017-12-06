<?php

namespace EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;

class Pattern extends Matcher
{
    /**
     * @var string|null
     */
    public $pattern;

    /**
     * Pattern constructor.
     *
     * @param string|null $pattern
     */
    public function __construct($pattern = null)
    {
        $this->pattern = $pattern;
    }
}
