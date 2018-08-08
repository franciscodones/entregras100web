/**
 * Sobreescritura de Ext.form.field.Text
 * @override
 */
Ext.define('Pyansa.overrides.form.field.Text', {
    override: 'Ext.form.field.Text',

    /**
     * Convierte el valor del campo a mayusculas
     * @type {Boolean}
     */
    transformToUpper: false,

    /**
     * Realiza un trim al valor del campo
     * @type {Boolean}
     */
    trimValue: false,

    /**
     * Sobreescribe el metodo `initComponent`
     */
    initComponent: function() {
        var me = this;

        me.callParent(arguments);

        me.addListener("change", me.transformValueToUpperCase);
    },

    /**
     * Sobreescribe la funcion `getValue` para hacer trim al valor
     * en caso que la propiedad `trimValue` sea `true`
     * @return {Object}
     */
    getValue: function() {
        var me = this,
            value = me.callParent();

        return me.trimValue && typeof value === "string" ? value.trim() : value;
    },

    /**
     * Sobreescribe la funcion `getSubmitValue` para hacer trim al valor
     * en caso que la propiedad `trimValue` sea `true`
     * @return {String}
     */
    getSubmitValue: function() {
        var me = this,
            value = me.callParent();

        return me.trimValue && typeof value === "string" ? value.trim() : value;
    },

    /**
     * Funcion usada como handler para transformar a mayusculas el valor
     */
    transformValueToUpperCase: function(field, newValue, oldValue, eOpts) {
        var me = field;

        if (me.transformToUpper && typeof newValue === "string") {
            me.suspendEvent("change");
            me.setValue(newValue.toUpperCase());
            me.resumeEvent("change");
        }
    }
});
