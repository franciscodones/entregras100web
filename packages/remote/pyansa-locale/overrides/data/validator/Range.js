/**
 * Sobreescribe los textos de la clase `Ext.data.validator.Range`
 * @override
 */
Ext.define('Pyansa.locale.data.validator.Range', {
    override: 'Ext.data.validator.Range',

    minOnlyMessage: "Debe ser al menos {0}",
    maxOnlyMessage: "No debe ser más que {0}",
    bothMessage: "Debe estar entre {0} y {1}"
});
