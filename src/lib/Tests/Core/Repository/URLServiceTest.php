<?php

namespace EzSystems\EzPlatformLinkManager\Tests\Core\Repository;

use DateTime;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion as ContentCriterion;
use eZ\Publish\API\Repository\Values\Content\Query as ContentQuery;
use eZ\Publish\API\Repository\Repository;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\SearchResult;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URL;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLUpdateStruct;
use EzSystems\EzPlatformLinkManager\Core\Repository\URLService;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URL as SpiUrl;
use PHPUnit\Framework\TestCase;

class URLServiceTest extends TestCase
{
    /**
     * @var \eZ\Publish\Core\Repository\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * @var \EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\Handler|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlHandler;

    protected function setUp()
    {
        parent::setUp();
        $this->repository = $this->createMock(Repository::class);
        $this->urlHandler = $this->createMock(Handler::class);
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    public function testFindUrlsUnauthorized()
    {
        $this->configureUrlViewPermission(false);

        $this->createUrlService()->findUrls(new URLQuery());
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue
     */
    public function testFindUrlsNonNumericOffset()
    {
        $this->configureUrlViewPermission(true);

        $query = new URLQuery();
        $query->offset = 'foo';

        $this->createUrlService()->findUrls($query);
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue
     */
    public function testFindUrlsNonNumericLimit()
    {
        $this->configureUrlViewPermission(true);

        $query = new URLQuery();
        $query->limit = 'foo';

        $this->createUrlService()->findUrls($query);
    }

    public function testFindUrls()
    {
        $this->configureUrlViewPermission(true);

        $query = new URLQuery();

        $url = $this->getApiUrl();

        $results = [
            'count' => 1,
            'items' => [
                new SpiUrl(),
            ],
        ];

        $expected = new SearchResult([
            'count' => 1,
            'items' => [$url],
        ]);

        $this->urlHandler
            ->expects($this->once())
            ->method('find')
            ->with($query)
            ->willReturn($results);

        $this->assertEquals($expected, $this->createUrlService()->findUrls($query));
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    public function testUpdateUrlUnauthorized()
    {
        $url = $this->getApiUrl();

        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'update')
            ->willReturn(false);

        $this->createUrlService()->updateUrl($url, new URLUpdateStruct());
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    public function testUpdateUrlNonUnique()
    {
        $this->configureUrlUpdatePermission(true);

        $url = $this->getApiUrl(1, 'http://ez.no');
        $struct = new URLUpdateStruct([
            'url' => 'http://ez.com',
        ]);

        $urlService = $this->createUrlService(['isUnique']);
        $urlService
            ->expects($this->once())
            ->method('isUnique')
            ->with($url->id, $struct->url)
            ->willReturn(false);

        $urlService->updateUrl($url, $struct);
    }

    public function testUpdateUrl()
    {
        $apiUrl = $this->getApiUrl(1, 'http://ez.no');
        $apiStruct = new URLUpdateStruct([
            'url' => 'http://ez.com',
            'isValid' => false,
            'lastChecked' => new DateTime(),
        ]);

        $this->configurePermissions([
            ['url', 'update', null],
            ['url', 'view', null],
            ['url', 'view', null],
        ]);

        $urlService = $this->createUrlService(['isUnique']);
        $urlService
            ->expects($this->once())
            ->method('isUnique')
            ->with($apiUrl->id, $apiStruct->url)
            ->willReturn(true);

        $this->urlHandler
            ->expects($this->once())
            ->method('updateUrl')
            ->willReturnCallback(function ($id, $struct) use ($apiUrl, $apiStruct) {
                $this->assertEquals($apiUrl->id, $id);

                $this->assertEquals($apiStruct->url, $struct->url);
                $this->assertEquals(0, $struct->lastChecked);
                $this->assertTrue($struct->isValid);
            });

        $this->urlHandler
            ->method('loadById')
            ->with($apiUrl->id)
            ->willReturnOnConsecutiveCalls(
                new SpiUrl([
                    'id' => $apiUrl->id,
                    'url' => $apiUrl->url,
                    'isValid' => $apiUrl->isValid,
                    'lastChecked' => $apiUrl->lastChecked,
                ]),
                new SpiUrl([
                    'id' => $apiUrl->id,
                    'url' => $apiStruct->url,
                    'isValid' => true,
                    'lastChecked' => 0,
                ])
            );

        $this->assertEquals(new URL([
            'id' => $apiUrl->id,
            'url' => $apiStruct->url,
            'isValid' => true,
            'lastChecked' => null,
        ]), $urlService->updateUrl($apiUrl, $apiStruct));
    }

    public function testUpdateUrlStatus()
    {
        $apiUrl = $this->getApiUrl(1, 'http://ez.no');
        $apiStruct = new URLUpdateStruct([
            'isValid' => true,
            'lastChecked' => new DateTime('@' . time()),
        ]);

        $this->configurePermissions([
            ['url', 'update', null],
            ['url', 'view', null],
            ['url', 'view', null],
        ]);

        $urlService = $this->createUrlService(['isUnique']);
        $urlService
            ->expects($this->once())
            ->method('isUnique')
            ->with($apiUrl->id, $apiStruct->url)
            ->willReturn(true);

        $this->urlHandler
            ->expects($this->once())
            ->method('updateUrl')
            ->willReturnCallback(function ($id, $struct) use ($apiUrl, $apiStruct) {
                $this->assertEquals($apiUrl->id, $id);

                $this->assertEquals($apiUrl->url, $struct->url);
                $this->assertEquals($apiStruct->lastChecked->getTimestamp(), $struct->lastChecked);
                $this->assertTrue($apiStruct->isValid, $struct->isValid);
            });

        $this->urlHandler
            ->method('loadById')
            ->with($apiUrl->id)
            ->willReturnOnConsecutiveCalls(
                new SpiUrl([
                    'id' => $apiUrl->id,
                    'url' => $apiUrl->url,
                    'isValid' => $apiUrl->isValid,
                    'lastChecked' => $apiUrl->lastChecked,
                ]),
                new SpiUrl([
                    'id' => $apiUrl->id,
                    'url' => $apiUrl->url,
                    'isValid' => $apiStruct->isValid,
                    'lastChecked' => $apiStruct->lastChecked->getTimestamp(),
                ])
            );

        $this->assertEquals(new URL([
            'id' => $apiUrl->id,
            'url' => $apiUrl->url,
            'isValid' => $apiStruct->isValid,
            'lastChecked' => $apiStruct->lastChecked,
        ]), $urlService->updateUrl($apiUrl, $apiStruct));
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    public function testLoadByIdUnauthorized()
    {
        $this->configureUrlViewPermission(false);

        $this->createUrlService()->loadById(1);
    }

    public function testLoadById()
    {
        $this->configureUrlViewPermission(true);

        $urlId = 12;

        $this->urlHandler
            ->expects($this->once())
            ->method('loadById')
            ->with($urlId)
            ->willReturn(new SpiUrl([
                'id' => $urlId,
            ]));

        $this->assertEquals(new URL([
            'id' => $urlId,
        ]), $this->createUrlService()->loadById($urlId));
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     */
    public function testLoadByUrlUnauthorized()
    {
        $this->configureUrlViewPermission(false);

        $this->createUrlService()->loadByUrl('http://ez.no');
    }

    public function testLoadByUrl()
    {
        $this->configureUrlViewPermission(true);

        $url = 'http://ez.no';

        $this->urlHandler
            ->expects($this->once())
            ->method('loadByUrl')
            ->with($url)
            ->willReturn(new SpiUrl([
                'url' => $url,
            ]));

        $this->assertEquals(new URL([
            'url' => $url,
        ]), $this->createUrlService()->loadByUrl($url));
    }

    /**
     * @dataProvider dateProviderForFindUsages
     */
    public function testFindUsages($offset, $limit, ContentQuery $expectedQuery, array $usages)
    {
        $url = $this->getApiUrl(1, 'http://ez.no');

        $searchService = $this->createMock(SearchService::class);
        $searchService
            ->expects($this->once())
            ->method('findContentInfo')
            ->willReturnCallback(function ($query) use ($expectedQuery) {
                $this->assertEquals($expectedQuery, $query);
            });

        $this->repository
            ->expects($this->once())
            ->method('getSearchService')
            ->willReturn($searchService);

        $this->urlHandler
            ->expects($this->once())
            ->method('findUsages')
            ->with($url->id)
            ->willReturn($usages);

        $this->createUrlService()->findUsages($url, $offset, $limit);
    }

    public function dateProviderForFindUsages()
    {
        return [
            [
                10, -1, new ContentQuery([
                    'filter' => new ContentCriterion\MatchNone(),
                    'offset' => 10,
                ]), [],
            ],
            [
                10, -1, new ContentQuery([
                    'filter' => new ContentCriterion\LogicalAnd([
                        new ContentCriterion\ContentId([1, 2, 3]),
                        new ContentCriterion\Visibility(ContentCriterion\Visibility::VISIBLE),
                    ]),
                    'offset' => 10,
                ]), [1, 2, 3],
            ],
            [
                10, 10, new ContentQuery([
                    'filter' => new ContentCriterion\LogicalAnd([
                        new ContentCriterion\ContentId([1, 2, 3]),
                        new ContentCriterion\Visibility(ContentCriterion\Visibility::VISIBLE),
                    ]),
                    'offset' => 10,
                    'limit' => 10,
                ]), [1, 2, 3],
            ],
        ];
    }

    public function testCreateUpdateStruct()
    {
        $this->assertEquals(new URLUpdateStruct(), $this->createUrlService()->createUpdateStruct());
    }

    protected function configureUrlViewPermission($hasAccess = false)
    {
        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'view')
            ->willReturn($hasAccess);
    }

    protected function configureUrlUpdatePermission($hasAccess = false)
    {
        $this->repository
            ->expects($this->once())
            ->method('hasAccess')
            ->with('url', 'update')
            ->willReturn($hasAccess);
    }

    protected function configurePermissions(array $permissions)
    {
        $this->repository
            ->expects($this->exactly(count($permissions)))
            ->method('hasAccess')
            ->withConsecutive(...$permissions)
            ->willReturn(true);
    }

    /**
     * @return \EzSystems\EzPlatformLinkManager\Core\Repository\URLService|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createUrlService(array $methods = null)
    {
        return $this
            ->getMockBuilder(URLService::class)
            ->setConstructorArgs([$this->repository, $this->urlHandler])
            ->setMethods($methods)
            ->getMock();
    }

    private function getApiUrl($id = null, $url = null)
    {
        return new URL(['id' => $id, 'url' => $url]);
    }
}
