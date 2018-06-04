/*
 * File: app/view/EditarUnidadWindowViewController.js
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

Ext.define('Entregas100Web.view.EditarUnidadWindowViewController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.editarunidadwindow',

    onCmbPlazaSelect: function(combo, record, eOpts) {
        var me = this,
            cmbZona = me.view.down("#cmbZona"),
            txtSerie = me.view.down("#txtSerie"),
            zonasLocalStore = me.getStore("ZonasLocalStore");

        if (record) {
            cmbZona.enable();
            txtSerie.setValue(record.get("plaza"));

            // refresca el filtro de la plaza en el store de zonas
            zonasLocalStore.removeFilter("plazaFilter");
            zonasLocalStore.addFilter([{
                id: "plazaFilter",
                property: "plaza_id",
                value: record.get("id"),
                exactMatch: true
            }]);
        }
    },

    onBtnCancelarClick: function(button, e, eOpts) {
        this.view.close();
    },

    onBtnGuardarClick: function(button, e, eOpts) {
        var me = this,
            editarUnidadForm = me.view.down("form").getForm(),
            zona = me.view.down("#cmbZona").getDisplayValue(),
            unidadesPanel = Ext.ComponentManager.get("unidadesPanel"),
            unidadesLocalStore = unidadesPanel.getController().getStore("UnidadesLocalStore"),
            record, waitWindow;

        if (editarUnidadForm.isValid()) {
            waitWindow = Ext.MessageBox.wait("Guardando cambios...");
            editarUnidadForm.updateRecord();
            record = editarUnidadForm.getRecord();

            // si el record no ha sufrigo cambios se termina la edicion
            if (!record.isDirty()) {
                waitWindow.close();
                me.view.close();
                return;
            }

            record.set("zona", zona);
            unidadesLocalStore.sync({
                success: onSyncSuccess
            });
        }

        function onSyncSuccess() {
            waitWindow.close();
            me.view.close();
        }
    },

    onEditarUnidadWindowBeforeRender: function(component, eOpts) {
        var me = this,
            zonasLocalStore = me.getStore("ZonasLocalStore"),
            plazaId = me.view.down("#cmbPlaza").getValue();

        me.getStore("PlazasLocalStore").load();
        zonasLocalStore.load();

        // refresca el filtro de la plaza en el store de zonas
        zonasLocalStore.removeFilter("plazaFilter");
        zonasLocalStore.addFilter([{
            id: "plazaFilter",
            property: "plaza_id",
            value: plazaId,
            exactMatch: true
        }]);
    }

});
