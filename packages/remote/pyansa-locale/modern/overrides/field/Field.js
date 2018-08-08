/**
 * Sobreescribe los textos de la clase `Ext.field.Field`
 * @override
 */
Ext.define('Pyansa.locale.field.Field', {
    override: 'Ext.field.Field',

    requiredMessage: 'Este campo es obligatorio',
    validationMessage: 'Esta en el formato incorrecto'
});
