/**
 * Funcionalidades extra para manipular numeros.
 * Se crea como una clase cualquiera para que el injector de dependencias de ExtJS
 * funcione de manera normal con este singleton.
 * Se usa la funcion posterior para reemplazar la clase creada por un objeto simple
 *
 * @singleton
 */
Ext.define("Pyansa.Number", {
    requires: [
        "Pyansa.Math"
    ]
}, function() {
    Pyansa.Number = {
        /**
         * Ajusta un numero a `n` decimales. Usando como opciones de ajuste los siguientes tipos:
         *
         * **********************************   "round"    **********************************
         *          -2    -1.5     -1.3     -1     0     1     1.3     1.5     2
         *           ^      |        |       ^           ^       |      |      ^
         *           +------+        +-------+           +-------+      +------+
         *
         * **********************************   "floor"    **********************************
         *          -2    -1.5     -1.3     -1     0     1     1.3     1.5     2
         *           ^      |        |                   ^      |       |
         *           +------+        |                   +------+       |
         *           +---------------+                   +--------------+
         *
         * **********************************   "trunc"    **********************************
         *          -2    -1.5     -1.3     -1     0     1     1.3     1.5     2
         *                  |        |       ^           ^      |       |
         *                  |        +-------+           +------+       |
         *                  +----------------+           +--------------+
         *
         * **********************************   "ceil"    ***********************************
         *          -2    -1.5     -1.3     -1     0     1     1.3     1.5     2
         *                  |        |       ^                  |       |      ^
         *                  |        +-------+                  |       +------+
         *                  +----------------+                  +--------------+
         *
         * NOTA: La function `toPrecision` ajusta la longitud de un numero a `n` digitos (el punto decimal no cuenta)
         * agregando `0` y punto decimal si se require. Debido a este comportamiento, esta funcion causa conflicto
         * cuando la parte entera tiene mas de 2 digitos.
         *
         * NOTA: La funcion `toFixed` devuelve un `string` no un `number`, ademas automaticamente redondea a la
         * cantidad de decimales deseada. Debido a estos comportamientos, esta funcion puede dar resultados no
         * deseados
         *
         * @param {Number} value Numero
         * @param {Number} [n=0] Numero de decimales
         * @param {String} [type="round"] Tipo de ajuste
         * @return {Number} Numero redondeado
         */
        decimalAdjust: function(value, n, type) {
            n = n || 0;
            type = type || "round";

            value = +value;
            value = Ext.Number.correctFloat(value);
            n = +n;
            // si `value` no es un numero o `n` no es entero retorna `NaN`
            if (isNaN(value) || !(typeof n === 'number' && n % 1 === 0 && n >= 0)) {
                return NaN;
            }
            // Avanza el punto decimal a la derecha para realiza el tipo
            // de ajuste deseado y eliminar los decimales sobrantes
            value = value.toString().split('e');
            value = Math[type](+(value[0] + 'e' + ((value[1] ? +value[1] : 0) + n)));
            // Regresa el punto decimal a su lugar original y
            // vuelve a convertir a float
            value = value.toString().split('e');
            return +(value[0] + 'e' + ((value[1] ? +value[1] : 0) - n));
        },

        /**
         * Trunca un numero a `n` decimales (ver @decimalAdjust)
         *
         * @param  {Number} value
         * @param  {Number} n
         * @return {Number}
         */
        trunc: function(value, n) {
            return this.decimalAdjust(value, n, "trunc");
        },

        /**
         * Redondea un numero a `n` decimales (ver @decimalAdjust)
         *
         * @param  {Number} value
         * @param  {Number} n
         * @return {Number}
         */
        round: function(value, n) {
            return this.decimalAdjust(value, n, "round");
        },

        /**
         * Avanza al siguiente decimal un numero a `n` decimales (ver @decimalAdjust)
         *
         * @param  {Number} value
         * @param  {Number} n
         * @return {Number}
         */
        ceil: function(value, n) {
            return this.decimalAdjust(value, n, "ceil");
        },

        /**
         * Retrocede al anterior decimal un numero a `n` decimales (ver @decimalAdjust)
         *
         * @param  {Number} value
         * @param  {Number} n
         * @return {Number}
         */
        floor: function(value, n) {
            return this.decimalAdjust(value, n, "floor");
        }
    };
});
