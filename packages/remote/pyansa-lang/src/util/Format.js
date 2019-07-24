/**
 * Funcionalidades extra para manipular formatos
 * Se crea como una clase cualquiera para que el injector de dependencias de ExtJS
 * funcione de manera normal con este singleton.
 * Se usa la funcion posterior para reemplazar la clase creada por un objeto simple
 *
 * @singleton
 */
Ext.define("Pyansa.util.Format", {
    requires: [
        "Ext.util.Format",
        "Pyansa.Number",
        "Pyansa.String"
    ]
}, function() {
    Pyansa.util.Format = {
        numberName: function(num) {
            var UNIDADES = {
                    "0": "",
                    "1": "un",
                    "2": "dos",
                    "3": "tres",
                    "4": "cuatro",
                    "5": "cinco",
                    "6": "seis",
                    "7": "siete",
                    "8": "ocho",
                    "9": "nueve"
                },
                DECENAS = {
                    "1": "",
                    "2": "",
                    "3": "treinta",
                    "4": "cuarenta",
                    "5": "cincuenta",
                    "6": "sesenta",
                    "7": "setenta",
                    "8": "ochenta",
                    "9": "noventa"
                },
                CENTENAS = {
                    "1": "",
                    "2": "doscientos",
                    "3": "trescientos",
                    "4": "cuatrocientos",
                    "5": "quinientos",
                    "6": "seiscientos",
                    "7": "setecientos",
                    "8": "ochocientos",
                    "9": "novecientos"
                },
                name = "",
                clases, numStr, i, claseNum, millaresMillones, millones, millares, unidades;

            num = Pyansa.Number.trunc(num);
            if (num < 0 || num > 999999999999) {
                throw new Error("El numero a convertir debe entrar en el rango de 0 a 999999999999");
            }
            if (num == 0) {
                return "cero";
            }
            numStr = Ext.String.leftPad(num.toString(), 12, "0");
            clases = Ext.String.chunk(numStr, 3);

            // millares de millones
            millaresMillones = getMillares(
                Ext.String.chunk(clases[0], 1).map(function(i) { return parseInt(i); })
            );

            // millones
            millones = getCentenas(
                Ext.String.chunk(clases[1], 1).map(function(i) { return parseInt(i); })
            );

            // millares
            millares = getMillares(
                Ext.String.chunk(clases[2], 1).map(function(i) { return parseInt(i); })
            );

            // centenas, decenas, unidades
            unidades = getCentenas(
                Ext.String.chunk(clases[3], 1).map(function(i) { return parseInt(i); })
            );

            // armado de nombre
            millones = (millaresMillones + " " + millones).trim() == "" ?
                "" :
                (millaresMillones + " " + millones).trim() + " millones";
            unidades = (millares + " " + unidades).trim();
            name = (millones + " " + unidades).trim();

            return name;


            function getUnidades(digito) {
                return UNIDADES[digito];
            }

            function getDecenas(digitos) {
                var decena = digitos[0],
                    unidad = digitos[1],
                    str;

                switch (decena) {
                    case 0: str = getUnidades(unidad); break;
                    case 1:
                        switch (unidad) {
                            case 0: str = "diez"; break;
                            case 1: str = "once"; break;
                            case 2: str = "doce"; break;
                            case 3: str = "trece"; break;
                            case 4: str = "catorce"; break;
                            case 5: str = "quince"; break;
                            default: str = "dieci" + getUnidades(unidad); break;
                        }
                        break;
                    case 2:
                        switch (unidad) {
                            case 0: str = "veinte"; break;
                            default: str = "veinti" + getUnidades(unidad); break;
                        }
                        break;
                    default:
                        switch (unidad) {
                            case 0: str = DECENAS[decena]; break;
                            default: str = DECENAS[decena] + " y " + getUnidades(unidad); break;
                        }
                        break;
                }

                return str;
            }

            function getCentenas(digitos) {
                var centena = digitos[0],
                    decena = digitos[1],
                    unidad = digitos[2],
                    str;

                switch (centena) {
                    case 0: str = getDecenas([decena, unidad]); break;
                    case 1:
                        switch (decena) {
                            case 0:
                                switch (unidad) {
                                    case 0: str = "cien"; break;
                                    default: str = "ciento " + getUnidades(unidad); break;
                                }
                                break;
                            default: str = "ciento " + getDecenas([decena, unidad]); break;
                        }
                        break;
                    default:
                        switch (decena) {
                            case 0:
                                switch (unidad) {
                                    case 0: str = CENTENAS[centena]; break;
                                    default: str = CENTENAS[centena] + " " + getUnidades(unidad); break;
                                }
                                break;
                            default: str = CENTENAS[centena] + " " + getDecenas([decena, unidad]); break;
                        }
                        break;
                }

                return str;
            }

            function getMillares(digitos) {
                var centena = digitos[0],
                    decena = digitos[1],
                    unidad = digitos[2],
                    str;

                str = getCentenas([centena, decena, unidad]);
                if (str == "") {
                    str = "";
                } else if (str == "un") {
                    str = "mil";
                } else {
                    str = str + " mil";
                }

                return str;
            }
        }
    };
});
