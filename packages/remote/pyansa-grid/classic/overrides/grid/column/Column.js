/**
 * Sobreescritura de Ext.grid.column.Column
 * @override
 */
Ext.define('Pyansa.overrides.grid.column.Column', {
    override: 'Ext.grid.column.Column',

    /**
     * Siempre mostrar el trigger del menu de la columna
     * @type {Boolean}
     */
    showMenuTrigger: false,

    verticalHeader: false,

    /**
     * Sobreescribe el template
     * @type {Array}
     */
    renderTpl: [
        '<div id="{id}-titleEl" data-ref="titleEl" role="presentation"',
            '{tipMarkup}class="', Ext.baseCSSPrefix, 'column-header-inner<tpl if="!$comp.isContainer"> ', Ext.baseCSSPrefix, 'leaf-column-header</tpl>',
            // Clase para mostrar el menu trigger permanentemente
            '<tpl if="$comp.showMenuTrigger"> ', Ext.baseCSSPrefix, 'column-header-show-trigger</tpl>',
            // Clase para cambiar la orientacion del header a vertical
            '<tpl if="$comp.verticalHeader"> ', Ext.baseCSSPrefix, 'column-vertical-header</tpl>',
            '<tpl if="empty"> ', Ext.baseCSSPrefix, 'column-header-inner-empty</tpl>">',
            //
            // TODO:
            // When IE8 retires, revisit https://jsbin.com/honawo/quiet for better way to center header text
            //
            '<div id="{id}-textContainerEl" data-ref="textContainerEl" role="presentation" class="', Ext.baseCSSPrefix, 'column-header-text-container">',
                '<div role="presentation" class="', Ext.baseCSSPrefix, 'column-header-text-wrapper">',
                    '<div id="{id}-textEl" data-ref="textEl" role="presentation" class="', Ext.baseCSSPrefix, 'column-header-text',
                        '{childElCls}">',
                        '<span id="{id}-textInnerEl" data-ref="textInnerEl" role="presentation" class="', Ext.baseCSSPrefix, 'column-header-text-inner">{text}</span>',
                    '</div>',
                    '{%',
                        'values.$comp.afterText(out, values);',
                    '%}',
                '</div>',
            '</div>',
            '<tpl if="!menuDisabled">',
                '<div id="{id}-triggerEl" data-ref="triggerEl" role="presentation" unselectable="on" class="', Ext.baseCSSPrefix, 'column-header-trigger',
                '{childElCls}" style="{triggerStyle}"></div>',
            '</tpl>',
        '</div>',
        '{%this.renderContainer(out,values)%}'
    ],

    initComponent: function() {
        var me = this,
            textMetrics = new Ext.util.TextMetrics(),
            suggestedHeight;

        // si es verticalHeader y no es una columna padre se 
        if (me.verticalHeader && me.columns !== null) {
            suggestedHeight = textMetrics.getWidth(me.text) + textMetrics.getHeight(me.text) + 10;
            me.height = Math.max(suggestedHeight, me.height || suggestedHeight);
        }

        this.callParent(arguments);
    }
});
