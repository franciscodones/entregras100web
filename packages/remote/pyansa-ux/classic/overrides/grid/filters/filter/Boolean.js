/**
 * Sobreescritura de Ext.grid.filters.filter.Boolean
 *
 * @override
 */
Ext.define('Pyansa.overrides.grid.filters.filter.Boolean', {
    override: 'Ext.grid.filters.filter.Boolean',

    requires: [
        "Ext.grid.column.Boolean"
    ],

    /**
     * Sobreescritura de la funcion `constructor`
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        me.callParent(arguments);

        // Si la columna es del tipo `Boolean`, cambia los textos de si y no
        // por los de la columna
        if (me.column instanceof Ext.grid.column.Boolean) {
            me.yesText = me.column.trueText;
            me.noText = me.column.falseText;
        }
    }
});
