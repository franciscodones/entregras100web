/**
 * Base de datos de SQLite
 * @class
 */
Ext.define("Pyansa.database.sqlite.Schema", {

    alias: "pyansa.database.sqlite.schema.",

    requires: [
        "Pyansa.database.sqlite.Connection",
        "Pyansa.database.sqlite.Table",
        "Ext.Deferred"
    ],

    /**
     * Esta propiedad es `true` para identificar instancias que son Schema
     *
     * @type {Boolean}
     */
    isSchema: true,

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
     * @type {Pyansa.database.sqlite.Connection}
     */
    connection: null,

    /**
     * Tablas del schema
     *
     * @type {Ext.util.Collection}
     */
    tables: null,

    /**
     * Constructor
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this,
            tables, defaults;

        config = me.initProperties(config);
        me.initConfig(config);

        me.connection = new Pyansa.database.sqlite.Connection({
            name: me.name,
            version: me.version,
            description: me.description,
            size: me.size
        });
        tables = me.tables;

        me.tables = new Ext.util.Collection({
            keyFn: function(item) {
                return item.name;
            },
            decoder: me.createTable.bind(me)
        });
        me.setTables(tables);
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
        config.tables = config.tables || me.tables;

        return config;
    },

    /**
     * Obtiene las tablas del schema
     *
     * @return {Ext.util.Collection}
     */
    getTables: function() {
        return this.tables;
    },

    /**
     * Asigna las tablas del schema
     *
     * @param {Pyansa.database.sqlite.Table[]|Object[]|String[]} tables
     */
    setTables: function(tables) {
        var me = this,
            tableCollection = me.tables,
            i, table;

        tables = tables || [];
        tableCollection.clear();

        for (i = 0; i < tables.length; i++) {
            table = tables[i];
            tableCollection.add(tables[i]);
        }
    },

    /**
     * Construye una tabla
     *
     * @return {Pyansa.database.sqlite.Table|Object|String} table
     */
    createTable: function(table) {
        var me = this;

        if (typeof table === "string") {
            table = {type: table};
        }
        if (!table.isTable) {
            table = Ext.create(table.type || "Pyansa.database.sqlite.Table", table);
            table.schema = me;
        }

        return table;
    },

    /**
     * Ejecuta una query
     *
     * @param  {String} query  [description]
     * @param  {Array} params [description]
     * @return {Ext.promise.Promise}
     */
    query: function(query, params) {
        var me = this,
            deferred = new Ext.Deferred();

        if (!me.connection) {
            Ext.raise("No existe una conexion a la base de datos");
        }

        me.connection.transaction(function(tx) {
            tx.executeSql(query, params);
        }, function(sqlError) {
            deferred.reject(sqlError);
        }, function() {
            deferred.resolve();
        });

        return deferred.promise;
    },

    /**
     * Ejecuta una transaccion
     */
    transaction: function() {
        var me = this;

        me.connection.transaction.apply(me.connection, arguments);
    },

    /**
     * Crea el schema
     *
     * @return {Ext.promise.Promise}
     */
    create: function() {
        var me = this,
            deferred = new Ext.Deferred();

        me.connection.transaction(function(tx) {
            me.tables.each(function(item) {
                tx.executeSql(item.buildStatement(item.createStatementTpl));
            });
        }, function(sqlError) {
            deferred.reject(sqlError);
        }, function() {
            deferred.resolve();
        });

        return deferred.promise;
    },

    /**
     * Crea el schema
     *
     * @return {Ext.promise.Promise}
     */
    drop: function() {
        var me = this,
            deferred = new Ext.Deferred();

        me.transaction(function(tx) {
            me.tables.each(function(item) {
                tx.executeSql(item.buildStatement(item.dropStatementTpl));
            });
        }, function(sqlError) {
            deferred.reject(sqlError);
        }, function() {
            deferred.resolve();
        });

        return deferred.promise;
    }
});
