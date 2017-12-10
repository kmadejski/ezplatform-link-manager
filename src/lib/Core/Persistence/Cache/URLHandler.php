<?php

namespace EzSystems\EzPlatformLinkManager\Core\Persistence\Cache;

use eZ\Publish\Core\Persistence\Cache\CacheServiceDecorator;
use eZ\Publish\Core\Persistence\Cache\PersistenceLogger;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler as URLHandlerInterface;
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
    public function updateUrl($id, URLUpdateStruct $struct)
    {
        $this->logger->logCall(__METHOD__, [
            'url' => $id,
            'struct' => $struct,
        ]);

        $url = $this->persistenceHandler->updateUrl($id, $struct);

        $this->cache->clear('url', $id);
        $this->cache->clear('content');

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function find(URLQuery $query)
    {
        $this->logger->logCall(__METHOD__, [
            'query' => $query,
        ]);

        return $this->persistenceHandler->find($query);
    }

    /**
     * {@inheritdoc}
     */
    public function loadById($id)
    {
        $cache = $this->cache->getItem('url', $id);

        $url = $cache->get();
        if ($cache->isMiss()) {
            $this->logger->logCall(__METHOD__, ['url' => $id]);
            $url = $this->persistenceHandler->loadById($id);
            $cache->set($url)->save();
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function loadByUrl($url)
    {
        return $this->persistenceHandler->loadByUrl($url);
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
