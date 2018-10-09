/**
 * Sobreescritura de Ext.data.Connection
 */
Ext.define('Pyansa.overrides.data.Connection', {
    override: 'Ext.data.Connection',

    config: {
        /**
         * `true` para enviar el timeout del request en el header `X-Request-Header`
         *
         * @type {Boolean}
         */
        sendTimeoutAsHeader: false
    },

    /**
     * Sobreescritura de la funcion `setOptions`
     *
     * @param {Object} options
     * @return {Ext.data.request.Base}
     */
    setOptions: function(options, scope) {
        var me = this,
            sendTimeoutAsHeader = options.sendTimeoutAsHeader || me.getSendTimeoutAsHeader(),
            timeout = options.timeout || me.getTimeout();

        console.log("sendTimeoutAsHeader", sendTimeoutAsHeader);
        if (sendTimeoutAsHeader) {
            options.headers = options.headers || {};
            options.headers["X-Request-Timeout"] = timeout;
            console.log(options);
        }

        return me.callParent(arguments);
    }
});
