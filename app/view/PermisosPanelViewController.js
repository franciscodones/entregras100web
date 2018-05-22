/*
 * File: app/view/PermisosPanelViewController.js
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

Ext.define('Entregas100Web.view.PermisosPanelViewController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.permisospanel',

    armarPermisosTreeList: function(perteneceId, tipo) {
        var me = this,
            permisosLocalStore = me.getStore("PermisosLocalStore"),
            permisosTreeStore = new Ext.data.TreeStore({
                parentIdProperty: "padre_id"
            }),
            permisosList = me.view.down("#permisosList"),
            permisosArray = [];

        // por cada permiso crea un nodo
        permisosLocalStore.each(function(record) {
            var nodo = {};

            nodo.permiso = record.get("permiso");
            nodo.pertenece_id = perteneceId;
            nodo.permiso_id = record.get("id");
            nodo.id = record.get("id");
            nodo.tipo = tipo;
            nodo.es_permitido = false;
            nodo.checked = false;
            // si el nodo no es padre de nadie entonces es una hoja
            if (!permisosLocalStore.findRecord("padre_id", nodo.permiso_id, null, null, null, true)) {
                nodo.leaf = true;
            } else {
                nodo.expanded = true;
            }
            // si no tiene padre entonces no se agrega la propiedad
            if (record.get("padre_id")) {
                nodo.padre_id = record.get("padre_id");
            }
            nodo.glyph = "f023@PyansaFontAwesomeSolid";

            permisosArray.push(nodo);
        });
        permisosTreeStore.getProxy().setData(permisosArray);
        permisosList.setStore(permisosTreeStore);
        permisosTreeStore.commitChanges();
    },

    onBtnRefrescarClick: function(button, e, eOpts) {
        var me = this,
            permisosLocalStore = me.getStore("PermisosLocalStore"),
            usuariosLocalStore = me.getStore("UsuariosLocalStore"),
            pivotePermisosLocalStore = me.getStore("PivotePermisosLocalStore"),
            tiposSesionLocalStore = me.getStore("TiposSesionLocalStore"),
            permisosList = me.view.down("#permisosList"),
            permisosListStore = permisosList.getStore(),
            promesaPermisosLoad = new Ext.Deferred(),
            promesaUsuariosLoad = new Ext.Deferred(),
            promesaTiposSesionLoad = new Ext.Deferred();

        if (permisosListStore && permisosListStore.isDirty()) {
            Ext.Msg.show({
                title: "Mensaje del sistema",
                message: "Se perderan los cambios que ha realizado, ¿Desea continuar?",
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.WARNING,
                fn: function(result) {
                    if (result == "yes") {
                        refrescar();
                    }
                }
            });
        } else {
            refrescar();
        }

        function refrescar() {
            me.view.mask("Cargando...");

            // revierte los cambios
            permisosList.setStore(null);

            // carga los stores necesarios antes de habilitar el panel
            permisosLocalStore.load(function(records, operation, success) {
                if (success) {
                    promesaPermisosLoad.resolve();
                } else {
                    promesaPermisosLoad.reject();
                }
            });
            usuariosLocalStore.load(function(records, operation, success) {
                if (success) {
                    promesaUsuariosLoad.resolve();
                } else {
                    promesaUsuariosLoad.reject();
                }
            });
            tiposSesionLocalStore.load(function(records, operation, success) {
                if (success) {
                    promesaTiposSesionLoad.resolve();
                } else {
                    promesaTiposSesionLoad.reject();
                }
            });

            // al terminar de cargar los stores habilita el panel
            Ext.Deferred.all([
            promesaPermisosLoad,
            promesaUsuariosLoad,
            promesaTiposSesionLoad
            ]).then(onStoresLoad, onStoresLoad);

            function onStoresLoad() {
                me.view.unmask();
            }
        }
    },

    onTiposSesionGridSelect: function(rowmodel, record, index, eOpts) {
        var me = this,
            permisosLocalStore = me.getStore("PermisosLocalStore"),
            pivotePermisosLocalStore = me.getStore("PivotePermisosLocalStore"),
            permisosList = me.view.down("#permisosList"),
            permisosListStore;

        permisosList.mask("Cargando...");

        // obtiene todos los permisos
        pivotePermisosLocalStore.getProxy().setExtraParams({
            pertenece_id:  record.get("id"),
            tipo: "TIPO_USUARIOS"
        });
        pivotePermisosLocalStore.load(onPivoteLoaded);

        function onPivoteLoaded(records, operation, success) {
            if (success) {
                me.armarPermisosTreeList(record.get("id"), "TIPO_USUARIOS");
                permisosListStore = permisosList.getStore();
                // marca los permisos otorgados
                Ext.Array.each(records, function(item) {
                    var treeRecord = permisosListStore.findRecord(
                    "id",
                    item.get("permiso_id"),
                    null,
                    null,
                    null,
                    true
                    );

                    treeRecord.set("es_permitido", true);
                    treeRecord.set("checked", true);

                    console.log(item.get("permiso_id"), treeRecord);
                });
                permisosListStore.commitChanges();
                permisosList.unmask();
            }
        }
    },

    onTiposSesionGridBeforeSelect: function(rowmodel, record, index, eOpts) {
        var me = this,
            permisosList = me.view.down("#permisosList"),
            permisosListStore = permisosList.getStore();

        if (permisosListStore.isDirty()) {
            Ext.Msg.show({
                title: "Mensaje del sistema",
                message: "Se perderan los cambios que ha realizado, ¿Desea continuar?",
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.WARNING,
                fn: function(result) {
                    if (result == "yes") {
                        permisosListStore.rejectChanges();
                        rowmodel.select(record);
                    }
                }
            });
            return false;
        }
    },

    onUsuariosGridSelect: function(rowmodel, record, index, eOpts) {
        var me = this,
            permisosLocalStore = me.getStore("PermisosLocalStore"),
            pivotePermisosLocalStore = me.getStore("PivotePermisosLocalStore"),
            permisosList = me.view.down("#permisosList"),
            permisosListStore;

        permisosList.mask("Cargando...");

        // obtiene todos los permisos
        pivotePermisosLocalStore.getProxy().setExtraParams({
            pertenece_id:  record.get("id"),
            tipo: "USUARIOS"
        });
        pivotePermisosLocalStore.load(onPivoteUsuariosLoaded);

        function onPivoteUsuariosLoaded(records, operation, success) {
            if (success) {
                if (records.length > 0) {
                    me.armarPermisosTreeList(record.get("id"), "USUARIOS");
                    permisosListStore = permisosList.getStore();
                    // marca los permisos otorgados
                    Ext.Array.each(records, function(item) {
                        var treeRecord = permisosListStore.findRecord(
                        "permiso_id",
                        item.get("permiso_id"),
                        null,
                        null,
                        null,
                        true
                        );

                        treeRecord.set("es_permitido", true);
                        treeRecord.set("checked", true);
                    });
                    permisosListStore.commitChanges();
                    permisosList.unmask();
                } else {
                    // en caso que el usuario no tenga permisos personalizados se tomaran los del tipo de usuario
                    pivotePermisosLocalStore.getProxy().setExtraParams({
                        pertenece_id:  record.get("tipo_sesion_id"),
                        tipo: "TIPO_USUARIOS"
                    });
                    pivotePermisosLocalStore.load(onPivoteTipoUsuariosLoaded);
                }
            }
        }

        function onPivoteTipoUsuariosLoaded(records, operation, success) {
            if (success) {
                me.armarPermisosTreeList(record.get("id"), "USUARIOS");
                permisosListStore = permisosList.getStore();
                // marca los permisos otorgados
                Ext.Array.each(records, function(item) {
                    var treeRecord = permisosListStore.findRecord(
                    "permiso_id",
                    item.get("permiso_id"),
                    null,
                    null,
                    null,
                    true
                    );

                    treeRecord.set("es_permitido", true);
                    treeRecord.set("checked", true);
                });

                // agrega los records al pivote como nuevos
                copiaRecords = records.map(function(item) {
                    var copia = {};

                    copia.pertenece_id = record.get("id");
                    copia.permiso_id = item.get("permiso_id");
                    copia.tipo = "USUARIOS";

                    return copia;
                });
                pivotePermisosLocalStore.removeAll();
                pivotePermisosLocalStore.commitChanges();
                pivotePermisosLocalStore.add(copiaRecords);
                permisosList.unmask();
            }
        }
    },

    onUsuariosGridBeforeSelect: function(rowmodel, record, index, eOpts) {
        var me = this,
            permisosList = me.view.down("#permisosList"),
            permisosListStore = permisosList.getStore();

        if (permisosListStore.isDirty()) {
            Ext.Msg.show({
                title: "Mensaje del sistema",
                message: "Se perderan los cambios que ha realizado, ¿Desea continuar?",
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.WARNING,
                fn: function(result) {
                    if (result == "yes") {
                        permisosListStore.rejectChanges();
                        rowmodel.select(record);
                    }
                }
            });
            return false;
        }
    },

    onBtnGuardarClick: function(button, e, eOpts) {
        var me = this,
            pivotePermisosLocalStore = me.getStore("PivotePermisosLocalStore"),
            permisosList = me.view.down("#permisosList"),
            permisosListStore = permisosList.getStore(),
            waitWindow, esPersonalizado, index;

        if (!permisosListStore.isDirty()) {
            return;
        }

        waitWindow = Ext.Msg.wait("Guardando cambios...");

        permisosListStore.each(function(item) {
            var pivoteRecord = pivotePermisosLocalStore.findRecord(
            "permiso_id",
            item.get("permiso_id"),
            null,
            null,
            null,
            true
            );

            if (item.get("es_permitido") && !pivoteRecord) {
                pivotePermisosLocalStore.add({
                    pertenece_id: item.get("pertenece_id"),
                    permiso_id: item.get("permiso_id"),
                    tipo: item.get("tipo")
                });
            } else if (!item.get("es_permitido") && pivoteRecord) {
                pivotePermisosLocalStore.remove(pivoteRecord);
            }
        });

        pivotePermisosLocalStore.sync({
            success: onSyncSuccess
        });

        function onSyncSuccess() {
            pivotePermisosLocalStore.commitChanges();
            permisosListStore.commitChanges();
            waitWindow.close();
        }
    },

    onBtnRevertirClick: function(button, e, eOpts) {
        var me = this,
            permisosList = me.view.down("#permisosList"),
            permisosListStore = permisosList.getStore();

        if (permisosListStore && permisosListStore.isDirty()) {
            Ext.Msg.show({
                title: "Mensaje del sistema",
                message: "Se perderan los cambios que ha realizado, ¿Desea continuar?",
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.WARNING,
                fn: function(result) {
                    if (result == "yes") {
                        // revierte los cambios
                        permisosListStore.rejectChanges();
                        permisosListStore.each(function(item) {
                            item.set("checked", item.get("es_permitido"));
                        });
                    }
                }
            });
        }
    },

    onPermisosListCheckChange: function(node, checked, e, eOpts) {
        var me = this,
            parentNode = node.parentNode,
            childNodes = node.childNodes;
        permisosList = me.view.down("#permisosList");

        node.set("es_permitido", checked);
        if (checked && parentNode && !parentNode.get("checked")) {
            parentNode.set("checked", true);
            parentNode.set("es_permitido", true);
            permisosList.fireEvent("checkchange", parentNode, true);
        } else if (!checked && childNodes) {
            Ext.Array.each(childNodes, function(item) {
                if (item.get("checked")) {
                    item.set("checked", false);
                    item.set("es_permitido", false);
                    permisosList.fireEvent("checkchange", item, false);
                }
            });
        }
    },

    onPermisosPanelAfterRender: function(component, eOpts) {
        var me = this,
            permisosLocalStore = me.getStore("PermisosLocalStore"),
            usuariosLocalStore = me.getStore("UsuariosLocalStore"),
            pivotePermisosLocalStore = me.getStore("PivotePermisosLocalStore"),
            tiposSesionLocalStore = me.getStore("TiposSesionLocalStore"),
            promesaPermisosLoad = new Ext.Deferred(),
            promesaUsuariosLoad = new Ext.Deferred(),
            promesaTiposSesionLoad = new Ext.Deferred();

        me.view.mask("Cargando...");

        // carga los stores necesarios antes de habilitar el panel
        permisosLocalStore.load(function(records, operation, success) {
            if (success) {
                promesaPermisosLoad.resolve();
            } else {
                promesaPermisosLoad.reject();
            }
        });
        usuariosLocalStore.load(function(records, operation, success) {
            if (success) {
                promesaUsuariosLoad.resolve();
            } else {
                promesaUsuariosLoad.reject();
            }
        });
        tiposSesionLocalStore.load(function(records, operation, success) {
            if (success) {
                promesaTiposSesionLoad.resolve();
            } else {
                promesaTiposSesionLoad.reject();
            }
        });

        // al terminar de cargar los stores habilita el panel
        Ext.Deferred.all([
        promesaPermisosLoad,
        promesaUsuariosLoad,
        promesaTiposSesionLoad
        ]).then(onStoresLoad, onStoresLoad);

        function onStoresLoad() {
            me.view.unmask();
        }
    }

});
