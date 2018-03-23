/**
 * Clase para manejar la columna como NULL
 * @class
 */
Ext.define("Pyansa.database.column.constraint.NullConstraint", {

    extend: "Pyansa.database.column.constraint.ColumnConstraint",

    alias: "pyansa.database.column.constraint.nullconstraint",

    requires: [
        "Ext.XTemplate"
    ],

    /**
     * Acepta valores NULL
     * @type {Boolean}
     */
    acceptsNull: false,

    /**
     * Obtiene el XTemplate relacionado al constraint
     * @return {Ext.XTemplate}
     */
    getStatementTpl: function() {
        var me = this,
            tpl;

        tpl = [
            "<tpl if='!acceptsNull'>NOT </tpl>",
            "NULL",
        ];

        return new Ext.XTemplate(tpl);
    }
});
