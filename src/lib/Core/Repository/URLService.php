<?php

namespace EzSystems\EzPlatformLinkManager\Core\Repository;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use EzSystems\EzPlatformLinkManager\API\Repository\URLService as URLServiceInterface;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\SearchResult;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URL;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLUpdateStruct;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler as URLHandler;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URL as SPIUrl;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URLUpdateStruct as SPIUrlUpdateStruct;
use DateTime;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion as ContentCriterion;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;

class URLService implements URLServiceInterface
{
    /**
     * @var \eZ\Publish\Core\Repository\Repository
     */
    protected $repository;

    /**
     * @var \EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler
     */
    protected $urlHandler;

    /**
     * URLService constructor.
     *
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler $urlHandler
     */
    public function __construct(Repository $repository, URLHandler $urlHandler)
    {
        $this->repository = $repository;
        $this->urlHandler = $urlHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function findUrls(Criterion $criteria, $offset = 0, $limit = -1)
    {
        if ($this->repository->hasAccess('url', 'view') !== true) {
            throw new UnauthorizedException('url', 'view');
        }

        if ($offset !== null && !is_numeric($offset)) {
            throw new InvalidArgumentValue('offset', $offset);
        }

        if ($limit !== null && !is_numeric($limit)) {
            throw new InvalidArgumentValue('limit', $limit);
        }

        $results = $this->urlHandler->find($criteria, $offset, $limit);

        $items = [];
        foreach ($results['items'] as $url) {
            $items[] = $this->buildDomainObject($url);
        }

        return new SearchResult([
            'count' => $results['count'],
            'items' => $items,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function updateUrl(URL $url, URLUpdateStruct $struct)
    {
        if ($this->repository->hasAccess('url', 'update') !== true) {
            throw new UnauthorizedException('url', 'update');
        }

        if (!$this->isUnique($url->id, $struct->url)) {
            throw new InvalidArgumentException('struct', 'url already exists');
        }

        $updateStruct = $this->buildUpdateStruct($this->loadById($url->id), $struct);

        $this->repository->beginTransaction();
        try {
            $this->urlHandler->updateUrl($url->id, $updateStruct);
            $this->repository->commit();
        } catch (\Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->loadById($url->id);
    }

    /**
     * {@inheritdoc}
     */
    public function loadById($id)
    {
        if ($this->repository->hasAccess('url', 'view') !== true) {
            throw new UnauthorizedException('url', 'view');
        }

        return $this->buildDomainObject(
            $this->urlHandler->load($id)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function loadByUrl($url)
    {
        if ($this->repository->hasAccess('url', 'view') !== true) {
            throw new UnauthorizedException('url', 'view');
        }

        return $this->buildDomainObject(
            $this->urlHandler->loadByUrl($url)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createUpdateStruct()
    {
        return new URLUpdateStruct();
    }

    /**
     * {@inheritdoc}
     */
    public function findUsages(URL $url, $offset = 0, $limit = -1)
    {
        $usages = $this->urlHandler->getRelatedContentIds($url->id);

        $query = new Query();
        if (!empty($usages)) {
            $query->filter = new ContentCriterion\LogicalAnd([
                new ContentCriterion\ContentId($usages),
                new ContentCriterion\Visibility(ContentCriterion\Visibility::VISIBLE),
            ]);
        } else {
            $query->filter = new ContentCriterion\MatchNone();
        }

        $query->offset = $offset;
        if ($limit > -1) {
            $query->limit = $limit;
        }

        return $this->repository->getSearchService()->findContentInfo($query);
    }

    protected function buildDomainObject(SPIUrl $data)
    {
        return new URL([
            'id' => $data->id,
            'url' => $data->url,
            'originalUrlMd5' => $data->originalUrlMd5,
            'isValid' => $data->isValid,
            'lastChecked' => $this->createDateTime($data->lastChecked),
            'created' => $this->createDateTime($data->created),
            'modified' => $this->createDateTime($data->modified),
        ]);
    }

    protected function buildUpdateStruct(URL $url, URLUpdateStruct $data)
    {
        $updateStruct = new SPIUrlUpdateStruct();

        if ($data->url !== null) {
            $updateStruct->url = $data->url;
        } else {
            $updateStruct->url = $url->url;
        }

        if ($data->lastChecked !== null) {
            $updateStruct->lastChecked = $data->lastChecked->getTimestamp();
        } elseif ($data->lastChecked !== null) {
            $updateStruct->lastChecked = $url->lastChecked->getTimestamp();
        } else {
            $updateStruct->lastChecked = 0;
        }

        if ($data->isValid !== null) {
            $updateStruct->isValid = $data->isValid;
        } else {
            $updateStruct->isValid = $url->isValid;
        }

        return $updateStruct;
    }

    /**
     * Check if URL is unique.
     *
     * @param int $id
     * @param string $url
     * @return bool
     * @throws \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    protected function isUnique($id, $url)
    {
        try {
            return $this->loadByUrl($url)->id === $id;
        } catch (NotFoundException $e) {
            return true;
        }
    }

    private function createDateTime($timestamp)
    {
        if ($timestamp > 0) {
            return new DateTime("@{$timestamp}");
        }

        return null;
    }
}
