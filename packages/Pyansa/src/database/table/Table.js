/**
 * Estructura para una tabla
 * @class
 */
Ext.define("Pyansa.database.table.Table", {

    alias: "pyansa.database.table.table",

    requires: [
        "Pyansa.database.column.Column",
        "Ext.Array",
        "Ext.XTemplate",
        "Pyansa.overrides.Array"
    ],

    /**
     * Nombre de la tabla
     * @type {String}
     */
    name: null,

    /**
     * Es tabla temporal
     * @type {Boolean}
     */
    isTemporary: false,

    /**
     * Columnas
     * @type {Pyansa.database.column.Column[]}
     */
    columns: [],

    /**
     * Constraints de la tabla
     * @type {Pyansa.database.table.TableConstraint[]}
     */
    constraints: [],

    /**
     * Constructor de la clase
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        Ext.apply(me, config);

        me.columns = Ext.Array.from(me.columns);
        me.columns = me.columns.map(function(column) {
            return new Pyansa.database.column.Column(column);
        });
    },

    /**
     * Obtiene el XTemplate relacionado la creacion de la tabla
     * @return {Ext.XTemplate}
     */
    getCreateStatementTpl: function() {
        var me = this,
            tpl;

        tpl = [
            "CREATE",
            "<tpl if='isTemporary'>",
                " TEMPORARY",
            "</tpl>",
            " TABLE",
            "<tpl if='ifNotExists'>",
                " IF NOT EXISTS",
            "</tpl>",
            " `{name}` (",
                "<tpl for='columns'>",
                    "{[values.buildStatement()]}",
                    "<tpl if='xindex < xcount'>",
                        ", ",
                    "</tpl>",
                "</tpl>",
            ")"
        ];

        return new Ext.XTemplate(tpl);
    },

    /**
     * Obtiene el XTemplate relacionado a la eliminacion de la tabla
     * @return {Ext.XTemplate}
     */
    getDropStatementTpl: function() {
        var me = this,
            tpl;

        tpl = [
            "DROP",
            "<tpl if='isTemporary'>",
                " TEMPORARY",
            "</tpl>",
            " TABLE",
            "<tpl if='ifExists'>",
                " IF EXISTS",
            "</tpl>",
            " `{name}`",
        ];

        return new Ext.XTemplate(tpl);
    },

    /**
     * Genera el string statement con los valores de la tabla
     * @param  {Ext.XTemplate} [tpl]
     * @return {String}
     */
    buildStatement: function(tpl) {
        var me = this;

        tpl = tpl || me.getCreateStatementTpl();

        return tpl.apply(me);
    }
});
