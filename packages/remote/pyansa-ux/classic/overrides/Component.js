/**
 * Sobreescritura de Ext.Component
 * @override
 */
Ext.define('Pyansa.overrides.Component', {
    override: 'Ext.Component',

    mixins: [
        "Pyansa.mixin.Permissible"
    ]
});
