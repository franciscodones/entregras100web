/**
 * Sobreescritura de Ext.data.Store
 */
Ext.define('Pyansa.overrides.data.reader.Json', {
    override: 'Ext.data.reader.Json',

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
            messageProperty: "message",
            rootProperty: "records",
            metaProperty: "metadata"
        };
        Ext.Object.each(defaults, function(key, value) {
            config[key] = config[key] || me[key] || value;
        });

        me.callParent([config]);
    }
});
