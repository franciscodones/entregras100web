/**
 * Sobreescribe los valores default de Ext.util.Format
 * @override
 */
Ext.define("Pyansa.locale.util.Format", {
    override: "Ext.util.Format",

}, function() {
    if (Ext.util && Ext.util.Format) {
        Ext.apply(Ext.util.Format, {
            thousandSeparator: ',',
            decimalSeparator: '.',
            currencySign: '$',
            dateFormat: 'd/m/Y'
        });
    }
});
