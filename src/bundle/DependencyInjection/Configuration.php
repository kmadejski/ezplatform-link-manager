<?php

namespace EzSystems\EzPlatformLinkManagerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends SiteAccessConfiguration
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('link_manager');

        $systemNode = $this->generateScopeBaseNode($rootNode);

        $systemNode
            ->booleanNode('enabled')->isRequired()->end()
            ->arrayNode('handler')->prototype('array')->end();

        return $treeBuilder;
    }
}
