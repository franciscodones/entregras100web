/**
 * Un dialogo es una ventana con un componente `field` para que el usuario ingrese o seleccione un valor
 * @class
 */
Ext.define("Pyansa.window.dialog.Dialog", {

    alias: "pyansa.window.dialog.dialog",

    extend: "Ext.window.Window",

    /**
     * Texto para el boton aceptar
     * @type {String}
     */
    acceptText: "Aceptar",

    /**
     * Texto para el boton cancelar
     * @type {String}
     */
    cancelText: "Cancelar",

    /**
     * Titulo de la ventana
     * @type {String}
     */
    title: "Ingrese un valor",

    /**
     * Constructor de la clase
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        if (!config.field) {
            Ext.raise("Es necesario especificar la configuracion del campo a utilizar");
        }

        config = Ext.merge({
            autoShow: true,
            width: 300,
            height: 200,
            modal: true,
            closable: false,
            items: [
                {
                    xtype: 'form',
                    bodyPadding: 10,
                    items: [
                        config.field
                    ]
                },
                {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        align: 'stretch',
                        pack: 'center'
                    },
                    items: [
                        {
                            xtype: 'button',
                            minWidth: 100,
                            text: me.cancelText,
                            listeners: {
                                click: {
                                    fn: me.onCancelButtonClick,
                                    scope: me
                                }
                            }
                        },
                        {
                            xtype: 'tbspacer',
                            width: 20
                        },
                        {
                            xtype: 'button',
                            minWidth: 100,
                            text: me.acceptText,
                            listeners: {
                                click: {
                                    fn: me.onAcceptButtonClick,
                                    scope: me
                                }
                            }
                        }
                    ]
                }
            ],
            listeners: {
                cancel: me.onDialogCancel,
                accept: me.onDialogAccept
            }
        }, config);

        me.callParent([config]);
    },

    /**
     * Se ejecuta con el evento `cancel` de este dialogo
     */
    onDialogCancel: function() {
        this.close();
    },

    /**
     * Se ejecuta con el evento `accept` de este dialogo
     */
    onDialogAccept: Ext.emptyFn,

    /**
     * Se ejecuta en el evento `click` del boton cancelar
     */
    onCancelButtonClick: function() {
        this.fireEvent("cancel");
    },

    /**
     * Se ejecuta en el evento `click` del boton aceptar
     * @return {[type]} [description]
     */
    onAcceptButtonClick: function() {
        var me = this,
            form = me.down("form"),
            field = me.down("field");

        // si el formulario es valido se ejecuta el evento `accept` del dialogo
        if (form.isValid()) {
            me.fireEvent("accept", field.getValue(), field);
        }
    }
});
