/**
 * Sobreescritura de `Ext.form.field.VTypes`
 *
 * @override
 */
Ext.define('Pyansa.overrides.form.field.VTypes', {
    override: 'Ext.form.field.VTypes',

    /**
     * Funcion validadora
     *
     * @param {Boolean} value
     */
    IPAddress: function(value) {
        return this.IPAddressRe.test(value);
    },

    /**
     * RegExp validadora
     *
     * @type {RegExp}
     */
    IPAddressRe: /^((([1-9]|1[0-9]|2[0-4])?[0-9]|25[0-5])\.){3}(([1-9]|1[0-9]|2[0-4])?[0-9]|25[0-5])$/,

    /**
     * Texto de error
     *
     * @type {String}
     */
    IPAddressText: 'Este campo debe ser una direccion IP válida',

    /**
     * Filtro para las teclas presionadas
     *
     * @type {RegExp}
     */
    IPAddressMask: /[\d\.]/i
});
