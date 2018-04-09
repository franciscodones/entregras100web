/**
 * Sobreescritura de Ext.grid.Panel
 * @override
 */
Ext.define('Pyansa.overrides.grid.Panel', {
    override: 'Ext.grid.Panel',

    requires: [
        "Ext.tip.ToolTip"
    ],

    /**
     * Agrega la propiedad `showMenuTrigger` a las columnas que no la tengan definida
     * @type {Boolean}
     */
    showMenuTriggers: false,

    /**
     * Muestra un `ToolTip` con una pista acerca del menu de las columnas
     * @type {Boolean}
     */
    showMenuHint: false,

    /**
     * Texto utilizado para la pista acerca del menu
     * @type {String}
     */
    menuHintText: "Menu para filtrar y ordenar similar a Excel",

    /**
     * Sobreescribe la funcion `initComponent`
     * @return {[type]} [description]
     */
    initComponent: function() {
        var me = this;

        me.callParent(arguments);

        me.addMenuTriggers();
        me.addMenuHint();
    },

    /**
     * Agrega la propiedad `showMenuTrigger` a las columnas que no la tengan definida
     */
    addMenuTriggers: function() {
        var me = this,
            columns, i;

        if (me.showMenuTriggers) {
            columns = me.query("gridcolumn[menuDisabled=false]:not([?showMenuTrigger])");
            for (i = 0; i < columns.length; i++) {
                columns[i].showMenuTrigger = true;
            }
        }
    },

    /**
     * Agrega el `ToolTip` con la pista del menu a la primera columna que lo tenga disponible y/o visible
     */
    addMenuHint: function() {
        var me = this,
            firstColumn;

        if (me.showMenuHint) {
            me.addListener("afterlayout", function() {
                firstColumn = me.query("gridcolumn[menuDisabled=false][showMenuTrigger]")[0];
                if (firstColumn) {
                    Ext.create("Ext.tip.ToolTip", {
                        html: me.menuHintText,
                        target: firstColumn,
                        anchor: true,
                        autoShow: true,
                        defaultAlign: "tl-br",
                        dismissDelay: 5000,
                        hideAction: "destroy"
                    });
                }
            }, me, {single: true});
        }
    }
});
