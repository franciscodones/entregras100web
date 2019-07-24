Ext.define("Pyansa.data.proxy.Sql", {
    extend: "Ext.data.proxy.Client",

    alias: "pyansa.data.proxy.sql",

    requires: [
        "Ext.Object",
        "Ext.XTemplate"
    ],

    config: {
        /**
         * No es necesario un reader
         * @type {null}
         */
        reader: null,

        /**
         * No es necesario un writer
         * @type {null}
         */
        writer: null,

        /**
         * Formato default para guardar las instancias Date
         * @type {String}
         */
        defaultDateFormat: 'Y-m-d H:i:s'
    },

    /**
     * Conexion a la base de datos
     *
     * @type {Pyansa.database.sqlite.Connection}
     */
    connection: null,

    /**
     * Tabla a la cual hace referencia el proxy
     *
     * @type {Pyansa.database.sqlite.Table|String}
     */
    table: null,

    /**
     * Template de la sentencia select para la lectura de registros
     *
     * @type {String|Array}
     */
    selectStatementTpl: [
        "SELECT",
        "<tpl if='columns'>",
            " {[ values.columns.join(\", \") ]}",
        "<tpl else>",
            " *",
        "</tpl>",
        " FROM {table}"
    ],

    /**
     * Template de la sentencia insert para la insercion de registros
     *
     * @type {String|Array}
     */
    insertStatementTpl: [
        "INSERT INTO `{table}` (",
            "{[ values.columns.join(\", \") ]}",
        ") VALUES (",
            "{[ Ext.String.repeat(\"?\", values.columns.length, \", \") ]}",
        ")"
    ],

    /**
     * Template de la sentencia insert para la insercion de registros
     *
     * @type {String|Array}
     */
    updateStatementTpl: [
        "UPDATE `{table}` SET ",
            "{[",
                "values.columns.map(function(column) {",
                    "return column + \" = ?\";",
                "}).join(\", \")",
            "]}",
        " WHERE {idProperty} = ?"
    ],

    /**
     * Template de la sentencia delete para la eliminacion de registros
     *
     * @type {String|Array}
     */
    deleteStatementTpl: [
        "DELETE FROM `{table}`",
        " WHERE {idProperty} = ?"
    ],

    /**
     * Constructor
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        config = me.initProperties(config);
        this.callParent([config]);
    },

    /**
     * Inicializa las propiedades que esta clase utiliza
     *
     * @param  {Object} config
     * @return {Object}
     */
    initProperties: function(config) {
        var me = this;

        config = config || {};
        config.connection = config.connection || me.connection;
        config.table = config.table || me.table;

        return config;
    },

    /**
     * Sobreescritura de la funcion `create` para crear los registros.
     *
     * @param  {Ext.data.operation.Create}   operation
     */
    create: function(operation) {
        var me = this,
            connection = me.connection,
            records = operation.getRecords(),
            modelClass = me.getModel(),
            clientIdProperty = new modelClass().clientIdProperty || "clientId",
            resultSet = new Ext.data.ResultSet(),
            insertedRecords = [],
            i, ln, data, record;

        operation.setStarted();
        connection.transaction(function(transaction) {
            me.insertRecords(transaction, records, function(rows, error) {
                if (error) {
                    operation.setException(error);
                    return;
                }

                for (i = 0, ln = rows.length; i < ln; i++) {
                    data = rows[i];
                    record = {
                        id: data.id
                    };
                    record[clientIdProperty] = data[clientIdProperty];
                    insertedRecords.push(record);
                }

                resultSet.setRecords(insertedRecords);
                resultSet.setTotal(ln);
                resultSet.setCount(ln);
                resultSet.setSuccess(true);

                operation.process(resultSet);
            });
        });
    },

    /**
     * Sobreescritura de la funcion `read` para leer los registros.
     *
     * @param  {Ext.data.operation.Read}   operation
     */
    read: function(operation) {
        var me = this,
            connection = me.connection,
            params = operation.getParams() || {},
            recordCreator = operation.getRecordCreator(),
            modelClass = me.getModel(),
            idProperty = new modelClass().getIdProperty(),
            resultSet = new Ext.data.ResultSet(),
            records = [],
            record, i, ln, data;

        operation.setStarted();
        connection.transaction(function(transaction) {
            me.selectRecords(transaction, params, function(rows, error) {
                if (error) {
                    operation.setException(error);
                    return;
                }

                for (i = 0, ln = rows.length; i < ln; i++) {
                    data = rows[i];
                    record = recordCreator ? recordCreator(data, modelClass) : new modelClass(data);
                    records.push(record);
                }

                resultSet.setRecords(records);
                resultSet.setTotal(ln);
                resultSet.setCount(ln);
                resultSet.setSuccess(true);

                operation.process(resultSet);
            });
        });
    },

    /**
     * Sobreescritura de la funcion `update` para actualizar los registros.
     *
     * @param  {Ext.data.operation.Update}   operation
     */
    update: function(operation) {
        var me = this,
            connection = me.connection,
            records = operation.getRecords(),
            resultSet = new Ext.data.ResultSet(),
            updatedRecords = [],
            i, ln, data, record;

        operation.setStarted();
        connection.transaction(function(transaction) {
            me.updateRecords(transaction, records, function(rows, error) {
                if (error) {
                    operation.setException(error);
                    return;
                }

                resultSet.setRecords(rows);
                resultSet.setTotal(ln);
                resultSet.setCount(ln);
                resultSet.setSuccess(true);

                operation.process(resultSet);
            });
        });
    },

    /**
     * Sobreescritura de la funcion `erase` para eliminar los registros.
     *
     * @param  {Ext.data.operation.Destroy}   operation
     */
    erase: function(operation) {
        var me = this,
            connection = me.connection,
            records = operation.getRecords(),
            resultSet = new Ext.data.ResultSet(),
            updatedRecords = [],
            i, ln, data, record;

        operation.setStarted();
        connection.transaction(function(transaction) {
            me.deleteRecords(transaction, records, function(rows, error) {
                if (error) {
                    operation.setException(error);
                    return;
                }

                resultSet.setRecords(rows);
                resultSet.setTotal(ln);
                resultSet.setCount(ln);
                resultSet.setSuccess(true);

                operation.process(resultSet);
            });
        });
    },

    /**
     * Funcion auxiliar con la cual se obtiene un array con los datos "raw" de los registros.
     * Si se requiere cambiar la manera de obtener la informacion, es aconsejable sobreescribir esta funcion
     * y no la funcion `read`.
     * La funcion `selectRecords` no parsea los datos raw a records, es la funcion
     * `read` la que se encarga de transformarlos a su respectivo modelo.
     * Por ejemplo, si se requiriera utilizar una query diferente a la default, se sobreescribiria esta funcion
     * y se accederia directamente a la base de datos.
     * No se debe olvidar ejecutar la funcion `callback`.
     *
     * @param  {Object}   transaction
     * @param  {Object}   params
     * @param  {Function} callback
     */
    selectRecords: function(transaction, params, callback) {
        var me = this,
            table = me.table,
            records = [],
            query, rows, i, ln, data;

        query = new Ext.XTemplate(me.selectStatementTpl).apply({
            table: table.name
        });

        transaction.executeSql(
            query,
            [],
            function(transaction, resultSet) {
                rows = resultSet.rows;

                for (i = 0, ln = rows.length; i < ln; i++) {
                    data = rows.item(i);
                    records.push(data);
                }

                if (typeof callback == "function") {
                    callback.call(me, records);
                }
            },
            function(transaction, error) {
                if (typeof callback == "function") {
                    callback.call(me, null, error);
                }
            }
        );
    },

    /**
     * Funcion auxiliar con la cual se insertan los datos en la base de datos.
     * Si se requiere cambiar la manera de insertar la informacion, es aconsejable sobreescribir esta funcion
     * y no la funcion `create`.
     * La funcion `insertRecords` no reemplaza los campos devueltos en el record original, es la funcion `create`
     * la que se encarga de esto.
     * Por ejemplo, se puede devolver el `id` y `clientId` para hacer el reemplazo en el record original.
     * No se debe olvidar ejecutar la funcion `callback`.
     *
     * @param  {Object}   transaction
     * @param  {Array}   records
     * @param  {Function} callback
     */
    insertRecords: function(transaction, records, callback) {
        var me = this,
            table = me.table,
            columns = table.getColumns().collect("name"),
            insertedRecords = [],
            errors = [],
            totalRecords = records.length,
            executed = 0,
            record = records[0],
            idProperty = record.getIdProperty(),
            modelIdentifierPrefix = record.self.identifier.getPrefix(),
            queryWithIdProperty, queryWithoutIdProperty, columnsWithoutIdProperty;

        columnsWithoutIdProperty = columns.filter(function(column) {
            return column != idProperty;
        });

        queryWithIdProperty = new Ext.XTemplate(me.insertStatementTpl).apply({
            table: table.name,
            columns: columns
        });

        queryWithoutIdProperty = new Ext.XTemplate(me.insertStatementTpl).apply({
            table: table.name,
            columns: columnsWithoutIdProperty
        });

        // ordena los records de tal manera que los que contienen un id autogenerada se agreguen al ultimo
        records.sort(function(a, b) {
            var aIsAutogenerated = Ext.String.startsWith(a.getId(), modelIdentifierPrefix),
                bIsAutogenerated = Ext.String.startsWith(b.getId(), modelIdentifierPrefix);

            if (aIsAutogenerated && !bIsAutogenerated) {
                // a debe ir despues que b
                return 1;
            } else if (!aIsAutogenerated && bIsAutogenerated) {
                // a debe ir antes que b
                return -1;
            } else {
                // cualquier otra condicion deja los records tal como estan
                return 0
            }
        });

        Ext.Array.each(records, function(record) {
            var id = record.getId(),
                data = me.getRecordData(record),
                clientIdProperty = record.clientIdProperty || "clientId",
                query, values;

            if (Ext.String.startsWith(id, modelIdentifierPrefix)) {
                // si el id es autogenerado entonces no se tomara en cuenta la columna
                // al guardar en la base de datos
                query = queryWithoutIdProperty;
                values = me.getColumnValues(columnsWithoutIdProperty, data);
            } else {
                // si el id NO es autogenerado entonces la columna se guardara tal como viene
                query = queryWithIdProperty;
                values = me.getColumnValues(columns, data);
            }

            transaction.executeSql(
                query,
                values,
                function(transaction, resultSet) {
                    executed++;
                    record = {
                        id: resultSet.insertId
                    };
                    record[clientIdProperty] = id
                    insertedRecords.push(record);

                    if (executed === totalRecords && typeof callback === "function") {
                        callback.call(me, insertedRecords, errors.length > 0 ? errors : null);
                    }
                },
                function(transaction, error) {
                    executed++;
                    record = {
                        id: id,
                        error: error
                    };
                    record[clientIdProperty] = id;
                    errors.push(record);

                    if (executed === totalRecords && typeof callback === "function") {
                        callback.call(me, insertedRecords, errors);
                    }
                }
            );
        });
    },

    /**
     * Funcion auxiliar con la cual se actualizan los datos en la base de datos.
     * Si se requiere cambiar la manera de actualizar la informacion, es aconsejable sobreescribir esta funcion
     * y no la funcion `update`.
     * La funcion `updateRecords` no reemplaza los campos devueltos en el record original, es la funcion `update`
     * la que se encarga de esto.
     * Por ejemplo, se puede devolver el `id` y `clientId` para hacer el reemplazo en el record original.
     * No se debe olvidar ejecutar la funcion `callback`.
     *
     * @param  {Object}   transaction
     * @param  {Array}   records
     * @param  {Function} callback
     */
    updateRecords: function(transaction, records, callback) {
        var me = this,
            table = me.table,
            columns = table.getColumns().collect("name"),
            updatedRecords  = [],
            errors = [],
            totalRecords = records.length,
            idProperty = records[0].getIdProperty(),
            executed = 0;

        query = new Ext.XTemplate(me.updateStatementTpl).apply({
            table: table.name,
            columns: columns,
            idProperty: idProperty
        });

        Ext.Array.each(records, function(record) {
            var id = record.getId(),
                data = me.getRecordData(record),
                values = me.getColumnValues(columns, data),
                clientIdProperty = record.clientIdProperty || "clientId";

            transaction.executeSql(
                query,
                values.concat(id),
                function(transaction, resultSet) {
                    executed++;
                    record = {
                        id: id
                    };
                    record[clientIdProperty] = id;
                    updatedRecords .push(record);

                    if (executed === totalRecords && typeof callback === "function") {
                        callback.call(me, updatedRecords , errors.length > 0 ? errors : null);
                    }
                },
                function(transaction, error) {
                    executed++;
                    record = {
                        error: error
                    };
                    record[clientIdProperty] = id;
                    errors.push(record);

                    if (executed === totalRecords && typeof callback === "function") {
                        callback.call(me, updatedRecords , errors);
                    }
                }
            );
        });
    },

    /**
     * Funcion auxiliar con la cual se eliminan los datos en la base de datos.
     * Si se requiere cambiar la manera de eliminar la informacion, es aconsejable sobreescribir esta funcion
     * y no la funcion `erase`.
     * La funcion `deleteRecords` no altera los records originales, es la funcion `update` la que se encarga de esto.
     * No se debe olvidar ejecutar la funcion `callback`.
     *
     * @param  {Object}   transaction
     * @param  {Array}   records
     * @param  {Function} callback
     */
    deleteRecords: function(transaction, records, callback) {
        var me = this,
            table = me.table,
            columns = table.getColumns().collect("name"),
            deletedRecords  = [],
            errors = [],
            totalRecords = records.length,
            idProperty = records[0].getIdProperty(),
            executed = 0;

        query = new Ext.XTemplate(me.deleteStatementTpl).apply({
            table: table.name,
            idProperty: idProperty
        });

        Ext.Array.each(records, function(record) {
            var id = record.getId(),
                clientIdProperty = record.clientIdProperty || "clientId";

            transaction.executeSql(
                query,
                [id],
                function(transaction, resultSet) {
                    executed++;
                    record = {
                        id: id
                    };
                    record[clientIdProperty] = id;
                    deletedRecords .push(record);

                    if (executed === totalRecords && typeof callback === "function") {
                        callback.call(me, deletedRecords , errors.length > 0 ? errors : null);
                    }
                },
                function(transaction, error) {
                    executed++;
                    record = {
                        error: error
                    };
                    record[clientIdProperty] = id;
                    errors.push(record);

                    if (executed === totalRecords && typeof callback === "function") {
                        callback.call(me, deletedRecords , errors);
                    }
                }
            );
        });
    },

    /**
     * Formatea los datos de cada record.
     * Esta funcion debe ser sobreescrita si se desea dar un formato diferente al default.
     *
     * @param  {Ext.data.Model} record
     * @return {Object}
     */
    getRecordData: function(record) {
        var me = this,
            fields = record.getFields(),
            data = {},
            name, value, i, ln, field;

        for (i = 0, ln = fields.length; i < ln; i++) {
            field = fields[i];
            if (field.persist) {
                name = field.name;
                value = record.get(name);
                if (field.isDateField) {
                    value = me.parseDate(field, value);
                } else if (field.isBooleanField) {
                    value = me.parseBoolean(field, value);
                }
                data[name] = value;
            }
        }

        return data;
    },

    /**
     * Transforma un valor date al formato dado por el campo
     *
     * @param  {Ext.data.field.Date} field
     * @param  {Date} date
     * @return {Number|String}
     */
    parseDate: function (field, date) {
        if (Ext.isEmpty(date)) {
            return null;
        }

        var dateFormat = field.getDateFormat() || this.getDefaultDateFormat();
        switch (dateFormat) {
            case 'timestamp':
                return date.getTime() / 1000;
            case 'time':
                return date.getTime();
            default:
                return Ext.Date.format(date, dateFormat);
        }
    },

    /**
     * Transforma el valor a boolean para la base de datos
     *
     * @param  {Ext.data.field.Boolean} field
     * @param  {Object} value
     * @return {Number}
     */
    parseBoolean: function(field, value) {
        return value == null ? null : (!!value ? 1 : 0);
    },

    /**
     * Obtiene los valores de las columnas de los datos proporcionados
     *
     * @param  {Array} columns
     * @param  {Object} data
     * @return {Array}
     */
    getColumnValues: function(columns, data) {
        var ln = columns.length,
            values = [],
            i, column, value;

        for (i = 0; i < ln; i++) {
            column = columns[i];
            value = data[column];
            if (value !== undefined) {
                values.push(value);
            } else {
                // si no esta definido se agrega como null para que el record pueda ser agregado a la tabla
                // y evitar que se agregue el valor "undefined" (un string) como valor de la columna
                values.push(null);
            }
        }

        return values;
    }
});
