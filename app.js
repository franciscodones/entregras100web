/*
 * File: app.js
 *
 * This file was generated by Sencha Architect version 4.2.2.
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

// @require @packageOverrides
Ext.Loader.setConfig({

});


Ext.application({
    models: [
        'PlazaModel',
        'EmpresaModel',
        'ZonaModel',
        'UnidadModel',
        'OperadorModel',
        'TarifaModel',
        'FormaPagoModel',
        'CombinacionFormaPagoModel',
        'CombinacionFormaPlazaModel',
        'PerfilPagoModel',
        'CombinacionFormaPerfilModel',
        'UsuarioModel',
        'TipoSesionModel',
        'PermisoModel',
        'PermisoUsuarioModel',
        'HorarioZonaModel'
    ],
    stores: [
        'PlazasStore',
        'EmpresasStore',
        'ZonasStore',
        'UnidadesStore',
        'OperadoresStore',
        'TarifasStore',
        'FormasPagoStore',
        'CombinacionesFormaPagoStore',
        'CombinacionesFormaPlazaStore',
        'PerfilesPagoStore',
        'CombinacionesFormaPerfilStore',
        'UsuariosStore',
        'TiposSesionStore',
        'PermisosStore',
        'PermisosUsuarioStore',
        'HorariosZonaStore'
    ],
    views: [
        'MainViewport',
        'LoginWindow',
        'PlazasPanel',
        'CrearPlazaWindow',
        'EditarPlazaWindow',
        'ZonasPanel',
        'CrearZonaWindow',
        'EditarZonaWindow',
        'UnidadesPanel',
        'CrearUnidadWindow',
        'OperadoresPanel',
        'CrearOperadorWindow',
        'EditarOperadorWindow',
        'TarifasPanel',
        'ConfigurarCuentaPanel',
        'FormasPagoPanel',
        'EditarFormaPagoWindow',
        'PerfilesPagoPanel',
        'EditarPerfilPagoWindow',
        'UsuariosPanel',
        'CrearUsuarioWindow',
        'EditarUsuarioWindow',
        'PermisosPanel',
        'EditarUnidadWindow',
        'HorariosZonaPanel',
        'CrearHorarioZonaWindow',
        'EditarHorarioZonaWindow'
    ],
    name: 'Entregas100Web',

    init: function() {
        // quitar cargando con ExtJS
        var cargando = Ext.get('cargando');
        if (cargando) {
            cargando.destroy();
        }

        // Separador de decimales == '.'
        // Separador de miles == ','
        if (Ext.util && Ext.util.Format) {
            Ext.apply(Ext.util.Format, {
                thousandSeparator: ",",
                decimalSeparator: "."
            });
        }

        // Deshabilita los warnings de ARIA
        Ext.ariaWarn = Ext.emptyFn;

        /**
         * Namespace para guardar todas las variables globales
         * Es mejor usar las variables globales de la forma:
         *
         * Ext._.usuario = {};     en vez de     Ext._usuario = {};
         *
         * Esto para tener un mejor control sobre estas variables a lo largo de la programacion
         * ya que se podran acceder a todas las globales guardadas simplemente accediendo de la
         * forma:
         *
         * Ext._
         */
        Ext._ = {};
    },

    launch: function() {
        Ext.create('Entregas100Web.view.LoginWindow', {renderTo: Ext.getBody()});
    }

});
