/**
 * Sobreescribe los textos de la clase `Ext.data.validator.Number`
 * @override
 */
Ext.define('Pyansa.locale.data.validator.Number', {
    override: 'Ext.data.validator.Number',

    config: {
        message: "No es un número válido"
    }
});
