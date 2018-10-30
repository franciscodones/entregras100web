/**
 * Esta clase genera un mapa usando como api https://www.mapbox.com/
 *
 * @class
 */
Ext.define("Pyansa.map.MapboxMap", {
    extend: "Pyansa.map.Map",
    alias: "pyansa.map.mapboxmap",

    requires: [
        "Pyansa.lib.leaflet"
    ],

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
    url: "https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}",

    /**
     * Id para cargar el estilo streets
     *
     * @type {String}
     */
    mapId: "mapbox.streets",

    /**
     * Atribucion obligatorio por parte de la libreria de leaflet.js
     *
     * @type {String}
     */
    attribution: "Map data &copy; <a href=\"http://openstreetmap.org\">OpenStreetMap</a> contributors, " +
                "<a href=\"http://creativecommons.org/licenses/by-sa/2.0/\">CC-BY-SA</a>, " +
                "Imagery © <a href=\"http://mapbox.com\">Mapbox</a>",

    /**
     * Token para acceder a los tiles
     *
     * @type {String}
     */
    accessToken: "",

    /**
     * Construcotr de la clase
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        me.initProperties(config);
        me.initConfig(config);
        me.callParent(arguments);
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
        me.callParent(arguments);
        me.url = config.url || me.config.url || me.url;
        me.mapId = config.mapId || me.config.mapId || me.mapId;
        me.attribution = config.attribution || me.config.attribution || me.attribution;
        me.accessToken = config.accessToken || me.config.accessToken || me.accessToken;
    },

    /**
     * Crea el layer de los tiles (cartografia)
     */
    createTileLayer: function() {
        var me = this;

        L.tileLayer(
            me.url,
            {
                id: me.mapId,
                attribution: me.attribution,
                accessToken: me.accessToken
            }
        ).addTo(me.map);
    }
});
