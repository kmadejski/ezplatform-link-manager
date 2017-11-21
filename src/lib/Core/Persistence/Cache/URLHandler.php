<?php

namespace EzSystems\EzPlatformLinkManager\Core\Persistence\Cache;

use eZ\Publish\Core\Persistence\Cache\PersistenceLogger;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler as URLHandlerInterface;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URLCreateStruct;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URLUpdateStruct;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

/**
 * SPI cache for URL Handler.
 *
 * @see \EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler
 */
class URLHandler implements URLHandlerInterface
{
    /**
     * @var \Symfony\Component\Cache\Adapter\TagAwareAdapterInterface
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
     * @param \Symfony\Component\Cache\Adapter\TagAwareAdapterInterface $cache
     * @param \EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler $persistenceHandler
     * @param \eZ\Publish\Core\Persistence\Cache\PersistenceLogger $logger
     */
    public function __construct(
        TagAwareAdapterInterface $cache,
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

        $this->cache->invalidateTags(['url-' . $id]);

        $usages = $this->getRelatedContentIds($id);
        if (!empty($usages)) {
            $this->cache->invalidateTags(array_map(function($id) {
                return 'content-' . $id;
            }, $usages));
        }

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
        $cacheItem = $this->cache->getItem('ez-url-' . $id);

        $url = $cacheItem->get();
        if ($cacheItem->isHit()) {
            return $url;
        }

        $this->logger->logCall(__METHOD__, ['url' => $id]);
        $url = $this->persistenceHandler->load($id);

        $cacheItem->set($url);
        $cacheItem->tag(['url-' . $id]);
        $this->cache->save($cacheItem);

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelatedContentIds($id)
    {
        $cacheItem = $this->cache->getItem('ez-url-' . $id . '-usages');

        $usages = $cacheItem->get();
        if ($cacheItem->isHit()) {
            return $usages;
        }

        $this->logger->logCall(__METHOD__, ['url' => $id]);
        $usages = $this->persistenceHandler->getRelatedContentIds($id);

        $cacheItem->set($usages);
        $cacheItem->tag(['url-' . $id . '-usages']);
        $this->cache->save($cacheItem);

        return $usages;
    }
}
