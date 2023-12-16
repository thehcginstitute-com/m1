function markVisited(productID) {
    new Ajax.Request('../index.php/ebizautoresponder/autoresponder/markVisitedProducts?product_id='+productID, { method:'get', onSuccess: function(transport){
    }
    });
}
(function() {
    var cb = function() {
    	// 2018-09-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
    	var p = $$('input[name^=product]').first();
    	if (p) {
			new Ajax.Request('../index.php/ebizautoresponder/autoresponder/getVisitedProductsConfig?product_id='+p.value, { method:'get', onSuccess: function(transport){
					if(transport.responseJSON.time > -1) {
						markVisited.delay(transport.responseJSON.time, p.value);
					}
				}
			});
		}
    };
    if (document.loaded) {
        cb();
    } else {
        document.observe('dom:loaded', cb);
    }
})();