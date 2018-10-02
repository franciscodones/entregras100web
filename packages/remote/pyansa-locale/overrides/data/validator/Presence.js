/**
 * Sobreescribe los textos de la clase `Ext.data.validator.Presence`
 * @override
 */
Ext.define('Pyansa.locale.data.validator.Presence', {
    override: 'Ext.data.validator.Presence',

    config: {
        message: "Debe estar presente"
    }
});
