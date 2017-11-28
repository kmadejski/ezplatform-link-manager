YUI.add('ezplatformlinkmanager-navigationplugin', function (Y) {
    Y.namespace('EzPlatformLinkManager.Plugin');

    Y.EzPlatformLinkManager.Plugin.NavigationPlugin = Y.Base.create('ezPlatformLinkManagerNavigationPlugin', Y.eZ.Plugin.ViewServiceBase, [], {
        initializer: function () {
            var service = this.get('host');

            service.addNavigationItem({
                Constructor: Y.eZ.NavigationItemView,
                config: {
                    title: "Link manager",
                    identifier: "link-manager",
                    route: {
                        name: 'adminGenericRoute',
                        params: {
                            uri: 'pjax/link-management'
                        }
                    },
                    matchParameter: 'uri'
                }
            }, 'platform');
        },
    }, {
        NS: 'ezPlatformLinkManagerNavigationPlugin'
    });

    Y.eZ.PluginRegistry.registerPlugin(
        Y.EzPlatformLinkManager.Plugin.NavigationPlugin, ['navigationHubViewService']
    );
});
