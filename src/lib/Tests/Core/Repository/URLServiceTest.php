<?php

namespace EzSystems\EzPlatformLinkManager\Tests\Core\Repository;

use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use eZ\Publish\Core\Repository\Repository;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Validity;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\SearchResult;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URL;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLUpdateStruct;
use EzSystems\EzPlatformLinkManager\Core\Repository\URLService;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URL as SPIURL;
use PHPUnit\Framework\TestCase;

class URLServiceTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $urlHandler;

    /**
     * @var \EzSystems\EzPlatformLinkManager\Core\Repository\URLService
     */
    private $urlService;

    protected function setUp()
    {
        parent::setUp();
        $this->repository = $this->createMock(Repository::class);
        $this->urlHandler = $this->createMock(Handler::class);
        $this->urlService = new URLService($this->repository, $this->urlHandler);
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    public function testFindUrlsUnauthorized()
    {
        $criterion = new Validity();
        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'view')
            ->will($this->returnValue(false));

        $this->urlService->findUrls($criterion);
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue
     */
    public function testFindUrlNonNumericOffset()
    {
        $criterion = new Validity();

        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'view')
            ->will($this->returnValue(true));

        $this->urlService->findUrls($criterion, 'foo');
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue
     */
    public function testFindUrlNonNumericLimit()
    {
        $criterion = new Validity();

        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'view')
            ->will($this->returnValue(true));

        $this->urlService->findUrls($criterion, 0, 'foo');
    }

    public function testFindUrl()
    {
        $criterion = new Validity();
        $url = $this->getUrl();

        $results = [
            'count' => 1,
            'items' => [
                new SPIURL(),
            ],
        ];

        $expected = new SearchResult([
            'count' => 1,
            'items' => [$url],
        ]);

        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'view')
            ->will($this->returnValue(true));

        $this->urlHandler
            ->expects($this->once())
            ->method('find')
            ->with($criterion)
            ->will($this->returnValue($results));

        $this->assertEquals($expected, $this->urlService->findUrls($criterion));
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    public function testUpdateUrlUnauthorized()
    {
        $url = $this->getUrl();
        $urlUpdateStruct = new URLUpdateStruct();

        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'update')
            ->will($this->returnValue(false));

        $this->urlService->updateUrl($url, $urlUpdateStruct);
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    public function testLoadUrlUnauthorized()
    {
        $url = $this->getUrl();

        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'view')
            ->will($this->returnValue(false));

        $this->urlService->loadUrl($url->id);
    }

    public function testLoadUrl()
    {
        $urlId = 12;

        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'view')
            ->will($this->returnValue(true));

        $spiUrl = new SPIURL();
        $spiUrl->id = $urlId;

        $this->urlHandler
            ->expects($this->once())
            ->method('load')
            ->with(12)
            ->will($this->returnValue($spiUrl));

        $buildDomainObject = new \ReflectionMethod(URLService::class, 'buildDomainObject');
        $buildDomainObject->setAccessible(true);
        $apiUrl = $buildDomainObject->invoke($this->urlService, $spiUrl);

        $this->assertEquals($apiUrl, $this->urlService->loadUrl($urlId));
    }

    public function testCreateUpdateStruct()
    {
        $urlUpdateStruct = new URLUpdateStruct();
        $this->assertEquals($urlUpdateStruct, $this->urlService->createUpdateStruct());
    }

    private function getUrl()
    {
        return new URL();
    }
}