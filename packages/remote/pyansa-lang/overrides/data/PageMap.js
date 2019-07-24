/**
 * Sobreescritura de Ext.data.PageMap
 *
 * @override
 */
Ext.define('Pyansa.overrides.data.PageMap', {
    override:'Ext.data.PageMap',

    /**
     * Sobreescritura de la funcion `hasRange` debido a un bug con los BufferedStore
     * (ver https://www.sencha.com/forum/showthread.php?304363-Buffered-Store-Fatal-HasRange-Call)
     *
     * @param  {Number}  start
     * @param  {Number}  end
     * @return {Boolean}
     */
    hasRange: function(start, end) {
        var pageNumber = this.getPageFromRecordIndex(start),
            endPageNumber = this.getPageFromRecordIndex(end);
        for (; pageNumber <= endPageNumber; pageNumber++) {
            if (!this.hasPage(pageNumber)) {
                return false;
            }
        }
        return true;
    }
});
