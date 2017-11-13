YUI.add('ezplatformlinkmanager-navigationplugin', function (Y) {
    Y.namespace('EzPlatformLinkManager.Plugin');

    Y.EzPlatformLinkManager.Plugin.NavigationPlugin = Y.Base.create('ezPlatformLinkManagerNavigationPlugin', Y.eZ.Plugin.ViewServiceBase, [], {
        initializer: function () {
            var service = this.get('host');

            service.addNavigationItem({
                Constructor: Y.eZ.NavigationItemView,
                config: {
                    title: "Link management",
                    identifier: "link-management",
                    route: {
                        name: 'adminGenericRoute',
                        params: {
                            uri: 'pjax/link-management'
                        }
                    },
                    matchParameter: 'uri'
                }
            }, 'admin');
        },
    }, {
        NS: 'ezPlatformLinkManagerNavigationPlugin'
    });

    Y.eZ.PluginRegistry.registerPlugin(
        Y.EzPlatformLinkManager.Plugin.NavigationPlugin, ['navigationHubViewService']
    );
});
