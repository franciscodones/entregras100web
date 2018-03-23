/**
 * Sobreescritura de Ext.grid.filters.filter.Date
 * @override
 */
Ext.define('Pyansa.overrides.grid.filters.filter.Date', {
    override: 'Ext.grid.filters.filter.Date',

    /**
     * Sobreescritura de la funcion `constructor`
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        config.fields = {
            lt: {text: 'Antes de'},
            gt: {text: 'Despues de'},
            eq: {text: 'En'}
        }

        me.callParent(arguments);
    }
});
