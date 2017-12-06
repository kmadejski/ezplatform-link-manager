<?php

namespace EzSystems\EzPlatformLinkManager\Core\MVC\Symfony\Http\SignalSlot;

use eZ\Publish\Core\MVC\Symfony\Cache\GatewayCachePurger;
use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\Core\SignalSlot\Slot;
use EzSystems\EzPlatformLinkManager\Core\SignalSlot\Signal\UpdateUrlSignal;

class UpdateUrlSlot extends Slot
{
    /**
     * @var \eZ\Publish\Core\MVC\Symfony\Cache\GatewayCachePurger
     */
    protected $httpCacheClearer;

    /**
     * UpdateUrlSlot constructor.
     *
     * @param GatewayCachePurger $httpCacheClearer
     */
    public function __construct(GatewayCachePurger $httpCacheClearer)
    {
        $this->httpCacheClearer = $httpCacheClearer;
    }

    /**
     * {@inheritdoc}
     */
    public function receive(Signal $signal)
    {
        if ($signal instanceof UpdateUrlSignal) {
            $this->httpCacheClearer->purgeAll();
        }
    }
}
