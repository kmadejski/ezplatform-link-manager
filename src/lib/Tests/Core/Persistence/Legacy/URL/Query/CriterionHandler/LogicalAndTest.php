<?php

namespace EzSystems\EzPlatformLinkManager\Tests\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use eZ\Publish\Core\Persistence\Database\Expression;
use eZ\Publish\Core\Persistence\Database\SelectQuery;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion\LogicalAnd;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler\LogicalAnd as LogicalAndHandler;

class LogicalAndTest extends CriterionHandlerTest
{
    /**
     * {@inheritdoc}
     */
    public function testAccept()
    {
        $handler = new LogicalAndHandler();

        $this->assertTrue($handler->accept($this->createMock(LogicalAnd::class)));
        $this->assertFalse($handler->accept($this->createMock(Criterion::class)));
    }

    /**
     * {@inheritdoc}
     */
    public function testHandle()
    {
        $foo = $this->createMock(Criterion::class);
        $bar = $this->createMock(Criterion::class);

        $fooExpr = 'FOO';
        $barExpr = 'BAR';

        $expected = 'FOO AND BAR';

        $expr = $this->createMock(Expression::class);
        $expr
            ->expects($this->once())
            ->method('lAnd')
            ->with([$fooExpr, $barExpr])
            ->willReturn($expected);

        $query = $this->createMock(SelectQuery::class);
        $query->expr = $expr;

        $converter = $this->createMock(CriteriaConverter::class);
        $converter
            ->expects($this->at(0))
            ->method('convertCriteria')
            ->with($query, $foo)
            ->willReturn($fooExpr);
        $converter
            ->expects($this->at(1))
            ->method('convertCriteria')
            ->with($query, $bar)
            ->willReturn($barExpr);

        $handler = new LogicalAndHandler();
        $actual = $handler->handle(
            $converter, $query, new LogicalAnd([$foo, $bar])
        );

        $this->assertEquals($expected, $actual);
    }
}
