Ext.define("Pyansa.state.StatefulStorage", {
    alias: "pyansa.state.statefulstorage",

    mixins: [
        "Ext.state.Stateful",
        "Ext.mixin.Observable"
    ],

    config: {
        /**
         * `true` para guardar el state de esta clase
         * NOTA: Se agregan tanto al config como a la clase debido a que al iniciar el mixin
         * la propiedad `stateful` se debe poder acceder de las siguientes maneras:
         * - this.stateful
         * - this.getStateful()
         * @type {Boolean}
         */
        stateful: true
    },

    /**
     * Este objeto guarda la informacion que contiene la instancia
     *
     * @type {Object}
     */
    data: null,

    /**
     * `true` para guardar el state de esta clase
     * NOTA: Se agregan tanto al config como a la clase debido a que al iniciar el mixin
     * la propiedad `stateful` se debe poder acceder de las siguientes maneras:
     * - this.stateful
     * - this.getStateful()
     *
     * @type {Boolean}
     */
    stateful: true,

    /**
     * Constructor
     *
     * @param  {String} id
     */
    constructor: function(config) {
        var me = this;

        me.data = {};
        me.initConfig(config);
        me.mixins.observable.constructor.call(me);
        me.mixins.state.constructor.call(me);
    },

    /**
     * Obtiene el state de la instancia
     *
     * @return {Object}
     */
    getState: function() {
        var me = this,
            state = {},
            prop;

        for (prop in me.data) {
            if (me.data.hasOwnProperty(prop)) {
                state[prop] = me.data[prop];
            }
        }

        return state
    },

    /**
     * Aplica el estado guardado
     *
     * @param  {Object} state
     */
    applyState: function(state) {
        var me = this;

        if (state) {
            Ext.apply(me.data, state);
        }
    },

    /**
     * Asigna el valor de `key`
     *
     * @param {String} key
     * @param {Object} value
     */
    set: function(key, value) {
        var me = this;

        me.data[key] = value;
        me.saveState();
    },

    /**
     * Obtiene el valor de `key`
     *
     * @param  {String} key
     * @return {Object}
     */
    get: function(key) {
        var me = this;

        return me.data[key];
    },

    /**
     * Elimina el valor de `key`
     *
     * @param  {String} key
     */
    delete: function(key) {
        var me = this;

        delete me.data[key];
        me.saveState();
    },

    /**
     * Evita que Ext.state.Stateful marque error al tratar de obtener los plugins de la clase.
     * Debido a que Ext.state.Stateful esta diseñado para funcionar con la clase Ext.Component
     * el mixin pide los plugins.
     * Esta funcion sirve como hack para evitar que Ext.state.Stateful marque error.
     *
     * @return {null}
     */
    getPlugins: function() {
        return null;
    }
});
