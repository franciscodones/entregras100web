/**
 * Clase para manejar la columna como PRIMARY KEY
 * @class
 */
Ext.define("Pyansa.database.table.constraint.PrimaryKeyConstraint", {

    extend: "Pyansa.database.table.constraint.TableConstraint",

    alias: "pyansa.database.table.constraint.primarykeyconstraint",

    requires: [
        "Pyansa.database.column.Column",
        "Ext.XTemplate"
    ],

    /**
     * Columna que es llave primaria
     * @type {Pyansa.database.column.Column}
     */
    column: null,

    /**
     * Obtiene el XTemplate relacionado al constraint
     * @return {Ext.XTemplate}
     */
    getStatementTpl: function() {
        var me = this,
            tpl;

        tpl = [
            "<tpl if='name'>",
                "CONSTRAINT `{name}` ",
            "</tpl>",
            "PRIMARY KEY (`{column.name}`)"
        ];

        return new Ext.XTemplate(tpl);
    }
});
