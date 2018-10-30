/**
 * Define un constructor en Pyansa.lib.leaflet para que el cmd pueda tomarlo en cuenta en los
 * requires
 */
Ext.define("Pyansa.lib.leaflet", function() {
    var resourcesPath = Ext.manifest.resources.path;

    Ext.Boot.loadSync(resourcesPath + "/pyansa-ux/leaflet/leaflet.css")
    Ext.Boot.loadSync(resourcesPath + "/pyansa-ux/leaflet/leaflet-routing-machine.css");
    Ext.Boot.loadSync(resourcesPath + "/pyansa-ux/leaflet/L.Icon.Pulse.css")
    Ext.Boot.loadSync(resourcesPath + "/pyansa-ux/leaflet/leaflet.js");
    Ext.Boot.loadSync(resourcesPath + "/pyansa-ux/leaflet/leaflet-routing-machine.min.js");
    Ext.Boot.loadSync(resourcesPath + "/pyansa-ux/leaflet/L.Icon.Pulse.js");

    return {};
});
