<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Tests\Pagination\Pagerfanta;

use EzSystems\EzPlatformLinkManager\API\Repository\URLService;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\SortClause;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\SearchResult;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URL;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;
use EzSystems\EzPlatformLinkManagerBundle\Pagination\Pagerfanta\URLSearchAdapter;
use PHPUnit\Framework\TestCase;

class URLSearchAdapterTest extends TestCase
{
    /** @var \EzSystems\EzPlatformLinkManager\API\Repository\URLService|\PHPUnit_Framework_MockObject_MockObject */
    private $urlService;

    protected function setUp()
    {
        $this->urlService = $this->createMock(URLService::class);
    }

    public function testGetNbResults()
    {
        $query = $this->createURLQuery();

        $searchResults = new SearchResult([
            'items' => [],
            'count' => 13,
        ]);

        $this->urlService
            ->expects($this->once())
            ->method('findUrls')
            ->willReturnCallback(function (URLQuery $q) use ($query, $searchResults) {
                $this->assertEquals($query->filter, $q->filter);
                $this->assertEquals($query->sortClauses, $q->sortClauses);
                $this->assertEquals(0, $q->offset);
                $this->assertEquals(0, $q->limit);

                return $searchResults;
            });

        $adapter = new URLSearchAdapter($query, $this->urlService);

        $this->assertEquals($searchResults->count, $adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $query = $this->createURLQuery();
        $limit = 25;
        $offset = 10;

        $searchResults = new SearchResult([
            'items' => [
                $this->createMock(URL::class),
                $this->createMock(URL::class),
                $this->createMock(URL::class),
            ],
            'count' => 13,
        ]);

        $this->urlService
            ->expects($this->once())
            ->method('findUrls')
            ->willReturnCallback(function (URLQuery $q) use ($query, $limit, $offset, $searchResults) {
                $this->assertEquals($query->filter, $q->filter);
                $this->assertEquals($query->sortClauses, $q->sortClauses);
                $this->assertEquals($limit, $q->limit);
                $this->assertEquals($offset, $q->offset);

                return $searchResults;
            });

        $adapter = new URLSearchAdapter($query, $this->urlService);

        $this->assertEquals($searchResults->items, $adapter->getSlice($offset, $limit));
    }

    private function createURLQuery()
    {
        $query = new URLQuery();
        $query->filter = new Criterion\MatchAll();
        $query->sortClauses = [
            new SortClause\Id(),
        ];

        return $query;
    }
}
