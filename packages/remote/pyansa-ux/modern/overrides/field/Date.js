/**
 * Sobreescritura de Ext.field.Date
 *
 * @override
 */
Ext.define('Pyansa.overrides.field.Date', {
    override: 'Ext.field.Date',

    /**
     * `true` para usar un input[type="date"] como input nativo intermediario
     *
     * @type {Boolean}
     */
    useNativeInput: true,

    /**
     * Se sobreescribe la funcion `initialize`
     */
    initialize: function() {
        var me = this,
            nativeInput, inputElem;

        // si es escritorio o no usa un input nativo se termina la funcion
        if (Ext.os.deviceType == "Desktop" || !me.useNativeInput) {
            me.callParent(arguments);
            return;
        }

        // crea un input nativo y lo oculta para que el funcionamiento sea transparente
        nativeInput = document.createElement("input");
        nativeInput.type = "date";
        nativeInput.style.visibility = "hidden";
        nativeInput.style.width = 0;
        nativeInput.style.height = 0;
        nativeInput.addEventListener("change", function(event) {
            // se invoca la funcion de esta forma para poder continuar con el scope del Ext.field.Date
            me.onNativeInputChange.call(me, Ext.Date.parse(this.value, "Y-m-d"));
        });
        inputElem = me.inputElement.dom;
        inputElem.insertAdjacentElement("afterend", nativeInput);
        me.nativeInput = nativeInput;
        me.callParent(arguments);
    },

    /**
     * Sobreescribe la funcion `showPicker` para evitar que se muestre el picker de ExtJS.
     * En caso que no se utilice un input nativo, esta funcion trabajara normalmente.
     */
    showPicker: function() {
        var me = this;

        // si es escritorio o no usa un input nativo se termina la funcion
        if (Ext.os.deviceType == "Desktop" || !me.useNativeInput) {
            me.callParent(arguments);
            return;
        }

        me.nativeInput.click();
    },

    /**
     * Se ejecuta cuando el input nativo cambia su valor
     *
     * @param  {Date} newValue
     */
    onNativeInputChange: function(newValue) {
        var me = this;

        me.setValue(newValue);
    },

    /**
     * Sobreescribe la funcion `applyValue` para cambiar el valor del input nativo
     *
     * @param  {Object|Date} value
     * @param  {Date} oldValue
     * @return {Date}
     */
    applyValue: function(value, oldValue) {
        var me = this;

        value = me.callParent(arguments);

        // si es escritorio o no usa un input nativo se termina la funcion
        if (Ext.os.deviceType == "Desktop" || !me.useNativeInput) {
            return value;
        }

        if (!value) {
            me.nativeInput.value = null;
        } else {
            me.nativeInput.value = Ext.Date.format(value, "Y-m-d");
        }

        return value;
    }
});
