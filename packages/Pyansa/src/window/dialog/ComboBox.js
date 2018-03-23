/**
 * Un dialogo es una ventana con un componente `field` para que el usuario ingrese o seleccione un valor
 * @class
 */
Ext.define("Pyansa.window.dialog.ComboBox", {

    alias: "pyansa.window.dialog.combobox",

    extend: "Pyansa.window.dialog.Dialog",

    /**
     * Titulo de la ventana
     * @type {String}
     */
    title: "Seleccione un valor",

    /**
     * Constructor de la clase
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        config = Ext.merge({
            field: {
                xtype: "combobox",
                anchor: '100%',
                fieldLabel: 'Label',
                labelAlign: 'top',
                labelStyle: 'text-align: center;',
                allowBlank: false,
                editable: false,
                forceSelection: true
            }
        }, config);

        me.callParent([config]);
    }
});
