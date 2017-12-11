<?php

namespace EzSystems\EzPlatformLinkManager\Tests\Core\SignalSlot;


use EzSystems\EzPlatformLinkManager\API\Repository\Values\SearchResult;
use eZ\Publish\Core\SignalSlot\SignalDispatcher;
use eZ\Publish\Core\SignalSlot\Tests\ServiceTest;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URL;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLUpdateStruct;
use EzSystems\EzPlatformLinkManager\Core\Repository\URLService as APIURLService;
use EzSystems\EzPlatformLinkManager\Core\SignalSlot\Signal\UpdateUrlSignal;
use EzSystems\EzPlatformLinkManager\Core\SignalSlot\URLService;

class URLServiceTest extends ServiceTest
{
    protected function getServiceMock()
    {
        return $this->createMock(APIURLService::class);
    }

    protected function getSignalSlotService($innerService, SignalDispatcher $dispatcher)
    {
        return new URLService($innerService, $dispatcher);
    }

    public function serviceProvider()
    {
        $urlUpdateStruct = new URLUpdateStruct(['url' => 'http://ezplatform.com']);
        $url = $this->getApiUrl(12, 'http://ez.no');
        $updatedUrl = $this->getApiUrl(12, 'http://ezplatform.com');

        return [
            [
                'updateUrl',
                array($url, $urlUpdateStruct),
                $updatedUrl,
                1,
                UpdateUrlSignal::class,
                array('urlId' => $updatedUrl->id)
            ],
            [
                'createUpdateStruct',
                array(),
                new URLUpdateStruct(),
                0
            ],
            [
                'findUrls',
                array(new URLQuery()),
                new SearchResult(),
                0
            ],
            [
                'loadById',
                array(12),
                $url,
                0
            ],
            [
                'loadByUrl',
                array('http://ez.no'),
                $url,
                0
            ]
        ];
    }

    private function getApiUrl($id = null, $url = null)
    {
        return new URL(['id' => $id, 'url' => $url]);
    }
}
