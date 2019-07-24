/**
 * Sobreescritura de Ext.mixin.Responsive
 *
 * @override
 */
Ext.define('Pyansa.overrides.mixin.Responsive', {
    override: 'Ext.mixin.Responsive',

    config: {
        /**
         * Establece formulas con los prefijos de las clases responsive.
         *
         * @type {Object}
         */
        responsiveFormulas: {
            xxs: function(context) {
                return context.width < 320;
            },
            xs: function(context) {
                return context.width >= 320 && context.width < 540;
            },
            sm: function(context) {
                return context.width >= 540 && context.width < 720;
            },
            md: function(context) {
                return context.width >= 720 && context.width < 1024;
            },
            lg: function(context) {
                return context.width >= 1024 && context.width < 1280;
            },
            xl: function(context) {
                return context.width >= 1280 && context.width < 1600;
            },
            xxl: function(context) {
                return context.width >= 1600;
            }
        }
    }
});