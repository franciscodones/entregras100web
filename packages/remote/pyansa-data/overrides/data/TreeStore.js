/**
 * Sobreescritura de Ext.data.TreeStore
 * @override
 */
Ext.define('Pyansa.overrides.data.TreeStore', {
    override: 'Ext.data.TreeStore',

    /**
     * Cuenta la cantidad de nodos sin importar si estan colapsados o filtrados.
     * Se tomara en cuentra el nodo `root` si este no lleva por id "root"
     *
     * @return {Number}
     */
    getNodesCount: function() {
        var count = 0;

        this.getRoot().cascade({
            before: function(node) {
                if (!node.isRoot() || node.getId() != "root") {
                    count++;
                }
            }
        });

        return count;
    },

    /**
     * Obtiene un array con todos los nodos sin importar si estan colapsados o filtrados.
     * Se tomara en cuentra el nodo `root` si este no lleva por id "root"
     *
     * @return {Ext.data.NodeInterface[]}
     */
    getNodes: function() {
        var nodes = [];

        this.getRoot().cascade({
            before: function(node) {
                if (!node.isRoot() || node.getId() != "root") {
                    nodes.push(node);
                }
            }
        });

        return nodes;
    },

    /**
     * Llama una funcion proporcionada por cada nodo sin importar si estan colapsados o filtrados.
     * Se tomara en cuentra el nodo `root` si este no lleva por id "root"
     *
     * @param  {Function} fn
     * @param  {Object}   scope
     */
    eachNode: function(fn, scope) {
        var i = 0;

        this.getRoot().cascade(function(node) {
            if (!node.isRoot() || node.getId() != "root") {
                return fn.call(scope || node, node, i++);
            }
        });
    }
});
