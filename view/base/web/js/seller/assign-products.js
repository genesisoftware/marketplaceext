/* global $, $H */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedProducts = config.selectedProducts,
            assignedProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            trRowIndex;
        $('in_adminassign_products').value = Object.toJSON(assignedProducts);

        /**
         * Register Category Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerAssignedProduct(grid, element, checked)
        {
            if (element.className != "admin__control-checkbox") {
                var trElement = jQuery('#' + element.id).parents('tr');
                trRowIndex = trElement.index();
                var length = assignedProducts.keys().length;
                if (checked) {
                    if (!(element.value in assignedProducts._object)) {
                        if (length > 0) {
                            var max = Math.max.apply(Math, Object.values(assignedProducts._object));
                        } else {
                            var max = 0;
                        }
                        assignedProducts.set(element.value, max + 1);
                    }
                } else {
                    assignedProducts.unset(element.value);
                }
                $('in_adminassign_products').value = Object.toJSON(assignedProducts);
                grid.reloadParams = {
                    'selected_products[]': assignedProducts.keys()
                };
            }
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function assignedProductRowClick(grid, event)
        {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;
            trRowIndex = trElement.rowIndex-2;
            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');
                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }
        gridJsObject.rowClickCallback = assignedProductRowClick;
        gridJsObject.checkboxCheckCallback = registerAssignedProduct;
    };
});