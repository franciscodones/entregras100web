/**
 * Sobreescritura de Ext.data.Model
 */
Ext.define('Pyansa.overrides.data.Model', {
    override: 'Ext.data.Model',

    requires: [
        "Ext.Object"
    ],

    clientIdProperty: 'clientId',

    /**
     * Obtiene el mensaje del primer error encontrado en el record.
     * En caso que el record sea valido esta funcion retornara null.
     *
     * @return {String}
     */
    getFirstError: function() {
        var me = this,
            validation = me.getValidation(),
            errors = validation.getData(),
            field, value, msg;

        if (me.isValid()) {
            return null;
        }

        for (field in errors) {
            value = errors[field];
            if (value != true) {
                return "\"" + field + "\" " + value.toLowerCase();
            }
        }
    }
});
