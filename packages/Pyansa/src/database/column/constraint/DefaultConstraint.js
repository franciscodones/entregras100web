/**
 * Clase para manejar el valor por default de la columna
 * @class
 */
Ext.define("Pyansa.database.column.constraint.DefaultConstraint", {

    extend: "Pyansa.database.column.constraint.ColumnConstraint",

    alias: "pyansa.database.column.constraint.defaultconstraint",

    requires: [
        "Ext.XTemplate"
    ],

    /**
     * Valor por default
     * @type {Object}
     */
    defaultValue: null,

    /**
     * Obtiene el XTemplate relacionado al constraint
     * @return {Ext.XTemplate}
     */
    getStatementTpl: function() {
        var me = this,
            tpl;

        tpl = [
            "<tpl if='defaultValue'>DEFAULT ",
                "<tpl switch='typeof values.defaultValue'>",
                    "<tpl case='number'>",
                        "{defaultValue}",
                    "<tpl default>",
                        "\"{defaultValue}\"",
                "</tpl>",
            "</tpl>",
        ];

        return new Ext.XTemplate(tpl);
    }
});
