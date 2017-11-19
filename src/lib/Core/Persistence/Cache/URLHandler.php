<?php

namespace EzSystems\EzPlatformLinkManager\Core\Persistence\Cache;

use eZ\Publish\Core\Persistence\Cache\CacheServiceDecorator;
use eZ\Publish\Core\Persistence\Cache\PersistenceLogger;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler as URLHandlerInterface;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URLCreateStruct;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URLUpdateStruct;

/**
 * SPI cache for URL Handler.
 *
 * @see \EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler
 */
class URLHandler implements URLHandlerInterface
{
    /**
     * @var \eZ\Publish\Core\Persistence\Cache\CacheServiceDecorator
     */
    protected $cache;

    /**
     * @var \EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler
     */
    protected $persistenceHandler;

    /**
     * @var \eZ\Publish\Core\Persistence\Cache\PersistenceLogger
     */
    protected $logger;

    /**
     * Setups current handler with everything needed.
     *
     * @param \eZ\Publish\Core\Persistence\Cache\CacheServiceDecorator $cache
     * @param \EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler $persistenceHandler
     * @param \eZ\Publish\Core\Persistence\Cache\PersistenceLogger $logger
     */
    public function __construct(
        CacheServiceDecorator $cache,
        URLHandlerInterface $persistenceHandler,
        PersistenceLogger $logger)
    {
        $this->cache = $cache;
        $this->persistenceHandler = $persistenceHandler;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function createUrl(URLCreateStruct $struct)
    {
        $this->logger->logCall(__METHOD__, [
            'struct' => $struct,
        ]);

        return $this->persistenceHandler->createUrl($struct);
    }

    /**
     * {@inheritdoc}
     */
    public function updateUrl($id, URLUpdateStruct $struct)
    {
        $this->logger->logCall(__METHOD__, [
            'url' => $id,
            'struct' => $struct,
        ]);

        $url = $this->persistenceHandler->updateUrl($id, $struct);

        $this->cache->clear('url', $id);
        // TODO: Find better way to clear content cache for url usages
        $this->cache->clear('content');

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function find(Criterion $criterion, $offset = 0, $limit = -1)
    {
        $this->logger->logCall(__METHOD__, [
            'criteria' => $criterion,
            'offset' => $offset,
            'limit' => $limit,
        ]);

        return $this->persistenceHandler->find($criterion, $offset, $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function load($id)
    {
        $cache = $this->cache->getItem('url', $id);

        $url = $cache->get();
        if ($cache->isMiss()) {
            $this->logger->logCall(__METHOD__, ['url' => $id]);
            $url = $this->persistenceHandler->load($id);
            $cache->set($url)->save();
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelatedContentIds($id)
    {
        $cache = $this->cache->getItem('url', $id, 'usages');

        $usages = $cache->get();
        if ($cache->isMiss()) {
            $this->logger->logCall(__METHOD__, ['url' => $id]);
            $usages = $this->persistenceHandler->getRelatedContentIds($id);
            $cache->set($usages)->save();
        }

        return $usages;
    }
}
