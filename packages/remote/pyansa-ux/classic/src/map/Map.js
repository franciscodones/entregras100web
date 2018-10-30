/**
 * Clase base para generar mapas
 *
 * @class
 */
Ext.define("Pyansa.map.Map", {
    alias: "pyansa.map.map",

    requires: [
        "Pyansa.lib.leaflet"
    ],

    config: {
        /**
         * Centro de mexico
         *
         * @type {Array}
         */
        center: [23.0999442125314, -102.96386718750001],

        /**
         * Zoom al que se aprecia mexico por completo
         *
         * @type {Number}
         */
        zoom: 6
    },

    /**
     * `true` para identificar que es una instancia de Pyansa.map.mapbox.Map
     * @type {Boolean}
     */
    isMap: true,

    /**
     * Mapa de leaflet.js
     *
     * @type {L.Map}
     */
    map: null,

    /**
     * Elemeto donde se renderizara el mapa
     *
     * @type {HTMLElement|String}
     */
    renderTo: null,

    /**
     * URL template de donde se tomaran la cartografia del mapa. Posee la siguiente forma:
     *
     * 'http://{s}.somedomain.com/blabla/{z}/{x}/{y}{r}.png'
     *
     * {s} significa uno de los subdominios (usado secuencialmente para ayudar al navegador con la limitacion de
     * de peticiones paralelas por dominio), {z} - nivel de zoom, {x} y {y} - las coordenadas del tile. {r} puede
     * ser usado para agregar "@2x" a la URL para cargar azulejos compatibles con retina.
     *
     * @type {String}
     */
    url: "",

    /**
     * Atribucion obligatorio por parte de la libreria de leaflet.js
     *
     * @type {String}
     */
    attribution: "Map data &copy; <a href=\"http://openstreetmap.org\">OpenStreetMap</a> contributors, " +
                "<a href=\"http://creativecommons.org/licenses/by-sa/2.0/\">CC-BY-SA</a>",

    /**
     * Construcotr de la clase
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        me.initProperties(config);
        me.initConfig(config);
        me.createMap();
        me.createTileLayer();
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
        me.renderTo = config.renderTo || me.config.renderTo || me.renderTo || null;
        me.url = config.url || me.config.url || me.url;
        me.attribution = config.attribution || me.config.attribution || me.attribution;
    },

    /**
     * Invalida el tamaño del mapa forzando a que este se reajuste al tamaño del contenedor
     *
     * @param  {Boolean} animate
     */
    invalidateSize: function(animate) {
        var me = this;

        me.map.invalidateSize(animate);
    },

    /**
     * Crea el mapa de leaflet.js
     */
    createMap: function() {
        var me = this;

        me.map = L.map(
            me.renderTo,
            {
                center: me.getCenter(),
                zoom: me.getZoom()
            }
        );
    },

    /**
     * Crea el layer de los tiles (cartografia)
     */
    createTileLayer: function() {
        var me = this;

        Ext.raise("Por el momento es necesario sobreescribir esta funcion en una subclase");
    }
});
