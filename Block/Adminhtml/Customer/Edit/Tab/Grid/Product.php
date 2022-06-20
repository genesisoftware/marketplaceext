<?php
/**
 * Sobrescreve o _prepareColumns do Webkul que ignorava o Post ao montar a coluna do Grid
 */
namespace Genesisoft\MarketplaceExt\Block\Adminhtml\Customer\Edit\Tab\Grid;

use Magento\Customer\Controller\RegistryConstants;

class Product extends \Webkul\Marketplace\Block\Adminhtml\Customer\Edit\Tab\Grid\Product
{
    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_adminassign',
            [
                'type' => 'checkbox',
                'name' => 'in_adminassign',
                'index' => 'entity_id',
                'data-form-part' => $this->getData('target_form'),
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction',
                'values' => !empty($this->getRequest()->getPostValue('selected_products')) ?
                                $this->getRequest()->getPostValue('selected_products') :
                                $this->getSellerAssignedProducts()
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'sortable' => true
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Product Name'),
                'index' => 'name'
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Product SKU'),
                'index' => 'sku'
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Product Price'),
                'index' => 'price',
                'type' => 'currency',
                'currency_code' => (string)$this->_scopeConfig->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            ]
        );

        return \Magento\Backend\Block\Widget\Grid\Extended::_prepareColumns();
    }

}
