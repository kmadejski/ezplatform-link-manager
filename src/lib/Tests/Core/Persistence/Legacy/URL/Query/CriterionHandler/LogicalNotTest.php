<?php

namespace EzSystems\EzPlatformLinkManager\Tests\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use eZ\Publish\Core\Persistence\Database\Expression;
use eZ\Publish\Core\Persistence\Database\SelectQuery;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion\LogicalNot;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler\LogicalNot as LogicalNotHandler;

class LogicalNotTest extends CriterionHandlerTest
{
    /**
     * {@inheritdoc}
     */
    public function testAccept()
    {
        $handler = new LogicalNotHandler();

        $this->assertHandlerAcceptsCriterion($handler, LogicalNot::class);
        $this->assertHandlerRejectsCriterion($handler, Criterion::class);
    }

    /**
     * {@inheritdoc}
     */
    public function testHandle()
    {
        $foo = $this->createMock(Criterion::class);
        $fooExpr = 'FOO';
        $expected = 'NOT FOO';

        $expr = $this->createMock(Expression::class);
        $expr
            ->expects($this->once())
            ->method('not')
            ->with($fooExpr)
            ->willReturn($expected);

        $query = $this->createMock(SelectQuery::class);
        $query->expr = $expr;

        $converter = $this->createMock(CriteriaConverter::class);
        $converter
            ->expects($this->at(0))
            ->method('convertCriteria')
            ->with($query, $foo)
            ->willReturn($fooExpr);

        $handler = new LogicalNotHandler();
        $actual = $handler->handle(
            $converter, $query, new LogicalNot($foo)
        );

        $this->assertEquals($expected, $actual);
    }
}
