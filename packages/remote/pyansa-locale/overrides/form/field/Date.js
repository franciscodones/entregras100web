/**
 * Sobreescribe los textos de la clase `Ext.form.field.Date`
 * @override
 */
Ext.define('Pyansa.locale.form.field.Date', {
    override: 'Ext.form.field.Date',

    ariaDisabledDatesText: "Esta fecha no puede ser seleccionada",
    ariaDisabledDaysText: "Este dia de la semana esta deshabilitado",
    ariaMaxText: "La fecha debe ser igual o anterior a {0}",
    ariaMinText: "La fecha debe ser igual o posterior a {0}",
    formatText: "El formato de fecha esperado es {0}"
});
