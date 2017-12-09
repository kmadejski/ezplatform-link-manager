<?php

namespace EzSystems\EzPlatformLinkManager\Tests\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use eZ\Publish\Core\Persistence\Database\Expression;
use eZ\Publish\Core\Persistence\Database\SelectQuery;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler\MatchNone as MatchNoneHandler;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion\MatchNone;

class MatchNoneTest extends CriterionHandlerTest
{
    /**
     * {@inheritdoc}
     */
    public function testAccept()
    {
        $handler = new MatchNoneHandler();

        $this->assertHandlerAcceptsCriterion($handler, MatchNone::class);
        $this->assertHandlerRejectsCriterion($handler, Criterion::class);
    }

    /**
     * {@inheritdoc}
     */
    public function testHandle()
    {
        $criterion = new MatchNone();
        $expected = 'NOT :value';

        $expr = $this->createMock(Expression::class);
        $expr
            ->expects($this->once())
            ->method('not')
            ->with(':value')
            ->willReturn($expected);

        $query = $this->createMock(SelectQuery::class);
        $query->expr = $expr;
        $query
            ->expects($this->once())
            ->method('bindValue')
            ->with('1')
            ->willReturn(':value');

        $converter = $this->createMock(CriteriaConverter::class);

        $handler = new MatchNoneHandler();
        $actual = $handler->handle($converter, $query, $criterion);

        $this->assertEquals($expected, $actual);
    }
}
