/**
 * Funcionalidades extra para manipular strings
 * Se crea como una clase cualquiera para que el injector de dependencias de ExtJS
 * funcione de manera normal con este singleton.
 * Se usa la funcion posterior para reemplazar la clase creada por un objeto simple
 *
 * @singleton
 */
Ext.define("Pyansa.String", {}, function() {
    var prop;

    Pyansa.String = {
        /**
         * Rellena por la derecha un string con el caracter especificado
         *
         * @param {String} value Substring a rellenar
         * @param {Number} size Longitud final
         * @param {String} [character=" "] Caracter de relleno
         * @return {String} String rellenado
         */
        rightPad: function(value, size, character) {
            var result = String(value);

            character = character || " ";
            while (result.length < size) {
                result = result + character;
            }
            return result;
        },

        /**
         * Genera un string de la longitud proporcionada (min. 1, maximo 1000 caracteres)
         * tomando los caracteres de una palabra determinada
         *
         * @param {Number} length Longitud del string
         * @param {String} [word=abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789] Palabra fuente
         * @return {String} String generado
         */
        random: function(length, word) {
            var str = "",
                i;

            word = word || "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

            for (i = 0; i < length; i++) {
                str += word.charAt(Ext.Number.randomInt(0, word.length - 1));
            }
            return str;
        },

        /**
         * Convierte un string `camelCase`
         *
         * @param  {String} value
         * @param  {Boolean} [capitalize] `true` para convertir a `UpperCamelCase`
         * @return {String}
         */
        toCamelCase: function(value, capitalize) {
            var words = value.split(/[_.\- ]+/),
                camelString = "",
                i;

            capitalize = capitalize || false;

            for (i = 0; i < words.length; i++) {
                if (i == 0) {
                    if (capitalize) {
                        camelString += words[i].charAt(0).toUpperCase();
                        camelString += words[i].substr(1);
                    } else {
                        camelString += words[i];
                    }
                } else {
                    camelString += words[i].charAt(0).toUpperCase();
                    camelString += words[i].substr(1);
                }
            }

            return camelString;
        },

        /**
         * Parte un string en piezas de tamaño `length` caracteres
         *
         * @param  {String} value String a partir
         * @param  {Number} length Tamaño de las piezas
         * @param  {Boolean} [reverse] Inicia las piezas desde el final
         * @return {Array} Piezas
         */
        chunk: function(value, length, reverse) {
            var pieces = [],
                substr = "";

            length = length || 1;
            reverse = reverse || false;

            while (value != "") {
                if (reverse) {
                    substr = value.substr(-length);
                    value = value.substring(0, value.length - length);
                    pieces.unshift(substr);
                } else {
                    substr = value.substr(0, length);
                    value = value.substr(length);
                    pieces.push(substr);
                }
            }

            return pieces;
        },

        /**
         * Separa un string en parrafos
         *
         * @param  {String} value
         * @param  {Number} length Longitud del parrafo
         * @param  {Boolean} [word=true] Partir parrafos en palabras enteras
         * @return {String[]}
         */
        splitParagraphs: function(value, length, word) {
            var paragraphs = [],
                p = "",
                words;

            word = word || true;

            if (!word) {
                return this.chunk(value.trim(), length);
            }

            words = Ext.String.splitWords(value);

            while (words.length > 0) {
                if ((p.length + words[0].length + 1) > length) {
                    paragraphs.push(p);
                    p = "";
                }

                if (p == "") {
                    p = words[0];
                } else if (p != "") {
                    p = p + " " + words[0];
                }

                words = words.slice(1);

                if (words.length == 0) {
                    paragraphs.push(p);
                }
            }

            return paragraphs;
        }
    };

    // se crean alias en Ext.String
    for (prop in Pyansa.String) {
        Ext.String[prop] = Pyansa.String[prop];
    }
});
