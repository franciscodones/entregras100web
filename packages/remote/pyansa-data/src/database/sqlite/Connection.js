Ext.define("Pyansa.database.sqlite.Connection", {
    alias: "pyansa.database.sqlite.connection",

    isConnection: true,

    /**
     * Nombre de la base de datos
     * @type {String}
     */
    name: null,

    /**
     * Version de la base de datos
     * @type {String}
     */
    version: "1.0",

    /**
     * Descripcion de la base de datos
     * @type {String}
     */
    description: "WebSQL Database",

    /**
     * Tamaño de la base de datos
     * @type {Number}
     */
    size: 0,

    /**
     * Conexion a la base de datos
     * @type {Object}
     */
    connection: null,

    /**
     * Constructor
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this,
            tables, defaults;

        // Inicializa las variables de tal manera que la prioridad que toman las propiedades son:
        // - config
        // - prototipo (variables declaradas en la clase)
        // - defaults
        config = config || {};
        defaults = {
            name: me.name,
            version: me.version,
            description: me.description,
            size: me.size,
            connection: me.connection
        };
        Ext.Object.each(defaults, function(key, value) {
            config[key] = config[key] || me[key] || value;
        });
        this.initConfig(config);

        if (me.size <= 0) {
            Ext.raise("El tamaño de la base de datos debe ser mayor a 0");
        }

        me.connection = me.databaseObject = openDatabase(me.name, me.version, me.description, me.size);
    },

    /**
     * Redirije la transaccion a la conexion de la base de datos
     * @return {[type]} [description]
     */
    transaction: function() {
        var me = this;

        me.connection.transaction.apply(me.connection, arguments);
    }
});
