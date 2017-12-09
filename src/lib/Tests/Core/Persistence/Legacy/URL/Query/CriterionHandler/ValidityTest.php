<?php

namespace EzSystems\EzPlatformLinkManager\Tests\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use eZ\Publish\Core\Persistence\Database\Expression;
use eZ\Publish\Core\Persistence\Database\SelectQuery;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriterionHandler\Validity as ValidityHandler;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion\Validity;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;

class ValidityTest extends CriterionHandlerTest
{
    /**
     * {@inheritdoc}
     */
    public function testAccept()
    {
        $handler = new ValidityHandler();

        $this->assertHandlerAcceptsCriterion($handler, Validity::class);
        $this->assertHandlerRejectsCriterion($handler, Criterion::class);
    }

    /**
     * {@inheritdoc}
     */
    public function testHandle()
    {
        $criterion = new Validity(true);
        $expected = 'is_valid = :is_valid';

        $expr = $this->createMock(Expression::class);
        $expr
            ->expects($this->once())
            ->method('eq')
            ->with('is_valid', ':is_valid')
            ->willReturn($expected);

        $query = $this->createMock(SelectQuery::class);
        $query->expr = $expr;
        $query
            ->expects($this->once())
            ->method('bindValue')
            ->with($criterion->isValid)
            ->willReturn(':is_valid');

        $converter = $this->createMock(CriteriaConverter::class);

        $handler = new ValidityHandler();
        $actual = $handler->handle($converter, $query, $criterion);

        $this->assertEquals($expected, $actual);
    }
}
