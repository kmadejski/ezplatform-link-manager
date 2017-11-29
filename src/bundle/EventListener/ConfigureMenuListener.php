<?php

namespace EzSystems\EzPlatformLinkManagerBundle\EventListener;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Registers "Link manager" item under "Admin" menu.
 */
class ConfigureMenuListener implements EventSubscriberInterface
{
    const ITEM_ADMIN__LINK_MANAGER = 'main__content__link_manager';

    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $parent = $menu->getChild(MainMenuBuilder::ITEM_CONTENT);
        $parent->addChild(self::ITEM_ADMIN__LINK_MANAGER, [
            'route' => 'admin_link_manager_list',
            'extras' => [
                'translation_domain' => 'menu',
            ],
        ]);

        return $menu;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => 'onMenuConfigure',
        ];
    }
}
