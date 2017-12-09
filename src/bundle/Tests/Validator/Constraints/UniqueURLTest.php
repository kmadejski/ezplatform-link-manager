<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Test\Validator\Constraints;

use EzSystems\EzPlatformLinkManagerBundle\Validator\Constraints\UniqueURL;
use PHPUnit\Framework\TestCase;

class UniqueURLTest extends TestCase
{
    /** @var \EzSystems\EzPlatformLinkManagerBundle\Validator\Constraints\UniqueURL */
    private $constraint;

    protected function setUp()
    {
        $this->constraint = new UniqueURL();
    }

    public function testConstruct()
    {
        $this->assertSame('ez.url.unique', $this->constraint->message);
    }

    public function testValidatedBy()
    {
        $this->assertSame('ezplatform.link_manager.validator.unique_url', $this->constraint->validatedBy());
    }

    public function testGetTargets()
    {
        $this->assertSame(UniqueURL::CLASS_CONSTRAINT, $this->constraint->getTargets());
    }
}
