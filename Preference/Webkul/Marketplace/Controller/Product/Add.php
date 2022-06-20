<?php
namespace Genesisoft\MarketplaceExt\Preference\Webkul\Marketplace\Controller\Product;

class Add extends \Webkul\Marketplace\Controller\Product\Add
{
     /**
     * Seller Product Add Action.
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $helper = $this->_objectManager->create(
            'Webkul\Marketplace\Helper\Data'
        );
        $isPartner = $helper->isSeller();
        if ($isPartner == 1) {
            try {
                $set = $this->getRequest()->getParam('set');
                $type = $this->getRequest()->getParam('type');
                if (isset($set) && isset($type)) {
                    $helper = $this->_objectManager->get(
                        'Webkul\Marketplace\Helper\Data'
                    );
                    $allowedsets = explode(',', $helper->getAllowedAttributesetIds());
                    $allowedtypes = explode(',', $helper->getAllowedProductType());
                    if (!in_array($type, $allowedtypes) || !in_array($set, $allowedsets)) {

                        return $this->resultRedirectFactory->create()->setPath(
                            '*/*/create',
                            ['_secure' => $this->getRequest()->isSecure()]
                        );
                    }
                    $product = $this->productBuilder->build(
                        $this->getRequest()->getParams(),
                        $helper->getCurrentStoreId()
                    );
                    $resultPage = $this->_resultPageFactory->create();
                    if ($helper->getIsSeparatePanel()) {
                        $resultPage->addHandle('marketplace_layout2_product_add');
                    }
                    $resultPage->getConfig()->getTitle()->set(
                        __('Add Product')
                    );

                    return $resultPage;
                } else {
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/create',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $this->resultRedirectFactory->create()->setPath(
                    '*/*/create',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/becomeseller',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }
}
