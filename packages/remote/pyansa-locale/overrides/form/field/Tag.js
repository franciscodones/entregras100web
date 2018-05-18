/**
 * Sobreescribe los textos de la clase `Ext.form.field.Tag`
 * @override
 */
Ext.define('Pyansa.locale.form.field.Tag', {
    override: 'Ext.form.field.Tag',

    ariaAvailableListLabel: "Valores disponibles",
    ariaDeselectedText: "{0} removido de la seleccion",
    ariaHelpText: "Use las flechas ARRIBA y ABAJO para ver los valores disponibles, ENTER para seleccionar. " +
        "Use las flechas IZQUIERDA y DERECHA para ver los valores seleccionados, la tecla DEL para deseleccionar.",
    ariaHelpTextEditable: "Use las flechas ARRIBA y ABAJO para ver los valores disponibles, ENTER para seleccionar. " +
        "Escriba y presione ENTER para crear un nuevo valor. Use las flechas IZQUIERDA y DERECHA para ver los valores " +
        "seleccionados, la tecla DEL para deseleccionar.",
    ariaNoneSelectedText: "Valor no seleccionado",
    ariaSelectedListLabel: "Valores seleccionados",
    ariaSelectedText: "Seleccionado {0}"
});
