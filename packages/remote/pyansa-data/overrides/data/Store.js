/**
 * Sobreescritura de Ext.data.Store
 * @override
 */
Ext.define('Pyansa.overrides.data.Store', {
    override: 'Ext.data.Store',

    requires: [
        "Pyansa.overrides.data.proxy.Ajax"
    ],

    /**
     * Revertir las operaciones en caso que la funcion `sync` presente excepciones
     * @type {Boolean}
     */
    rejectOnExceptions: false,

    /**
     * Sobreescritura de la funcion `onBatchComplete`
     */
    onBatchComplete: function(batch, operation) {
        var me = this,
            proxy = me.getProxy(),
            batchExceptions = batch.getExceptions(),
            operationException;

        if (me.rejectOnExceptions) {
            if (batchExceptions.length > 0) {
                me.rejectChanges();
            }
        }

        me.callParent(arguments);
    },

    /**
     * Retorna `true` en caso que el store tenga records agregados, modificados o removidos
     * @return {Boolean}
     */
    isDirty: function() {
        var me = this;

        return me.getNewRecords().length > 0 || me.getUpdatedRecords().length > 0 || me.getRemovedRecords().length > 0;
    }
});
