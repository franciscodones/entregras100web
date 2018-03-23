/**
 * Sobreescritura de Ext.data.Store
 * @override
 */
Ext.define('Pyansa.overrides.data.Store', {
    override: 'Ext.data.Store',

    /**
     * Revertir las operaciones en caso que la funcion `sync` presente excepciones
     * @type {Boolean}
     */
    rollbackOnExceptions: false,

    /**
     * Sobreescribe el constructor de la clase
     * @param  {Object} cfg
     */
    constructor: function(cfg) {
        var me = this;

        cfg = cfg || {};

        // si tiene un proxy configurado y es de tipo `ajax`, se asegura que tenga un listener para el evento `exception`
        if (cfg.proxy && cfg.proxy.type == "ajax") {
            cfg.proxy = Ext.apply({
                headers: {
                    "Accept": "application/json, */*"
                },
                listeners: {
                    exception: {
                        fn: me.onAjaxException,
                        scope: me
                    }
                }
            }, cfg.proxy);
        }

        me.callParent([cfg]);
    },

    /**
     * Sobreescritura de la funcion `onBatchComplete`
     */
    onBatchComplete: function(batch, operation) {
        var me = this,
            proxy = me.getProxy(),
            batchExceptions = batch.getExceptions(),
            operationException;

        if (me.rollbackOnExceptions) {
            if (batchExceptions.length > 0) {
                for (i = 0; i < batchExceptions.length; i++) {
                    operationException = batchExceptions[i];
                    if (operationException.isCreateOperation) {
                        me.rollbackCreateOperation(operationException);
                    } else if (operationException.isUpdateOperation) {
                        me.rollbackUpdateOperation(operationException);
                    }
                }
            }
        }

        me.callParent(arguments);
    },

    /**
     * Revierte los cambios de una operacion `create`
     * @param  {Ext.data.operation.Create} operation
     */
    rollbackCreateOperation: function(operation) {
        var records = operation.getRecords(),
            i;

        for (i = 0; i < records.length; i++) {
            records[i].drop();
        }
    },

    /**
     * Revierte los cambios de una operacion `create`
     * @param  {Ext.data.operation.Create} operation
     */
    rollbackUpdateOperation: function(operation) {
        var records = operation.getRecords(),
            i;

        for (i = 0; i < records.length; i++) {
            records[i].reject();
        }
    },

    /**
     * En caso de exception se muestra una ventana de error
     * @param  {Ext.data.proxy.Server} proxy
     * @param  {Ext.data.Response} response
     * @param  {Ext.data.operation.Operation} operation
     * @param  {Object} eOpts
     */
    onAjaxException: function(proxy, response, operation, eOpts) {
        var me = this,
            message;

        window.args = arguments;

        // mensaje de error
        if (typeof operation.error === "string") {
            message = operation.error;
        } else {
            message = operation.error.status + " " + operation.error.statusText + ". " + operation.error.response.responseText;
        }

        // muestra el mensaje de error
        Ext.Msg.show({
            title: 'Mensaje del Sistema',
            message: message,
            icon: Ext.Msg.ERROR,
            buttons: Ext.Msg.OK
        });
    }
});
