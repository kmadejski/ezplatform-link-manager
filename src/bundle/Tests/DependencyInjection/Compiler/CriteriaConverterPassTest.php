<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Tests\DependencyInjection\Compiler;

use EzSystems\EzPlatformLinkManagerBundle\DependencyInjection\Compiler\CriteriaConverterPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CriteriaConverterPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->setDefinition('ezpublish.spi.persistence.legacy.url.criterion_converter', new Definition());
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CriteriaConverterPass());
    }

    public function testRegisterCriteriaConverter()
    {
        $criteriaConverter = 'criteria_converter';
        $serviceId = 'service_id';
        $definition = new Definition();
        $definition->addTag('ezpublish.legacy.gateway.criterion_handler.url');
        $this->setDefinition($serviceId, $definition);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ezpublish.spi.persistence.legacy.url.criterion_converter',
            'addHandler',
            array(new Reference($serviceId))
        );
    }
}
