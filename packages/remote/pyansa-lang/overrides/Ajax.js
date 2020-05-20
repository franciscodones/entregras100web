/**
 * Sobreescritura de Ext.data.Connection
 */
Ext.define('Pyansa.overrides.Ajax', {
    override: 'Ext.Ajax',

    requires: [
        "Pyansa.overrides.data.Connection"
    ]
}, function() {
    var me = this;

    // actualiza la config que se sobreescribio en Pyansa.overrides.data.Connection
    me.setConfig("sendTimeoutAsHeader", me.config.sendTimeoutAsHeader);

    /**
     * Resuelve la respuesta del request
     *
     * @return {String}
     */
    me.resolveResponseError = function(value, defaultMessage) {
        if (value instanceof Ext.form.action.Action) {
            return resolveFormAction(value);
        } else if (value instanceof Ext.data.operation.Operation) {
            return resolveOperation(value);
        } else if (value instanceof Ext.data.Batch) {
            return resolveBatch(value);
        } else {
            return defaultMessage;
        }

        function resolveResponse(response) {
            var message;

            if (response.responseJson) {
                // si el servidor respondio con un json
                message = response.responseJson.message;
            } else if (response.responseText) {
                // si el servidor respondio con un texto se trata de convertir a json
                response.responseJson = Ext.JSON.decode(response.responseText, true);
                if (response.responseJson) {
                    message = response.responseJson.message;
                } else {
                    message = response.responseText;
                }
            } else if (response.statusText) {
                // si el servidor solo respondio un error de http
                message = response.statusText;
            } else {
                // mensaje default
                message = defaultMessage;
            }

            return message;
        }

        function resolveBatch(batch) {
            var operations = batch.getExceptions(),
                message;

            if (operations.length > 0) {
                message = resolveOperation(operations[0]);
            } else {
                message = defaultMessage;
            }

            return message;
        }

        function resolveOperation(operation) {
            var error = operation.getError(),
                response, message;

            if (typeof error === "string") {
                message = error;
            } else if (error && error.response) {
                message = resolveResponse(error.response);
            } else {
                message = defaultMessage;
            }

            return message;
        }

        function resolveFormAction(action) {
            var message, response;

            if (action.failureType == Ext.form.action.Action.CLIENT_INVALID) {
                message = 'Error al enviar la informacion al servidor. Información inválida';
            } else if (action.failureType == Ext.form.action.Action.CONNECT_FAILURE) {
                message = 'Ha ocurrido un error al conectarse con el servidor';
            } else if (action.failureType == Ext.form.action.Action.SERVER_INVALID) {
                message = resolveResponse(action.response);
            } else {
                message = defaultMessage;
            }

            return message;
        }
    }
});
