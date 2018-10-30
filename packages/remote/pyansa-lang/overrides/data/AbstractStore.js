/**
 * Sobreescritura de Ext.data.AbstractStore
 *
 * @override
 */
Ext.define('Pyansa.overrides.data.AbstractStore', {
    override: 'Ext.data.AbstractStore',

    requires: [
        "Ext.data.identifier.Sequential"
    ],

    /**
     * Sobreescribe el constructor
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this,
            identifier = me.self.identifier;

        storeId = me.getStoreId();
        if (!storeId && (config && config.storeId)) {
            me.setStoreId(storeId = config.storeId);
        }
        if (!storeId && (config && config.id)) {
            me.setStoreId(storeId = config.id);
        }

        // crea el identificador de la clase si no existe
        if (!identifier && storeId) {
            identifier = me.initIdentifier(storeId);
        }

        // si el ya existe un store con el mismo id, se reemplaza el id
        if (Ext.data.StoreManager.get(storeId)) {
            me.setStoreId(storeId = identifier.generate());
            // se modifica la configuracion para que al llamar el constructor padre no reasigne el id original
            if (config && config.storeId) {
                config.storeId = storeId;
            }
            if (config && config.id) {
                config.id = storeId;
            }
        }

        me.callParent([config]);
    },

    /**
     * Inicializa el identificador para evitar stores con id duplicados
     *
     * @return {Ext.data.identifier.Sequential}
     */
    initIdentifier: function(storeId) {
        var identifier = new Ext.data.identifier.Sequential({
            prefix: storeId + "-"
        });

        this.self.identifier = identifier;

        return identifier;
    }
});
