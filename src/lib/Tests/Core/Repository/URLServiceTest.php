<?php

namespace EzSystems\EzPlatformLinkManager\Tests\Core\Repository;

use eZ\Publish\Core\Repository\Repository;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\SearchResult;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URL;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;
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
        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'view')
            ->will($this->returnValue(false));

        $this->urlService->findUrls(new URLQuery());
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue
     */
    public function testFindUrlsNonNumericOffset()
    {
        $query = new URLQuery();
        $query->offset = 'foo';

        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'view')
            ->will($this->returnValue(true));

        $this->urlService->findUrls($query);
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue
     */
    public function testFindUrlsNonNumericLimit()
    {
        $query = new URLQuery();
        $query->limit = 'foo';

        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'view')
            ->will($this->returnValue(true));

        $this->urlService->findUrls($query);
    }

    public function testFindUrls()
    {
        $query = new URLQuery();

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
            ->with($query)
            ->will($this->returnValue($results));

        $this->assertEquals($expected, $this->urlService->findUrls($query));
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
    public function testLoadByIdUnauthorized()
    {
        $url = $this->getUrl();

        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'view')
            ->will($this->returnValue(false));

        $this->urlService->loadById($url->id);
    }

    public function testLoadById()
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
            ->method('loadById')
            ->with($urlId)
            ->will($this->returnValue($spiUrl));

        $buildDomainObject = new \ReflectionMethod(URLService::class, 'buildDomainObject');
        $buildDomainObject->setAccessible(true);
        $apiUrl = $buildDomainObject->invoke($this->urlService, $spiUrl);

        $this->assertEquals($apiUrl, $this->urlService->loadById($urlId));
    }

    public function testCreateUpdateStruct()
    {
        $urlUpdateStruct = new URLUpdateStruct();
        $this->assertEquals($urlUpdateStruct, $this->urlService->createUpdateStruct());
    }

    private function getUrl($id = null)
    {
        return new URL(['id' => $id]);
    }
}
