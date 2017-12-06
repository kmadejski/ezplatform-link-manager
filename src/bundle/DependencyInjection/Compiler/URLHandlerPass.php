<?php

namespace EzSystems\EzPlatformLinkManagerBundle\DependencyInjection\Compiler;

use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass will register URL checkers.
 */
class URLHandlerPass implements CompilerPassInterface
{
    const URL_HANDLER_REGISTRY = 'ezpublish.url_checker.handler_registry';
    const URL_HANDLER_TAG = 'url_checker';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::URL_HANDLER_REGISTRY)) {
            return;
        }

        $definition = $container->findDefinition(self::URL_HANDLER_REGISTRY);
        foreach ($container->findTaggedServiceIds(self::URL_HANDLER_TAG) as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['scheme'])) {
                    throw new LogicException(sprintf(
                        '%s service tag needs a "scheme" attribute to identify which scheme is supported by handler. None given.',
                        self::URL_HANDLER_TAG
                    ));
                }

                $definition->addMethodCall('addHandler', [
                    $attribute['scheme'],
                    new Reference($id),
                ]);
            }
        }
    }
}
