/*
 * File: app/view/EditarHorarioZonaWindowViewController.js
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

Ext.define('Entregas100Web.view.EditarHorarioZonaWindowViewController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.editarhorariozonawindow',

    onCmbPlazaSelect: function(combo, record, eOpts) {
        var me = this,
            cmbZona = me.view.down("#cmbZona"),
            hidPlaza = me.view.down("#hidPlaza"),
            zonasLocalStore = me.getStore("ZonasLocalStore");

        if (record) {
            cmbZona.enable();
            hidPlaza.setValue(combo.getDisplayValue());

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

    onCmbZonaSelect: function(combo, record, eOpts) {
        var me = this,
            hidZona = me.view.down("#hidZona");

        hidZona.setValue(combo.getDisplayValue());
    },

    onTimeHoraInicialSelect: function(combo, record, eOpts) {
        var me = this,
            cmbHoraFinal = me.view.down("#timeHoraFinal");

        cmbHoraFinal.enable();
        cmbHoraFinal.setMinValue(
        Ext.Date.add(combo.getValue(), Ext.Date.MINUTE, 30)
        );
    },

    onBtnCancelarClick: function(button, e, eOpts) {
        this.view.close();
    },

    onBtnGuardarClick: function(button, e, eOpts) {
        var me = this,
            editarHorarioZonaForm = me.view.down("form").getForm(),
            horariosZonaPanel = Ext.ComponentManager.get("horariosZonaPanel"),
            horariosZonaLocalStore = horariosZonaPanel.getController().getStore("HorariosZonaLocalStore"),
            record, waitWindow;

        if (editarHorarioZonaForm.isValid()) {
            waitWindow = Ext.MessageBox.wait("Guardando cambios...");
            editarHorarioZonaForm.updateRecord();
            record = editarHorarioZonaForm.getRecord();

            // si el record no ha sufrigo cambios se termina la edicion
            if (!record.isDirty()) {
                waitWindow.close();
                me.view.close();
                return;
            }

            horariosZonaLocalStore.sync({
                success: onSyncSuccess
            });
        }

        function onSyncSuccess() {
            waitWindow.close();
            me.view.close();
        }
    },

    onEditarHorarioZonaWindowAfterRender: function(component, eOpts) {
        var me = this,
            plazasLocalStore = me.getStore("PlazasLocalStore"),
            zonasLocalStore = me.getStore("ZonasLocalStore"),
            plazaId = me.view.down("#cmbPlaza").getValue();

        plazasLocalStore.load();
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
