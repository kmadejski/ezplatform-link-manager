<?php

namespace EzSystems\EzPlatformLinkManager\URLChecker;

use DateTime;
use eZ\Publish\API\Repository\Repository;
use EzSystems\EzPlatformLinkManager\API\Repository\URLService as URLServiceInterface;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\SearchResult;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URL;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class URLChecker implements URLCheckerInterface
{
    use LoggerAwareTrait;

    /** @var \eZ\Publish\API\Repository\Repository */
    protected $repository;

    /** @var \EzSystems\EzPlatformLinkManager\API\Repository\URLService */
    protected $urlService;

    /** @var \EzSystems\EzPlatformLinkManager\URLChecker\URLHandlerRegistryInterface */
    protected $handlerRegistry;

    /**
     * URLChecker constructor.
     *
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\URLService $urlService
     * @param \EzSystems\EzPlatformLinkManager\URLChecker\URLHandlerRegistryInterface $handlerRegistry
     */
    public function __construct(
        Repository $repository,
        URLServiceInterface $urlService,
        URLHandlerRegistryInterface $handlerRegistry)
    {
        $this->repository = $repository;
        $this->urlService = $urlService;
        $this->handlerRegistry = $handlerRegistry;
        $this->logger = new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function check(Criterion $criterion, $offset = 0, $limit = -1)
    {
        $grouped = $this->fetchUrls($criterion, $offset, $limit);
        foreach ($grouped as $scheme => $urls) {
            if (!$this->handlerRegistry->supported($scheme)) {
                $this->logger->error('Unsupported URL schema: ' . $scheme);
                continue;
            }

            $handler = $this->handlerRegistry->getHandler($scheme);
            $handler->validate($urls, function (URL $url, $isValid) {
                $this->logger->info(sprintf('URL id = %d (%s) was checked (valid = %s)', $url->id, $url->url, (int) $isValid));
                $this->setUrlStatus($url, $isValid);
            });
        }
    }

    /**
     * Fetch URLs to check.
     *
     * @param Criterion $criterion
     * @param int $offset
     * @param int $limit
     * @return array
     */
    protected function fetchUrls(Criterion $criterion, $offset = 0, $limit = -1)
    {
        $urls = $this->repository->sudo(function () use ($criterion, $offset, $limit) {
            return $this->urlService->findUrls($criterion, $offset, $limit);
        });

        return $this->groupByScheme($urls);
    }

    /**
     * Sets URL status.
     *
     * @param URL $url
     * @param bool $isValid
     */
    protected function setUrlStatus(URL $url, $isValid)
    {
        $this->repository->sudo(function () use ($url, $isValid) {
            $updateStruct = $this->urlService->createUpdateStruct();
            $updateStruct->isValid = $isValid;
            $updateStruct->lastChecked = new DateTime();

            $this->urlService->updateUrl($url, $updateStruct);
        });
    }

    /**
     * Group URLs by schema.
     *
     * @param SearchResult $urls
     * @return array
     */
    private function groupByScheme(SearchResult $urls)
    {
        $grouped = [];

        foreach ($urls as $url) {
            $scheme = parse_url($url->url, PHP_URL_SCHEME);
            if (!$scheme) {
                continue;
            }

            if (!isset($grouped[$scheme])) {
                $grouped[$scheme] = [];
            }

            $grouped[$scheme][] = $url;
        }

        return $grouped;
    }
}
