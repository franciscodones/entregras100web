/**
 * Contenedor para renderizar un Pyansa.map.Map
 *
 * @class
 */
Ext.define("Pyansa.map.OpenStreetMapPanel", {
    extend: "Ext.container.Container",
    alias: "widget.pyansa.map.openstreetmappanel",

    requires: [
        "Pyansa.map.Map",
        "Pyansa.map.MapboxMap"
    ],

    config: {
        /**
         * Es necesario el layout `fit` para que el contenedor no tenga divs internos
         *
         * @type {String}
         */
        layout: "fit"
    },

    /**
     * Mapa a renderizar
     *
     * @type {Pyansa.map.mapbox.Map|Objec}
     */
    map: null,

    /**
     * Token para acceder a los tiles
     *
     * @type {String}
     */
    accessToken: null,

    /**
     * Constructor de la clase
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        me.initProperties(config);
        me.callParent([config]);
    },

    /**
     * Inicializa las propiedades de esta clase para que la instancia tenga sus propios valores
     * y evitar que utilice el que esta en el prototype
     *
     * @param  {Object} config
     */
    initProperties: function(config) {
        var me = this;

        config = config || {};
        me.initProperties = Ext.emptyFn; // ignora cualquier llamada subsequente de initProperties
        me.map = config.map || me.config.map || me.map || null;
        me.accessToken = config.accessToken || me.config.accessToken || me.accessToken || null;
    },

    /**
     * Despues de que las medidas del componente esten listas se carga el mapa y el tile layer.
     * No puede ser afterRender ya que el height de este componente (necesario para cargar correctamente el mapa)
     * es definido en cada afterLayout
     */
    onBoxReady: function() {
        var me = this,
            map;

        me.callParent();
        me.createMap();
    },

    /**
     * Despues de realizar el layout se invalida el tamaño del mapa para que este vuelva a renderizarse correctamente
     */
    afterLayout: function() {
        var me = this;

        if (me.map) {
            me.map.invalidateSize(true);
        }
    },

    /**
     * Crea el mapa
     */
    createMap: function() {
        var me = this;

        me.map = new Pyansa.map.MapboxMap({
            renderTo: me.el.dom,
            accessToken: me.accessToken
        });
    }
});
