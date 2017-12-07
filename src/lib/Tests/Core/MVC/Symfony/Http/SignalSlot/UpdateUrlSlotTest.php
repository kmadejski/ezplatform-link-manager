<?php

namespace EzSystems\EzPlatformLinkManager\Tests\MVC\Symfony\Http\SignalSlot;

use eZ\Publish\Core\MVC\Symfony\Cache\Tests\Http\SignalSlot\AbstractPurgeAllSlotTest;
use eZ\Publish\Core\SignalSlot\Signal\ContentService\UpdateContentSignal;
use EzSystems\EzPlatformLinkManager\Core\MVC\Symfony\Http\SignalSlot\UpdateUrlSlot;
use EzSystems\EzPlatformLinkManager\Core\SignalSlot\Signal\UpdateUrlSignal;

class UpdateUrlSlotTest extends AbstractPurgeAllSlotTest
{
    /**
     * {@inheritdoc}
     */
    public static function createSignal()
    {
        return new UpdateUrlSignal([
            'urlId' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getReceivedSignalClasses()
    {
        return [
            UpdateContentSignal::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSlotClass()
    {
        return UpdateUrlSlot::class;
    }
}
