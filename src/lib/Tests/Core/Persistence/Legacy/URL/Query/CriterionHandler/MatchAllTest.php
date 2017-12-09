<?php

namespace EzSystems\EzPlatformLinkManager\Tests\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use eZ\Publish\Core\Persistence\Database\SelectQuery;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler\MatchAll as MatchAllHandler;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion\MatchAll;

class MatchAllTest extends CriterionHandlerTest
{
    /**
     * {@inheritdoc}
     */
    public function testAccept()
    {
        $handler = new MatchAllHandler();

        $this->assertHandlerAcceptsCriterion($handler, MatchAll::class);
        $this->assertHandlerRejectsCriterion($handler, Criterion::class);
    }

    /**
     * {@inheritdoc}
     */
    public function testHandle()
    {
        $criterion = new MatchAll();
        $expected = ':value';

        $query = $this->createMock(SelectQuery::class);
        $query
            ->expects($this->once())
            ->method('bindValue')
            ->with('1')
            ->willReturn(':value');

        $converter = $this->createMock(CriteriaConverter::class);

        $handler = new MatchAllHandler();
        $actual = $handler->handle($converter, $query, $criterion);

        $this->assertEquals($expected, $actual);
    }
}
