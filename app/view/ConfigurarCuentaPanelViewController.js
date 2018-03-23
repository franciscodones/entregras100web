/*
 * File: app/view/ConfigurarCuentaPanelViewController.js
 *
 * This file was generated by Sencha Architect
 * http://www.sencha.com/products/architect/
 *
 * This file requires use of the Ext JS 6.5.x Classic library, under independent license.
 * License of Sencha Architect does not include license for Ext JS 6.5.x Classic. For more
 * details see http://www.sencha.com/license or contact license@sencha.com.
 *
 * This file will be auto-generated each and everytime you save your project.
 *
 * Do NOT hand edit this file.
 */

Ext.define('Entregas100Web.view.ConfigurarCuentaPanelViewController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.configurarcuentapanel',

    onBtnGuardarClick: function(button, e, eOpts) {
        var me = this,
            cambiarContrasenaForm = me.view.down("#cambiarContrasenaForm");

        if (cambiarContrasenaForm.isValid()) {
            cambiarContrasenaForm.submit({
                headers: {
                    "Accept": "application/json, */*"
                },
                success: function(form, action) {
                    Ext.Msg.show({
                        title: 'Mensaje del Sistema',
                        message: action.result.message,
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg.INFO
                    });
                },
                failure: function(form, action) {
                    Ext.Msg.show({
                        title: 'Mensaje del Sistema',
                        message: (action.result && action.result.message) || "Error al cambiar la contraseña",
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg.ERROR
                    });
                },
                waitMsg: "Guardando contraseña...",
                waitTitle: "Mensaje del Sistema"
            });
        }
    },

    onConfigurarCuentaPanelAfterRender: function(component, eOpts) {
        var me = this,
            cambiarContrasenaForm = me.view.down("#cambiarContrasenaForm");

        cambiarContrasenaForm.getForm().setValues({id: Ext._.usuario.id});
    }

});
