/**
 * Sobreescritura de Ext.grid.filters.filter.Date
 * @override
 */
Ext.define('Pyansa.overrides.grid.filters.filter.Date', {
    override: 'Ext.grid.filters.filter.Date',

    requires: [
        "Ext.Object"
    ],

    /**
     * Sobreescritura de la funcion `constructor`
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        config = config || {};
        config = Ext.Object.chain(config);

        config.fields = Ext.apply({
            lt: {text: 'Antes de'},
            gt: {text: 'Despues de'},
            eq: {text: 'En'}
        }, config.fields);

        me.callParent(arguments);
    }
});
