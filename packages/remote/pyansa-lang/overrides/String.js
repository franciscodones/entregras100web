/**
 * Se crear este override para asegurar que se agreguen las funcionalidades de Pyansa.String
 */
Ext.define("Pyansa.overrides.String", {
    override: "Ext.String",

    requires: [
        "Pyansa.String"
    ]
}, function() {
    var prop;

    // se crean alias en Ext.String
    for (prop in Pyansa.String) {
        Ext.String[prop] = Pyansa.String[prop];
    }
});
