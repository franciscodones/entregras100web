/**
 * Sobreescritura de la clase Ext.data.request.Ajax
 *
 * @override
 */
Ext.define('Pyansa.overrides.data.request.Ajax', {
    override: 'Ext.data.request.Ajax'
}, function(Ajax) {

    // almacena la funcion original en una propiedad auxiliar
    Ajax.$$parseStatus = Ajax.parseStatus;

    // se sobreescribe la funcion static parseStatus debido a que los ajax con status 0 (request rechazado, sin internet, etc)
    // marcan como success las operaciondes de los store
    Ajax.parseStatus = function(status, response) {
        var ret = Ajax.$$parseStatus(status, response),
            type = response.responseType;

        console.log(response);
        if (status === 0 && (type === 'json' || type === 'document') && (!response.responseURL)) {
            ret.success = false;
        }
        
        return ret;
    };
});