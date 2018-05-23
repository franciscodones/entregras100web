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

        if (
            me.hasOwnProperty("isPermited") || // si tiene definida una funcion `isPermited`
            (me.permissionId != null && me.permissionId !== undefined) && // o tiene definida la propiedad `permissionId`
            !me.isPermited(me.permissionId) // se evalue la funcion `isPermited`
        ) {
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
