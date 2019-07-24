/**
 * Sobreescritura de Ext.data.Store
 *
 * @override
 */
Ext.define('Pyansa.overrides.data.Store', {
    override: 'Ext.data.Store',

    /**
     * Revertir las operaciones en caso que la funcion `sync` presente excepciones
     *
     * @type {Boolean}
     */
    rejectOnExceptions: false,

    /**
     * Sobreescritura de la funcion `onBatchComplete`
     *
     * @param {Ext.data.Batch} batch
     * @param {Ext.data.operation.Operation}
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
     *
     * @return {Boolean}
     */
    isDirty: function() {
        var me = this;

        return me.getNewRecords().length > 0 || me.getUpdatedRecords().length > 0 || me.getRemovedRecords().length > 0;
    },

    /**
     * Clona este store al crear una nueva instancia y copiar sus records, filter y sorters
     *
     * @return {Ext.data.Store}
     */
    clone: function() {
        var me = this,
            newStore, records, filters, sorters, config;

        // clona la config
        config = me.getCurrentConfig();
        delete config.data;
        delete config.filters;
        delete config.sorters;
        delete config.grouper;
        delete config.fields;
        delete config.listeners;
        delete config.proxy;
        delete config.storeId;

        // clona los records
        records = me.getDataSource().getRange().map(function(record) {
            return record.copy();
        });

        // clona los filters
        filters = me.getFilters().clone().getRange();

        // clona los sorters
        sorters = me.getSorters().clone().getRange();

        config.filters = filters;
        config.sorters = sorters;

        // crea la instancia
        newStore = new me.self(config);
        newStore.add(records);

        // clona las propiedades
        newStore.loadCount = me.loadCount;
        newStore.totalCount = me.totalCount;
        newStore.complete = me.complete;
        newStore.currentPage = me.currentPage;

        return newStore;
    },

    /**
     * Obtiene todos los records invalidos del store.
     * Si el store esta filtrado, aun asi toma encuenta todos los records.
     *
     * @return {Ext.data.Model[]}
     */
    getInvalidRecords: function () {
        var me = this;

        return me.filterDataSource(function(item) {
            return !item.isValid();
        });
    }
});
