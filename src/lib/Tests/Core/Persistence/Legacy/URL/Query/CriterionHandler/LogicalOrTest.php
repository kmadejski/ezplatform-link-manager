<?php

namespace EzSystems\EzPlatformLinkManager\Tests\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use eZ\Publish\Core\Persistence\Database\Expression;
use eZ\Publish\Core\Persistence\Database\SelectQuery;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion\LogicalOr;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler\LogicalOr as LogicalOrHandler;

class LogicalOrTest extends CriterionHandlerTest
{
    /**
     * {@inheritdoc}
     */
    public function testAccept()
    {
        $handler = new LogicalOrHandler();

        $this->assertHandlerAcceptsCriterion($handler, LogicalOr::class);
        $this->assertHandlerRejectsCriterion($handler, Criterion::class);
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

        $expected = 'FOO OR BAR';

        $expr = $this->createMock(Expression::class);
        $expr
            ->expects($this->once())
            ->method('lOr')
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

        $handler = new LogicalOrHandler();
        $actual = $handler->handle(
            $converter, $query, new LogicalOr([$foo, $bar])
        );

        $this->assertEquals($expected, $actual);
    }
}
