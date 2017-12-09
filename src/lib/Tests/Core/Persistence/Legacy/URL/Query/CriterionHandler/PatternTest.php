<?php

namespace EzSystems\EzPlatformLinkManager\Tests\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use eZ\Publish\Core\Persistence\Database\Expression;
use eZ\Publish\Core\Persistence\Database\SelectQuery;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion\Pattern;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler\Pattern as PatternHandler;

class PatternTest extends CriterionHandlerTest
{
    /**
     * {@inheritdoc}
     */
    public function testAccept()
    {
        $handler = new PatternHandler();

        $this->assertHandlerAcceptsCriterion($handler, Pattern::class);
        $this->assertHandlerRejectsCriterion($handler, Criterion::class);
    }

    /**
     * {@inheritdoc}
     */
    public function testHandle()
    {
        $criterion = new Pattern('google.com');
        $expected = 'url LIKE :pattern';

        $expr = $this->createMock(Expression::class);
        $expr
            ->expects($this->once())
            ->method('like')
            ->with('url', ':pattern')
            ->willReturn($expected);

        $query = $this->createMock(SelectQuery::class);
        $query->expr = $expr;
        $query
            ->expects($this->once())
            ->method('bindValue')
            ->with('%' . $criterion->pattern . '%')
            ->willReturn(':pattern');

        $converter = $this->createMock(CriteriaConverter::class);

        $handler = new PatternHandler();
        $actual = $handler->handle($converter, $query, $criterion);

        $this->assertEquals($expected, $actual);
    }
}
