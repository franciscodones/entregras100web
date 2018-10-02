/**
 * Sobreescribe los textos de la clase `Ext.data.validator.Length`
 * @override
 */
Ext.define('Pyansa.locale.data.validator.Length', {
    override: 'Ext.data.validator.Length',

    minOnlyMessage: "La longitud debe ser al menos {0}",
    maxOnlyMessage: "La longitud no debe ser mayor que {0}",
    bothMessage: "La longitud debe estar entre {0} y {1}"
});
