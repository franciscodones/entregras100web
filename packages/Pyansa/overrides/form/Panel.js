/**
 * Sobreescritura de Ext.form.Panel
 * @override
 */
Ext.define('Pyansa.overrides.form.Panel', {
    override: 'Ext.form.Panel',

    requires: [
        "Pyansa.overrides.form.field.Text"
    ],

    /**
     * Establece la propiedad `trimValue` en los componentes hijos de tipo `textfield` del formulario
     * que no lo tengan especificado. Si el componente hijo tiene especificada la propiedad `trimValue`
     * el valor de esta no se altera.
     * @type {Boolean}
     */
    trimValues: false,

    /**
     * Sobreescribe el metodo `initComponent`
     */
    initComponent: function() {
        var me = this,
            notTrimmedFields,
            i;

        me.callParent(arguments);

        if (me.trimValues) {
            notTrimmedFields = me.query("[xtype=textfield]:not([?trimValue])");
            for (i = 0; i < notTrimmedFields.length; i++) {
                notTrimmedFields[i].trimValue = true;
            }
        }
    },
});
