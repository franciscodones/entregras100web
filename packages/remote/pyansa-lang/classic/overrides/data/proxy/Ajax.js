/**
 * Sobreescritura de Ext.data.proxy.Ajax
 *
 * @override
 */
Ext.define('Pyansa.overrides.data.proxy.Ajax', {
    override: 'Ext.data.proxy.Ajax',

    requires: [
        "Ext.Object",
        "Ext.window.MessageBox"
    ],

    /**
     * Sobreescritura del constructor
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this,
            acceptHeader;

        config = config || {}

        //  agrega el header para aceptar json
        config.headers = config.headers || {};
        config.headers["Accept"] = "application/json, */*";

        // agrega el listener para el evento `exception`
        config.listeners = config.listeners || {};
        config.listeners.exception = config.listeners.exception || "onAjaxException";

        me.callParent([config]);
    },

    /**
     * En caso de exception se muestra una ventana de error
     *
     * @param  {Ext.data.proxy.Server} proxy
     * @param  {Ext.data.Response} response
     * @param  {Ext.data.operation.Operation} operation
     * @param  {Object} eOpts
     */
    onAjaxException: function(proxy, response, operation, eOpts) {
        var me = this,
            message;

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
