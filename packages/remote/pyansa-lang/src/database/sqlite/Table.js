/**
 * Estructura para una tabla
 * @class
 */
Ext.define("Pyansa.database.sqlite.Table", {

    alias: "pyansa.database.sqlite.table",

    requires: [
        "Pyansa.database.sqlite.Column",
        "Ext.XTemplate",
        "Ext.util.Collection",
        "Ext.Deferred"
    ],

    /**
     * Template para generar la sentencia create de la tabla
     *
     * @type {String|Array}
     */
    createStatementTpl: [
        "CREATE",
        "<tpl if='isTemporary'>",
            " TEMPORARY",
        "</tpl>",
        " TABLE",
        "<tpl if='checkExistence'>",
            " IF NOT EXISTS",
        "</tpl>",
        " `{name}` (",
            "{[",
                "values.columns.items.map(function(item) { ",
                    "return item.buildStatement();",
                "}).join(\", \")",
            "]}",
            "<tpl if='columns.findIndex(\"isPrimaryKey\", true) != -1'>",
                ", PRIMARY KEY (",
                    "{[",
                        "values.columns.items.filter(function(item) {",
                            "return item.isPrimaryKey;",
                        "}).map(function(item) { ",
                            "return \"`\" + item.name + \"`\";",
                        "}).join(\", \")",
                    "]}",
                ")",
            "</tpl>",
        ")"
    ],

    /**
     * Template para generar la sentencia drop de la tabla
     *
     * @type {String|Array}
     */
    dropStatementTpl: [
        "DROP",
        "<tpl if='isTemporary'>",
            " TEMPORARY",
        "</tpl>",
        " TABLE",
        "<tpl if='checkExistence'>",
            " IF EXISTS",
        "</tpl>",
        " `{name}`",
    ],

    /**
     * Esta propiedad es `true` para identificar instancias que son Table
     *
     * @type {Boolean}
     */
    isTable: true,

    /**
     * Nombre de la tabla
     *
     * @type {String}
     */
    name: null,

    /**
     * Es tabla temporal
     *
     * @type {Boolean}
     */
    isTemporary: false,

    /**
     * Si no existe
     *
     * @type {Boolean}
     */
    checkExistence: false,

    /**
     * Columnas
     *
     * @type {Ext.util.Collection}
     */
    columns: null,

    /**
     * Constructor de la clase
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this,
            columns;

        config = me.initProperties(config);
        me.initConfig(config);

        columns = me.columns;
        me.columns = new Ext.util.Collection({
            keyFn: function(item) {
                return item.name;
            },
            decoder: me.createColumn
        });
        me.setColumns(columns);
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
        config.isTemporary = config.isTemporary || me.isTemporary;
        config.checkExistence = config.checkExistence || me.checkExistence;
        config.columns = config.columns || me.columns;

        return config;
    },

    /**
     * Obtiene las columnas de la tabla
     *
     * @return {Ext.util.Collection}
     */
    getColumns: function() {
        return this.columns;
    },

    /**
     * Asigna las columnas de la tabla
     *
     * @param {Pyansa.database.sqlite.Column[]|Object[]} columns
     */
    setColumns: function(columns) {
        var me = this,
            columnsCollection = me.columns,
            i;

        columns = columns || [];
        columnsCollection.clear();

        for (i = 0; i < columns.length; i++) {
            columnsCollection.add(columns[i]);
        }
    },

    /**
     * Construye una columna
     *
     * @return {Pyansa.database.sqlite.Column|Object} column
     */
    createColumn: function(column) {
        if (!column.isColumn) {
            column = Ext.create("pyansa.database.sqlite.column", column);
        }

        return column;
    },

    /**
     * Genera el string statement con los valores de la tabla
     *
     * @param  {String|Array|Ext.XTemplate} tpl
     * @return {String}
     */
    buildStatement: function(tpl) {
        var me = this;

        if (!tpl) {
            Ext.raise("No existe un template para generar la sentencia");
        }

        if (Ext.isArray(tpl)) {
            tpl = tpl.join("");
        }

        if (!tpl.isXTemplate) {
            tpl = new Ext.XTemplate(tpl);
        }

        return tpl.apply(me);
    },

    /**
     * Crea la tabla en la base de datos
     *
     * @return {Ext.promise.Promise}
     */
    create: function() {
        var me = this;

        return me.schema.query(me.buildStatement(me.createStatementTpl));
    },

    /**
     * Elimina la tabla de la base de datos
     *
     * @return {Ext.promise.Promise}
     */
    drop: function() {
        var me = this;

        return me.schema.query(me.buildStatement(me.dropStatementTpl));
    },

    /**
     * Trunca la tabla de la base de datos.
     * En SQLite no existe una setencia para truncar tablas. Por lo tanto, se ejecuta un drop y un create
     * para simular este comportamiento.
     *
     * @return {Ext.promise.Promise}
     */
    truncate: function() {
        var me = this,
            deferred = new Ext.Deferred();

        me.schema.transaction(function(tx) {
            tx.executeSql(me.buildStatement(me.dropStatementTpl));
            tx.executeSql(me.buildStatement(me.createStatementTpl));
        }, function(sqlError) {
            deferred.reject(sqlError);
        }, function() {
            deferred.resolve();
        });

        return deferred.promise;
    }
});
