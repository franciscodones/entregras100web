/**
 * Estructura para una columna
 * @class
 */
Ext.define("Pyansa.database.sqlite.Column", {

    alias: "pyansa.database.sqlite.column",

    requires: [
        "Ext.XTemplate"
    ],

    /**
     * Template relacionado al constraint
     *
     * @type {String|Array}
     */
    statementTpl: [
        "`{name}` ",
        "{[values.type.toUpperCase()]} ",
        "<tpl if='!acceptsNull'>NOT </tpl>",
        "NULL",
        "<tpl if='defaultValue !== null && defaultValue !== undefined'>",
            " DEFAULT ",
            "<tpl switch='typeof values.defaultValue'>",
                "<tpl case='number'>",
                    "{defaultValue}",
                "<tpl default>",
                    "\"{defaultValue}\"",
            "</tpl>",
        "</tpl>"
    ],

    /**
     * Esta propiedad es `true` para identificar instancias que son Column
     *
     * @type {Boolean}
     */
    isColumn: true,

    /**
     * Nombre de la columna
     *
     * @type {String}
     */
    name: null,

    /**
     * Tipo de columna
     *
     * @type {String}
     */
    type: null,

    /**
     * Es llave primaria
     *
     * @type {Boolean}
     */
    isPrimaryKey: false,

    /**
     * Acepta valores NULL
     *
     * @type {Boolean}
     */
    acceptsNull: false,

    /**
     * Valor por default
     *
     * @type {Object}
     */
    defaultValue: null,

    /**
     * Constructor de la clase
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        config = me.initProperties(config);
        this.initConfig(config);
    },

    /**
     * Inicializa las propiedades que esta clase utiliza
     *
     * @param  {Object} config
     * @return {Object}
     */
    initProperties: function(config) {
        var me = this;

        config = config || {};
        config.name = config.name || me.name;
        config.type = config.type || me.type;
        config.isPrimaryKey = config.isPrimaryKey || me.isPrimaryKey;
        config.acceptsNull = config.acceptsNull || me.acceptsNull;
        config.defaultValue = config.defaultValue || me.defaultValue;

        return config;
    },

    /**
     * Genera el string statement con los valores de la columna
     *
     * @param  {String|Array|Ext.XTemplate} [tpl]
     * @return {String}
     */
    buildStatement: function(tpl) {
        var me = this;

        tpl = tpl || me.statementTpl;

        if (!tpl) {
            Ext.raise("No existe un template para generar la sentencia");
        }

        if (Ext.isArray(tpl)) {
            tpl = tpl.join("");
        }

        if (!tpl.isXTemplate) {
            tpl = new Ext.XTemplate(tpl);
        }

        return tpl.apply(me);
    }
});
