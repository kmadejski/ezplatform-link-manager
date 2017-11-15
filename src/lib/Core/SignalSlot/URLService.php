<?php

namespace EzSystems\EzPlatformLinkManager\Core\SignalSlot;

use eZ\Publish\Core\SignalSlot\SignalDispatcher;
use EzSystems\EzPlatformLinkManager\API\Repository\URLService as URLServiceInterface;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URL;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLUpdateStruct;
use EzSystems\EzPlatformLinkManager\Core\SignalSlot\Signal\UpdateUrlSignal;

class URLService implements URLServiceInterface
{
    /**
     * Aggregated service.
     *
     * @var \EzSystems\EzPlatformLinkManager\API\Repository\URLService
     */
    protected $service;

    /**
     * SignalDispatcher.
     *
     * @var SignalDispatcher
     */
    protected $signalDispatcher;

    /**
     * URLService constructor.
     *
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\URLService $service
     * @param \eZ\Publish\Core\SignalSlot\SignalDispatcher $signalDispatcher
     */
    public function __construct(URLServiceInterface $service, SignalDispatcher $signalDispatcher)
    {
        $this->service = $service;
        $this->signalDispatcher = $signalDispatcher;
    }

    /**
     * @inheritdoc
     */
    public function createUpdateStruct()
    {
        return $this->service->createUpdateStruct();
    }

    /**
     * @inheritdoc
     */
    public function findUrls(Criterion $criteria, $offset = 0, $limit = -1)
    {
        return $this->service->findUrls($criteria, $offset, $limit);
    }

    /**
     * @inheritdoc
     */
    public function findUsages(URL $url, $offset = 0, $limit = -1)
    {
        return $this->service->findUsages($url, $offset, $limit);
    }

    /**
     * @inheritdoc
     */
    public function loadUrl($id)
    {
        return $this->service->loadUrl($id);
    }

    /**
     * @inheritdoc
     */
    public function updateUrl(URL $url, URLUpdateStruct $struct)
    {
        $returnValue = $this->service->updateUrl($url, $struct);

        $this->signalDispatcher->emit(
            new UpdateUrlSignal([
                'urlId' => $returnValue->id
            ])
        );

        return $returnValue;
    }
}
