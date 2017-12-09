<?php

namespace EzSystems\EzPlatformLinkManager\URLChecker;

use DateTime;
use EzSystems\EzPlatformLinkManager\API\Repository\URLService as URLServiceInterface;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\SearchResult;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URL;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class URLChecker implements URLCheckerInterface
{
    use LoggerAwareTrait;

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
        URLServiceInterface $urlService,
        URLHandlerRegistryInterface $handlerRegistry)
    {
        $this->urlService = $urlService;
        $this->handlerRegistry = $handlerRegistry;
        $this->logger = new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function check(URLQuery $query)
    {
        $grouped = $this->fetchUrls($query);
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
     * @param URLQuery $query
     * @return array
     */
    protected function fetchUrls(URLQuery $query)
    {
        return $this->groupByScheme(
            $this->urlService->findUrls($query)
        );
    }

    /**
     * Sets URL status.
     *
     * @param URL $url
     * @param bool $isValid
     */
    protected function setUrlStatus(URL $url, $isValid)
    {
        $updateStruct = $this->urlService->createUpdateStruct();
        $updateStruct->isValid = $isValid;
        $updateStruct->lastChecked = new DateTime();

        $this->urlService->updateUrl($url, $updateStruct);
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
