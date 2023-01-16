AdminOrder.prototype.OMRunChangeSelectStoreView = false;

AdminOrder.prototype.showCustomersGrid = function(){
    AdminOrder.prototype.OMRunChangeSelectStoreView = true;
    order.customerSelectorShow();
    $$('.show-customer-grid')[0].hide();
};

AdminOrder.prototype.cancelSelectCustomer = function(){
    order.customerSelectorHide();
    $$('.show-customer-grid')[0].show();
};

AdminOrder.prototype.setDefaultStoreView = function(id){
    if(this.storeId == false){
        this.setStoreId(id);
    }
    if ($('store_switcher')) {
        $('store_switcher').setValue(this.storeId);
    }
};

AdminOrder.prototype.addCancelButtonToCustomerGrid = function(id){
    $$('#order-customer-selector .entry-edit-head>div')[0]
        .insert({'top':'<button class="scalable back hide-customer-grid" type="button" onclick="order.cancelSelectCustomer()"><span>Cancel</span></button>'});
};

AdminOrder.prototype.setStoreId = AdminOrder.prototype.setStoreId.wrap(function(callOriginal, id) {
    if (AdminOrder.updateStoreUrl == "undefined" || AdminOrder.updateStoreUrl == undefined) {
        return callOriginal(id);
    }

    var params = {'store_id':id};
    var url = AdminOrder.updateStoreUrl;
    new Ajax.Request(url, {
        parameters:params,
        onSuccess: function() {
            callOriginal(id);
            if ($('store_switcher')) {
                $('store_switcher').setValue(this.storeId);
            }
        }.bind(this)
    });
});

AdminOrder.prototype.setCustomerId = AdminOrder.prototype.setCustomerId.wrap(function(callOriginal, id) {
    callOriginal(id);

    order.cancelSelectCustomer();
    var buttonTitle = (order.customerId != false) ? 'Change Customer': 'Add Customer';
    order.productGridAddSelected();
    $$('button.show-customer-grid span')[0].update(buttonTitle);

    if ($('store_switcher')) {
        $('store_switcher').setValue(this.storeId);

        if(typeof AdminOrder.prototype.OMRunChangeSelectStoreView != "undefined" && AdminOrder.prototype.OMRunChangeSelectStoreView) {
            AdminOrder.prototype.OMRunChangeSelectStoreView = false;
            //$('store_switcher').fire('change');
            jQueryIWD('#store_switcher').trigger('change');
        }
    }
});

AdminOrder.prototype.productGridAddSelected = AdminOrder.prototype.productGridAddSelected.wrap(function(callOriginal) {
    callOriginal();
    this.showArea('search');

    /* scroll to added items */
    var floatingHeaderHeight = $$('.content-header-floating')[0].getHeight();
    var pos = Element.cumulativeOffset($('order-items'));
    window.scrollTo(pos.left, pos.top - floatingHeaderHeight);
});

AdminOrder.prototype.changeAddressField = AdminOrder.prototype.changeAddressField.wrap(function(callOriginal, event) {
    this.isShippingMethodReseted = false;
    callOriginal(event);
});

