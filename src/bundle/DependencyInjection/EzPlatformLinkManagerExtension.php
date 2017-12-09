<?php

namespace EzSystems\EzPlatformLinkManagerBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class EzPlatformLinkManagerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('default_settings.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $processor = new ConfigurationProcessor($container, 'ez_platform_link_manager');
        $processor->mapConfig($config, function ($scopeSettings, $currentScope, ContextualizerInterface $contextualizer) use ($container) {
            foreach ($scopeSettings['handlers'] as $name => $options) {
                $contextualizer->setContextualParameter('url_handler.' . $name . '.options', $currentScope, $options);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('assetic', [
            'bundles' => ['EzPlatformLinkManagerBundle'],
        ]);

        $this->prependYUI($container);
        $this->prependCSS($container);
    }

    private function prependYUI(ContainerBuilder $container)
    {
        $container->setParameter(
            'link_management.public_dir', 'bundles/ezplatformlinkmanager'
        );

        $yuiConfigFile = __DIR__ . '/../Resources/config/yui.yml';

        $config = Yaml::parse(file_get_contents($yuiConfigFile));
        $container->prependExtensionConfig('ez_platformui', $config);
        $container->addResource(new FileResource($yuiConfigFile));
    }

    private function prependCSS(ContainerBuilder $container)
    {
        $container->setParameter(
            'ezplatformlinkmanager.css_dir',
            'bundles/ezplatformlinkmanager/css'
        );

        $cssConfigFile = __DIR__ . '/../Resources/config/css.yml';
        $config = Yaml::parse(file_get_contents($cssConfigFile));
        $container->prependExtensionConfig('ez_platformui', $config);
        $container->addResource(new FileResource($cssConfigFile));
    }
}
