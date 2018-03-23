/**
 * Sobreescritura de Ext.Array
 * @override
 */
Ext.define('Pyansa.overrides.Array', {
    override: 'Ext.Array',

    /**
     * Sustituye un elemento en el arreglo
     * @param  {Array} array
     * @param  {Object} item
     * @param  {Array} substitute
     * @return {Array}
     */
    substitute: function(array, item, substitute) {
        var index = this.indexOf(array, item);

        if (index < 0) {
            return array;
        }

        return this.replace(array, index, 1, substitute);
    }
});
