/**
 * Estructura para una columna
 * @class
 */
Ext.define("Pyansa.controller.DatabaseController", {

    extend: "Ext.app.Controller",

    alias: "pyansa.controller.databasecontroller",

    requires:[
        "Pyansa.database.Schema"
    ],

    /**
     * Es una columna nulleable
     * @type {Pyansa.database.schema.Database}
     */
    database: null,

    constructor: function() {
        var me = this;

        if (!Ext.isObject(me.database)) {
            Ext.raise("La propiedad \"database\" debe ser un objeto");
        }

        me.database = new Pyansa.database.Schema(me.database);
    }
});
