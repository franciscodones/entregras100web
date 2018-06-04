/*
 * File: app/view/EditarUnidadWindow.js
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

Ext.define('Entregas100Web.view.EditarUnidadWindow', {
    extend: 'Ext.window.Window',
    alias: 'widget.editarunidadwindow',

    requires: [
        'Entregas100Web.view.EditarUnidadWindowViewModel',
        'Entregas100Web.view.EditarUnidadWindowViewController',
        'Ext.form.Panel',
        'Ext.form.FieldContainer',
        'Ext.form.field.Number',
        'Ext.toolbar.Spacer',
        'Ext.form.field.ComboBox',
        'Ext.form.FieldSet',
        'Ext.form.field.Checkbox',
        'Ext.button.Button'
    ],

    controller: 'editarunidadwindow',
    viewModel: {
        type: 'editarunidadwindow'
    },
    modal: true,
    id: 'editarUnidadWindow',
    width: 500,
    glyph: 'f0d1@FontAwesome',
    title: 'Editar Unidad',

    items: [
        {
            xtype: 'form',
            trimValues: true,
            itemId: 'editarUnidadForm',
            bodyPadding: 10,
            items: [
                {
                    xtype: 'fieldcontainer',
                    layout: {
                        type: 'hbox',
                        align: 'stretch'
                    },
                    items: [
                        {
                            xtype: 'numberfield',
                            flex: 1,
                            fieldLabel: 'Unidad',
                            name: 'unidad',
                            allowBlank: false,
                            hideTrigger: true,
                            allowDecimals: false,
                            allowExponential: false,
                            maxValue: 999,
                            minValue: 1
                        },
                        {
                            xtype: 'tbspacer',
                            width: 20
                        },
                        {
                            xtype: 'textfield',
                            transformToUpper: true,
                            flex: 1,
                            fieldLabel: 'Tipo',
                            name: 'letra',
                            allowBlank: false,
                            maskRe: /[A-Za-z]/,
                            maxLength: 1,
                            minLength: 1
                        }
                    ]
                },
                {
                    xtype: 'fieldcontainer',
                    layout: {
                        type: 'hbox',
                        align: 'stretch'
                    },
                    items: [
                        {
                            xtype: 'combobox',
                            flex: 1,
                            itemId: 'cmbPlaza',
                            fieldLabel: 'Plaza',
                            name: 'plaza_id',
                            allowBlank: false,
                            editable: false,
                            matchFieldWidth: false,
                            displayField: 'ciudad',
                            forceSelection: true,
                            valueField: 'id',
                            bind: {
                                store: '{PlazasLocalStore}'
                            },
                            listeners: {
                                select: 'onCmbPlazaSelect'
                            }
                        },
                        {
                            xtype: 'tbspacer',
                            width: 20
                        },
                        {
                            xtype: 'combobox',
                            flex: 1,
                            itemId: 'cmbZona',
                            fieldLabel: 'Zona',
                            name: 'zona_id',
                            allowBlank: false,
                            editable: false,
                            matchFieldWidth: false,
                            displayField: 'zona',
                            forceSelection: true,
                            valueField: 'id',
                            bind: {
                                store: '{ZonasLocalStore}'
                            }
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: 'Folios',
                    items: [
                        {
                            xtype: 'fieldcontainer',
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    flex: 1,
                                    itemId: 'txtSerie',
                                    fieldLabel: 'Serie',
                                    name: 'folios_serie',
                                    allowBlank: false
                                },
                                {
                                    xtype: 'tbspacer',
                                    width: 20
                                },
                                {
                                    xtype: 'numberfield',
                                    flex: 1,
                                    fieldLabel: 'Notas',
                                    name: 'folios_nota',
                                    value: 0,
                                    allowBlank: false,
                                    hideTrigger: true,
                                    allowDecimals: false,
                                    allowExponential: false,
                                    minValue: 0
                                }
                            ]
                        },
                        {
                            xtype: 'fieldcontainer',
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items: [
                                {
                                    xtype: 'numberfield',
                                    flex: 1,
                                    fieldLabel: 'Puntos',
                                    name: 'folios_puntos',
                                    value: 0,
                                    allowBlank: false,
                                    hideTrigger: true,
                                    allowDecimals: false,
                                    allowExponential: false,
                                    minValue: 0
                                },
                                {
                                    xtype: 'tbspacer',
                                    width: 20
                                },
                                {
                                    xtype: 'numberfield',
                                    flex: 1,
                                    fieldLabel: 'Litrogas',
                                    name: 'folios_litrogas',
                                    value: 0,
                                    allowBlank: false,
                                    hideTrigger: true,
                                    allowDecimals: false,
                                    allowExponential: false,
                                    minValue: 0
                                }
                            ]
                        },
                        {
                            xtype: 'fieldcontainer',
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items: [
                                {
                                    xtype: 'numberfield',
                                    flex: 1,
                                    fieldLabel: 'Recirculacion',
                                    name: 'folios_recirculacion',
                                    value: 0,
                                    allowBlank: false,
                                    hideTrigger: true,
                                    allowDecimals: false,
                                    allowExponential: false,
                                    minValue: 0
                                },
                                {
                                    xtype: 'tbspacer',
                                    width: 20
                                },
                                {
                                    xtype: 'numberfield',
                                    flex: 1,
                                    fieldLabel: 'Consignacion',
                                    name: 'folios_consignacion',
                                    value: 0,
                                    allowBlank: false,
                                    hideTrigger: true,
                                    allowDecimals: false,
                                    allowExponential: false,
                                    minValue: 0
                                }
                            ]
                        },
                        {
                            xtype: 'fieldcontainer',
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items: [
                                {
                                    xtype: 'numberfield',
                                    flex: 1,
                                    fieldLabel: 'Donativo',
                                    name: 'folios_donativo',
                                    value: 0,
                                    allowBlank: false,
                                    hideTrigger: true,
                                    allowDecimals: false,
                                    allowExponential: false,
                                    minValue: 0
                                },
                                {
                                    xtype: 'tbspacer',
                                    width: 20
                                },
                                {
                                    xtype: 'numberfield',
                                    flex: 1,
                                    fieldLabel: 'Cortesia',
                                    name: 'folios_cortesia',
                                    value: 0,
                                    allowBlank: false,
                                    hideTrigger: true,
                                    allowDecimals: false,
                                    allowExponential: false,
                                    minValue: 0
                                }
                            ]
                        }
                    ]
                },
                {
                    xtype: 'fieldcontainer',
                    layout: {
                        type: 'hbox',
                        align: 'stretch',
                        pack: 'center'
                    },
                    items: [
                        {
                            xtype: 'checkboxfield',
                            width: 130,
                            hideEmptyLabel: false,
                            labelWidth: 0,
                            name: 'cobro_aditivo',
                            boxLabel: 'Contiene AD+',
                            checked: true,
                            inputValue: 'true',
                            uncheckedValue: 'false'
                        },
                        {
                            xtype: 'checkboxfield',
                            width: 130,
                            hideEmptyLabel: false,
                            labelWidth: 0,
                            name: 'online',
                            boxLabel: 'Modo Online',
                            inputValue: 'true',
                            uncheckedValue: 'false'
                        },
                        {
                            xtype: 'checkboxfield',
                            width: 130,
                            hideEmptyLabel: false,
                            labelWidth: 0,
                            name: 'permitir_ruta_nocturna',
                            boxLabel: 'Ruta Nocturna',
                            inputValue: 'true',
                            uncheckedValue: 'false'
                        }
                    ]
                },
                {
                    xtype: 'fieldcontainer',
                    margin: '20 0 0 0',
                    layout: {
                        type: 'hbox',
                        align: 'stretch',
                        pack: 'center'
                    },
                    items: [
                        {
                            xtype: 'button',
                            itemId: 'btnCancelar',
                            width: 120,
                            glyph: 'f057@FontAwesome',
                            text: 'Cancelar',
                            listeners: {
                                click: 'onBtnCancelarClick'
                            }
                        },
                        {
                            xtype: 'tbspacer',
                            width: 20
                        },
                        {
                            xtype: 'button',
                            itemId: 'btnGuardar',
                            width: 120,
                            glyph: 'f058@FontAwesome',
                            text: 'Guardar',
                            listeners: {
                                click: 'onBtnGuardarClick'
                            }
                        }
                    ]
                }
            ]
        }
    ],
    listeners: {
        beforerender: 'onEditarUnidadWindowBeforeRender'
    }

});