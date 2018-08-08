/**
 * Sobreescribe los textos de la clase `Ext.field.Date`
 * @override
 */
Ext.define('Pyansa.locale.field.Date', {
    override: 'Ext.field.Date',

    minDateMessage: "La fecha en este campo debe ser igual o posterior a {0}",
    maxDateMessage: "La fecha en este campo debe ser igual o anterior a {0}"
});
