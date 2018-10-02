/**
 * Sobreescritura de Ext.data.proxy.Ajax
 * @override
 */
Ext.define('Pyansa.overrides.data.proxy.Ajax', {
    override: 'Ext.data.proxy.Ajax',

    /**
     * Sobreescritura del constructor
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        // Inicializa las variables de tal manera que la prioridad que toman las propiedades son:
        // - config
        // - prototipo (variables declaradas en la clase)
        // - defaults
        config = config || {};
        defaults = {
            headers: {
                "Accept": "application/json, */*"
            }
        };
        Ext.Object.each(defaults, function(key, value) {
            config[key] = config[key] || me[key] || value;
        });

        me.callParent([config]);
    }
});
