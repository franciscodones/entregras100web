/**
 * Se crear este override para asegurar que se agreguen las funcionalidades de Pyansa.util.Format
 */
Ext.define("Pyansa.overrides.util.Format", {
    override: "Ext.util.Format",

    requires: [
        "Pyansa.util.Format"
    ]
}, function() {
    var prop;

    // se crean alias en Ext.util.Format
    for (prop in Pyansa.util.Format) {
        Ext.util.Format[prop] = Pyansa.util.Format[prop];
    }
});
