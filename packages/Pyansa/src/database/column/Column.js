/**
 * Estructura para una columna
 * @class
 */
Ext.define("Pyansa.database.column.Column", {

    alias: "pyansa.database.column.column",

    requires: [
        "Pyansa.database.column.constraint.ColumnConstraint",
        "Pyansa.database.column.constraint.NullConstraint",
        "Pyansa.database.column.constraint.DefaultConstraint",
        "Ext.Array",
        "Ext.XTemplate"
    ],

    /**
     * Nombre de la columna
     * @type {String}
     */
    name: null,

    /**
     * Tipo de columna
     * @type {String}
     */
    type: null,

    /**
     * Constraints de la columna
     * @type {Array}
     */
    constraints: null,

    /**
     * Constructor de la clase
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this,
            constraints,
            nullConstraint = new Pyansa.database.column.constraint.NullConstraint();

        Ext.apply(me, config);

        constraints = Ext.Array.from(me.constraints);
        me.constraints = [];

        if (!Ext.Array.contains(["text" , "integer" , "numeric", "real"], me.type)) {
            Ext.raise("El tipo \"" + me.type + "\" es inválido para una columna de la tabla");
        }

        // agrega los constraints
        Ext.Array.each(constraints, me.addConstraint, me);

        // se asegura que la columna posea un `NullConstraint`
        if (me.validateConstraint(nullConstraint, true)) {
            me.addConstraint(nullConstraint);
        }
    },

    /**
     * Valida el constraint
     * @param  {Pyansa.database.column.constraint.ColumnConstraint} constraint
     * @param {Boolean} [safe] Si es en modo seguro se retorna un Boolean en vez de arrojar un error
     * @return {Pyansa.database.column.constraint.ColumnConstraint}
     */
    validateConstraint: function(constraint, safe) {
        var me = this,
            hasNullConstraint, hasDefaultConstraint;

        hasNullConstraint = me.constraints.some(function(col) {
            return col instanceof Pyansa.database.column.constraint.NullConstraint;
        });

        hasDefaultConstraint = me.constraints.some(function(col) {
            return col instanceof Pyansa.database.column.constraint.DefaultConstraint;
        });

        if (constraint instanceof Pyansa.database.column.constraint.NullConstraint && hasNullConstraint) {
            if (safe) {
                return false;
            }
            Ext.raise(
                "La columna \"" + me.name + "\" ya tiene una instancia de " +
                "Pyansa.database.column.constraint.NullConstraint"
            );
        }

        if (constraint instanceof Pyansa.database.column.constraint.DefaultConstraint && hasDefaultConstraint) {
            if (safe) {
                return false;
            }
            Ext.raise(
                "La columna \"" + me.name + "\" ya tiene una instancia de " +
                "Pyansa.database.column.constraint.DefaultConstraint"
            );
        }

        return constraint;
    },

    /**
     * Agrega un constraint a la columna
     * @param {Pyansa.database.column.constraint.ColumnConstraint|Object} constraint
     */
    addConstraint: function(constraint) {
        var me = this;

        if (Ext.isSimpleObject(constraint)) {
            if (constraint.type == "null") {
                constraint = new Pyansa.database.column.constraint.NullConstraint(constraint);
            } else if (constraint.type == "default") {
                constraint = new Pyansa.database.column.constraint.DefaultConstraint(constraint);
            } else {
                Ext.raise("El tipo constraint \"" + constraint.type + "\" es inválido");
            }
        }

        me.constraints.push(me.validateConstraint(constraint));
    },

    /**
     * Obtiene el XTemplate relacionado la columna
     * @return {Ext.XTemplate}
     */
    getStatementTpl: function() {
        var me = this,
            tpl;

        tpl = [
            "`{name}` ",
            "{[values.type.toUpperCase()]}",
            "<tpl for='constraints'>",
                " {[values.buildStatement()]}",
            "</tpl>"
        ];

        return new Ext.XTemplate(tpl);
    },

    /**
     * Genera el string statement con los valores de la columna
     * @param  {Ext.XTemplate} [tpl]
     * @return {String}
     */
    buildStatement: function(tpl) {
        var me = this;

        tpl = tpl || me.getStatementTpl();

        return tpl.apply(me);
    }
});
