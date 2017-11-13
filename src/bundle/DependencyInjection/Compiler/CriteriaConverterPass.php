<?php

namespace EzSystems\EzPlatformLinkManagerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass will register URL criterion handlers.
 */
class CriteriaConverterPass implements CompilerPassInterface
{
    const CRITERION_CONVERTER = 'ezpublish.spi.persistence.legacy.url.criterion_converter';
    const CRITERION_HANDLER_TAG = 'ezpublish.legacy.gateway.criterion_handler.url';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::CRITERION_CONVERTER)) {
            return;
        }

        $definition = $container->findDefinition(self::CRITERION_CONVERTER);
        foreach ($container->findTaggedServiceIds(self::CRITERION_HANDLER_TAG) as $id => $attributes) {
            $definition->addMethodCall('addHandler', [
                new Reference($id),
            ]);
        }
    }
}
