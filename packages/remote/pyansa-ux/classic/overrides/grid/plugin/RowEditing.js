/**
 * Sobreescritura de Ext.grid.plugin.RowEditing
 *
 * @override
 */
Ext.define('Pyansa.overrides.grid.plugin.RowEditing', {
    override: 'Ext.grid.plugin.RowEditing',

    /**
     * Sincronizar el store despues de editar
     *
     * @type {Boolean}
     */
    syncAfterEdit: false,

    /**
     * Sobreescritura de la funcion `completeEdit`
     */
    completeEdit: function(config) {
        var me = this,
            store = me.context.store;

        me.callParent(arguments);

       if (!store.autoSync && me.syncAfterEdit) {
            store.sync();
       }
    }
});
