/**
 * Clase base para los CONSTRAINT de una columna
 * @class
 */
Ext.define("Pyansa.database.table.constraint.TableConstraint", {

    alias: "pyansa.database.table.constraint.tableconstraint",

    /**
     * Nombre del CONSTRAINT
     * @type {String}
     */
    name: null,

    /**
     * Constructor de la clase
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        Ext.apply(me, config);
    },

    /**
     * Obtiene el XTemplate relacionado al constraint
     * @return {Ext.XTemplate}
     */
    getStatementTpl: function() {
        Ext.raise("La funcion getStatementTemplate no ha sido implementada en la subclase de " + this.$className);
    },

    /**
     * Genera el string statement con los valores del constraint
     * @param  {Ext.XTemplate} [tpl]
     * @return {String}
     */
    buildStatement: function(tpl) {
        var me = this;

        tpl = tpl || me.getStatementTpl();

        return tpl.apply(me);
    }
});
