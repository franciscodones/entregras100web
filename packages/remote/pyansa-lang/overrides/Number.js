/**
 * Se crear este override para asegurar que se agreguen las funcionalidades de Pyansa.Number
 */
Ext.define("Pyansa.overrides.Number", {
    override: "Ext.Number",

    requires: [
        "Pyansa.Number"
    ]
}, function() {
    var prop;

    // se crean alias en Ext.Number
    for (prop in Pyansa.Number) {
        Ext.Number[prop] = Pyansa.Number[prop];
    }
});
