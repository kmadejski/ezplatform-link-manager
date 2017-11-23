<?php

namespace EzSystems\EzPlatformLinkManager\Tests\Core\Persistence\Legacy\URL;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Validity;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Gateway;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Handler;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Mapper;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URL;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URLCreateStruct;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URLUpdateStruct;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $gateway;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mapper;

    /**
     * @var \EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Handler
     */
    private $handler;

    protected function setUp()
    {
        parent::setUp();
        $this->gateway = $this->createMock(Gateway::class);
        $this->mapper = $this->createMock(Mapper::class);
        $this->handler = new Handler($this->gateway, $this->mapper);
    }

    public function testCreateUrl()
    {
        $urlCreateStruct = new URLCreateStruct();

        $url = $this->getUrl();

        $this->mapper
            ->expects($this->once())
            ->method('createURLFromCreateStruct')
            ->with($urlCreateStruct)
            ->will($this->returnValue($url));

        $this->gateway
            ->expects($this->once())
            ->method('insertUrl')
            ->with($url)
            ->will($this->returnValue($url->id));

        $this->assertEquals($url, $this->handler->createUrl($urlCreateStruct));
    }

    public function testUpdateUrl()
    {
        $urlUpdateStruct = new URLUpdateStruct();
        $url = $this->getUrl();

        $this->mapper
            ->expects($this->once())
            ->method('createURLFromUpdateStruct')
            ->with($urlUpdateStruct)
            ->will($this->returnValue($url));

        $this->gateway
            ->expects($this->once())
            ->method('updateUrl')
            ->with($url);

        $this->assertEquals($url, $this->handler->updateUrl($url->id, $urlUpdateStruct));
    }

    public function testFind()
    {
        $criterion = new Validity();

        $results = [
            'count' => 0,
            'rows' => [],
        ];

        $expected = [
            'count' => 0,
            'items' => null,
        ];

        $this->gateway
            ->expects($this->once())
            ->method('find')
            ->with($criterion)
            ->will($this->returnValue($results));

        $this->assertEquals($expected, $this->handler->find($criterion));
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\NotFoundException
     */
    public function testLoadWithoutUrlData()
    {
        $url = $this->getUrl();

        $rows = [];

        $this->gateway
            ->expects($this->once())
            ->method('loadUrlData')
            ->with($url->id)
            ->will($this->returnValue($rows));

        $this->mapper
            ->expects($this->once())
            ->method('extractURLsFromRows')
            ->with($rows);

        $this->handler->load($url->id);
    }

    public function testLoadWithUrlData()
    {
        $urls[] = $this->getUrl();

        $this->gateway
            ->expects($this->once())
            ->method('loadUrlData')
            ->with($urls[0]->id)
            ->will($this->returnValue([]));

        $this->mapper
            ->expects($this->once())
            ->method('extractURLsFromRows')
            ->with([])
            ->will($this->returnValue($urls));

        $this->assertEquals($urls[0], $this->handler->load($urls[0]->id));
    }

    public function testGetRelatedContentIds()
    {
        $url = $this->getUrl();
        $ids = [1, 2, 3];

        $this->gateway
            ->expects($this->once())
            ->method('getRelatedContentIds')
            ->with($url->id)
            ->will($this->returnValue($ids));

        $this->assertEquals($ids, $this->handler->getRelatedContentIds($url->id));
    }

    private function getUrl()
    {
        $url = new URL();
        $url->id = 12;

        return $url;
    }
}
