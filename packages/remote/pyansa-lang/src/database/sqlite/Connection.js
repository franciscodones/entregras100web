Ext.define("Pyansa.database.sqlite.Connection", {
    alias: "pyansa.database.sqlite.connection",

    /**
     * `true` para saber que la instancia es de esta clase
     *
     * @type {Boolean}
     */
    isConnection: true,

    /**
     * Nombre de la base de datos
     *
     * @type {String}
     */
    name: null,

    /**
     * Version de la base de datos
     *
     * @type {String}
     */
    version: "1.0",

    /**
     * Descripcion de la base de datos
     *
     * @type {String}
     */
    description: "WebSQL Database",

    /**
     * Tamaño de la base de datos
     *
     * @type {Number}
     */
    size: 0,

    /**
     * Conexion a la base de datos
     *
     * @type {Object}
     */
    connection: null,

    /**
     * Constructor
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        config = me.initProperties(config);
        me.initConfig(config);

        if (me.size <= 0) {
            Ext.raise("El tamaño de la base de datos debe ser mayor a 0");
        }

        me.connection = me.databaseObject = openDatabase(me.name, me.version, me.description, me.size);
    },

    /**
     * Inicializa las propiedades que esta clase utiliza
     *
     * @param  {Object} config
     * @return {Object}
     */
    initProperties: function(config) {
        var me = this;

        config = config || {};
        config.name = config.name || me.name;
        config.version = config.version || me.version;
        config.description = config.description || me.description;
        config.size = config.size || me.size;
        config.connection = config.connection || me.connection;

        return config;
    },

    /**
     * Redirije la transaccion a la conexion de la base de datos
     */
    transaction: function() {
        var me = this;

        me.connection.transaction.apply(me.connection, arguments);
    }
});
