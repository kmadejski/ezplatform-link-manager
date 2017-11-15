<?php

namespace EzSystems\EzPlatformLinkManager\Core\SignalSlot\Signal;

use eZ\Publish\Core\SignalSlot\Signal;

/**
 * Signal emitted when URL is updated.
 */
class UpdateUrlSignal extends Signal
{
    /**
     * UrlId.
     *
     * @var mixed
     */
    public $urlId;
}
