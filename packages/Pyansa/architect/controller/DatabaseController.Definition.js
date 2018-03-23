{
    "className": "Pyansa.controller.DatabaseController",
    "classAlias": "pyansa.controller.databasecontroller",
    "inherits": "Ext.app.Controller",
    "autoName": "MyDatabaseController",
    "toolbox": {
        "name": "Database Controller",
        "category": "Controllers",
        "groups": ["Pyansa"]
    },
    "configs": [
        {
            "name": "database",
            "type": "object",
            "initialValue": {
                "name": "mydatabase",
                "description": "MyDatabase 1.0",
                "version": "1.0",
                "size": 1024,
                "tables": [
                    {
                        "name": "mytable",
                        "columns": [
                            {
                                "name": "id",
                                "type": "integer",
                                "constraints": {
                                    "isAutoincrement": true
                                }
                            },
                            {
                                "name": "my_text_column",
                                "type": "text",
                                "constraints": {
                                    "acceptsNull": true
                                }
                            },
                            {
                                "name": "my_number_column",
                                "type": "integer",
                                "constraints": {
                                    "defaultValue": 1
                                }
                            }
                        ]
                    }
                ]
            }
        }
    ]
}
