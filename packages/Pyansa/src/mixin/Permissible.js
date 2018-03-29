/**
 * Este mixin proporciona las funcionalidades para saber si un componente esta permitido
 */
Ext.define('Pyansa.mixin.Permissible', {
    extend: 'Ext.Mixin',

    mixinConfig: {
        after: {
            afterRender: "evaluatePermission"
        }
    },

    /**
     * Accion a efectuar por el componente cuando no esta permitido
     * @type {String}
     */
    notPermitedAction: "hide",

    /**
     * Evalua si el componente esta permitido y actua de acuerdo a `notPermitedAction`
     */
    evaluatePermission: function() {
        var me = this;

        // si el componente tiene definida una funcion propia `isPermited`, esta se evalua
        // sin importar que `permissionId` no este definido
        if (me.hasOwnProperty("isPermited") && !me.isPermited(me.permissionId)) {
            if (me.notPermitedAction == "hide") {
                me.setHidden(true);
            } else {
                me.setDisabled(true);
            }
        }

        // si el componente no tiene denifido `permissionId` no se evalue la funcion `isPermited`
        if (me.permissionId != null && Ext.isDefined(me.permissionId) && !me.isPermited(me.permissionId)) {
            if (me.notPermitedAction == "hide") {
                me.setHidden(true);
            } else {
                me.setDisabled(true);
            }
        }
    },

    /**
     * Retorna `true` si el componente es permitido.
     * Esta funcion debe ser sobreescrita de acuerdo a la forma en que se evalua los permisos
     * @param  {Number}  permissionId
     * @return {Boolean}
     */
    isPermited: function(permissionId) {
        return true;
    }
});
