<?php

namespace EzSystems\EzPlatformLinkManagerBundle;

use EzSystems\EzPlatformLinkManagerBundle\DependencyInjection\Compiler\CriteriaConverterPass;
use EzSystems\EzPlatformLinkManagerBundle\DependencyInjection\Compiler\URLHandlerPass;
use EzSystems\EzPlatformLinkManagerBundle\Security\URLPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzPlatformLinkManagerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CriteriaConverterPass());
        $container->addCompilerPass(new URLHandlerPass());

        // Retrieve "ezpublish" container extension.
        $eZExtension = $container->getExtension('ezpublish');
        // Add the policy provider.
        $eZExtension->addPolicyProvider(new URLPolicyProvider());
    }
}
