/**
 * Sobreescribe los textos de la clase `Ext.data.validator.Bound`
 * @override
 */
Ext.define('Pyansa.locale.data.validator.Bound', {
    override: 'Ext.data.validator.Bound',

    config: {
        emptyMessage: "Debe estar presente",
        minOnlyMessage: "El valor debe ser mayor que {0}",
        maxOnlyMessage: "El valor debe ser menor que {0}",
        bothMessage: "El valor debe estar entre {0} y {1}"
    }
});
