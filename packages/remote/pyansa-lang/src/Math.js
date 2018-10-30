/**
 * Funcionalidades extra para Math.
 * Se crea como una clase cualquiera para que el injector de dependencias de ExtJS
 * funcione de manera normal con este singleton.
 * Se usa la funcion posterior para reemplazar la clase creada por un objeto simple
 *
 * @singleton
 */
Ext.define("Pyansa.Math", {}, function() {
    var prop;

    Pyansa.Math = {
        /**
         * Trunca un valor
         *
         * @param {Number} x
         * @return {Number} Numero truncado
         */
        trunc: Math.trunc || function(x) {
            return (x < 0 ? Math.ceil(x) : Math.floor(x));
        }
    };

    // se crean alias en Math
    for (prop in Pyansa.Math) {
        Math[prop] = Pyansa.Math[prop];
    }
});
