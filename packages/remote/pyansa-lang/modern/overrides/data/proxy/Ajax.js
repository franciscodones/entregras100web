/**
 * Sobreescritura de Ext.data.proxy.Ajax
 *
 * @override
 */
Ext.define('Pyansa.overrides.data.proxy.Ajax', {
    override: 'Ext.data.proxy.Ajax',

    /**
     * Sobreescritura del constructor
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        config = me.initProperties(config);
        me.callParent([config]);
    },

    /**
     * Inicializa las propiedades que esta clase utiliza
     *
     * @param  {Object} config
     * @return {Object}
     */
    initProperties: function(config) {
        var me;

        config = config || {};
        config = me.prepareConfig(config);

        return config;
    },

    /**
     * Prepara la configuracion del proxy
     *
     * @param  {Object} config
     * @return {Object}
     */
    prepareConfig: function(config) {
        var headers = config.headers,
            reader = config.reader,
            writer = config.writer;

        // configuracion de headers
        if (reader && reader.type == "json") {
            config.headers = me.prepareAcceptHeader(headers);
            config.reader = me.prepareJsonReaderConfig(reader);
        }

        // configuracion del writer
        if (writer && writer.type == "json") {
            config.headers = me.prepareAcceptHeader(headers);
            config.reader = me.prepareJsonWriterConfig(reader);
        }

        return config;
    },

    /**
     * Esta funcion altera el header "Accept" del proxy para que acepte json
     *
     * @param  {Object} config
     * @return {Object}
     */
    prepareAcceptHeader: function(headers) {
        var acceptHeader;

        headers = headers || {};

        if (headers["Accept"]) {
            // si ya existe el header se le agrega la opcion de json en primer lugar
            // en caso que no la tenga
            acceptHeader = headers["Accept"];
            if (acceptHeader.indexOf("application/json") == -1) {
                acceptHeader = "application/json, " + acceptHeader;
            }
        } else {
            // si no existe el header se asigna
            headers["Accept"] = "application/json, */*";
        }

        return headers;
    },

    /**
     * Prepara la configuracion del reader tipo "json" con propiedades default
     *
     * @param  {Object} reader
     * @return {Object}
     */
    prepareJsonReaderConfig: function(reader) {
        reader.messageProperty = reader.messageProperty || "message";
        reader.rootProperty = reader.rootProperty || "records";

        return reader;
    },

    /**
     * Prepara la configuracion del writer tipo "json" con propiedades default
     *
     * @param  {Object} reader
     * @return {Object}
     */
    prepareJsonWriterConfig: function(writer) {
        writer.rootProperty = writer.rootProperty || "records";

        return reader;
    }
});
