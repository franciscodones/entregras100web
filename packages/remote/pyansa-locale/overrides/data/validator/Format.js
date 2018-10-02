/**
 * Sobreescribe los textos de la clase `Ext.data.validator.Format`
 * @override
 */
Ext.define('Pyansa.locale.data.validator.Format', {
    override: 'Ext.data.validator.Format',

    config: {
        message: "Está en el formato incorrecto"
    }
});
