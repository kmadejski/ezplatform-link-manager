<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;

class URLPolicyProvider implements PolicyProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $configBuilder->addConfig([
            'url' => [
                'view' => null,
                'update' => null,
            ],
        ]);
    }
}
