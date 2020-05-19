/**
 * Sobreescritura de Ext.grid.filters.Filters
 *
 * @override
 */
Ext.define('Pyansa.overrides.grid.filters.Filters', {
    override: 'Ext.grid.filters.Filters',

    /**
     * Sobreescritura de la funcion `initColumns`
     *
     * @param  {Object} config
     */
    initColumns: function() {
        var me = this,
            grid = me.grid,
            store = grid.getStore();

        if (store.getRemoteFilter()) {
            // suprime el primer siguiente filtrado remoto para evitar que el store
            // realice un load al crear los filtros
            store.suppressNextFilter = true;
            me.callParent();
            store.suppressNextFilter = false;
        } else {
            me.callParent();
        }
    }
});
