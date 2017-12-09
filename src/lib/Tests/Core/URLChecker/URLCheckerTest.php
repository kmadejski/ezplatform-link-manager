<?php

namespace EzSystems\EzPlatformLinkManager\Tests\Core\URLChecker;

use EzSystems\EzPlatformLinkManager\API\Repository\URLService;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\SearchResult;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URL;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLUpdateStruct;
use EzSystems\EzPlatformLinkManager\URLChecker\URLChecker;
use EzSystems\EzPlatformLinkManager\URLChecker\URLHandlerInterface;
use EzSystems\EzPlatformLinkManager\URLChecker\URLHandlerRegistryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class URLCheckerTest extends TestCase
{
    /** @var \EzSystems\EzPlatformLinkManager\API\Repository\URLService|\PHPUnit_Framework_MockObject_MockObject */
    private $urlService;

    /** @var \EzSystems\EzPlatformLinkManager\URLChecker\URLHandlerRegistryInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $handlerRegistry;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    protected function setUp()
    {
        $this->urlService = $this->createMock(URLService::class);
        $this->urlService
            ->expects($this->any())
            ->method('createUpdateStruct')
            ->willReturnCallback(function () {
                return new URLUpdateStruct();
            });

        $this->handlerRegistry = $this->createMock(URLHandlerRegistryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testCheck()
    {
        $query = new URLQuery();
        $groups = $this->createGroupedUrls(['http', 'https']);

        $this->urlService
            ->expects($this->once())
            ->method('findUrls')
            ->with($query)
            ->willReturn($this->createSearchResults($groups));

        $handlers = [
            'http' => $this->createMock(URLHandlerInterface::class),
            'https' => $this->createMock(URLHandlerInterface::class),
        ];

        foreach ($handlers as $scheme => $handler) {
            $handler
                ->expects($this->once())
                ->method('validate')
                ->willReturnCallback(function (array $urls, $callback) use ($scheme, $groups) {
                    $this->assertEqualsArrays($groups[$scheme], $urls);
                });
        }

        $this->configureUrlHandlerRegistry($handlers);

        $urlChecker = $this->createUrlChecker();
        $urlChecker->check($query);
    }

    public function testCheckUnsupported()
    {
        $query = new URLQuery();
        $groups = $this->createGroupedUrls(['http', 'https'], 10);

        $this->urlService
            ->expects($this->once())
            ->method('findUrls')
            ->with($query)
            ->willReturn($this->createSearchResults($groups));

        $this->logger
            ->expects($this->atLeastOnce())
            ->method('error')
            ->with('Unsupported URL schema: https');

        $handlers = [
            'http' => $this->createMock(URLHandlerInterface::class),
        ];

        foreach ($handlers as $scheme => $handler) {
            $handler
                ->expects($this->once())
                ->method('validate')
                ->willReturnCallback(function (array $urls, $callback) use ($scheme, $groups) {
                    $this->assertEqualsArrays($groups[$scheme], $urls);
                });
        }

        $this->configureUrlHandlerRegistry($handlers);

        $urlChecker = $this->createUrlChecker();
        $urlChecker->check($query);
    }

    protected function assertEqualsArrays(array $expected, array $actual, $message = '')
    {
        sort($expected);
        sort($actual);

        $this->assertEquals($expected, $actual, $message);
    }

    private function configureUrlHandlerRegistry(array $schemes)
    {
        $this->handlerRegistry
            ->method('supported')
            ->willReturnCallback(function ($scheme) use ($schemes) {
                return isset($schemes[$scheme]);
            });

        $this->handlerRegistry
            ->method('getHandler')
            ->willReturnCallback(function ($scheme) use ($schemes) {
                return $schemes[$scheme];
            });
    }

    private function createSearchResults(array &$urls)
    {
        $input = array_reduce($urls, 'array_merge', []);

        shuffle($input);

        return new SearchResult([
            'count' => count($input),
            'items' => $input,
        ]);
    }

    private function createGroupedUrls(array $schemes, $n = 10)
    {
        $results = [];

        foreach ($schemes as $i => $scheme) {
            $results[$scheme] = [];
            for ($j = 0; $j < $n; ++$j) {
                $results[$scheme][] = new URL([
                    'id' => $i * 100 + $j,
                    'url' => $scheme . '://' . $j,
                ]);
            }
        }

        return $results;
    }

    /**
     * @return \EzSystems\EzPlatformLinkManager\URLChecker\URLChecker
     */
    private function createUrlChecker()
    {
        $urlChecker = new URLChecker(
            $this->urlService,
            $this->handlerRegistry
        );
        $urlChecker->setLogger($this->logger);

        return $urlChecker;
    }
}
