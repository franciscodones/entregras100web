/**
 * Estructura para una base de datos de SQLite
 * @class
 */
Ext.define("Pyansa.database.Schema", {

    alias: "pyansa.database.schema.",

    requires: [
        "Pyansa.database.table.Table"
    ],

    /**
     * Nombre de la tabla
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
     * Tamañp de la base de datos
     * @type {Number}
     */
    size: 1024,

    /**
     * Tipo de columna
     * @type {Ext.util.MixedCollection}
     */
    tables: [],

    constructor: function(config) {
        var me = this;

        Ext.apply(me, config);

        if (me.size <= 0) {
            Ext.raise("El tamaño de la base de datos debe ser mayor a 0");
        }

        if (Ext.isArray(me.tables)) {
            me.tables = me.tables.map(function(table) {
                return new Pyansa.database.table.Table(table);
            });
        }
    }
});
