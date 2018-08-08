/**
 * Sobreescritura de Ext.data.writer.Json
 */
Ext.define('Pyansa.overrides.data.writer.Json', {
    override: 'Ext.data.writer.Json',

    requires: [
        "Ext.Object"
    ],

    /**
     * Sobreescritura del constructor
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this,
            defaults;

        // Inicializa las variables de tal manera que la prioridad que toman las propiedades son:
        // - config
        // - prototipo (variables declaradas en la clase)
        // - defaults
        config = config || {};
        defaults = {
            writeAllFields: true,
            allowSingle: false,
            encode: true,
            rootProperty: 'records'
        };
        Ext.Object.each(defaults, function(key, value) {
            config[key] = config[key] || me[key] || value;
        });

        me.callParent([config]);
    }
});
