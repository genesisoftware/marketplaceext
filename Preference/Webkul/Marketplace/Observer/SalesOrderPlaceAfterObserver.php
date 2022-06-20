<?php
namespace Genesisoft\MarketplaceExt\Preference\Webkul\Marketplace\Observer;

class SalesOrderPlaceAfterObserver extends \Webkul\Marketplace\Observer\SalesOrderPlaceAfterObserver
{
    /**
     * Seller Product Sales Calculation Method.
     *
     * @param \Magento\Sales\Model\Order $order
     */
    public function productSalesCalculation($order)
    {
        /*
        * Marketplace Order details save before Observer
        */
        $this->_eventManager->dispatch(
            'mp_order_save_before',
            ['order' => $order]
        );

        /*
        * Get Global Commission Rate for Admin
        */
        $percent = $this->_marketplaceHelper->getConfigCommissionRate();

        /*
        * Get Current Store Currency Rate
        */
        $currentCurrencyCode = $order->getOrderCurrencyCode();
        $baseCurrencyCode = $order->getBaseCurrencyCode();
        $allowedCurrencies = $this->_marketplaceHelper->getConfigAllowCurrencies();
        $rates = $this->_marketplaceHelper->getCurrencyRates(
            $baseCurrencyCode,
            array_values($allowedCurrencies)
        );
        if (empty($rates[$currentCurrencyCode])) {
            $rates[$currentCurrencyCode] = 1;
        }

        $lastOrderId = $order->getId();

        /*
        * Marketplace Credit Management module Observer
        */
        $this->_eventManager->dispatch(
            'mp_discount_manager',
            ['order' => $order]
        );

        $this->_eventManager->dispatch(
            'mp_advance_commission_rule',
            ['order' => $order]
        );

        $sellerData = $this->getSellerProductData($order, $rates[$currentCurrencyCode]);

        $sellerProArr = $sellerData['seller_pro_arr'];
        $sellerTaxArr = $sellerData['seller_tax_arr'];
        $sellerCouponArr = $sellerData['seller_coupon_arr'];

        $taxToSeller = $this->_marketplaceHelper->getConfigTaxManage();
        $shippingAll = $this->_coreSession->getData('shippinginfo');
//        $selectedShippingMethod = explode('_',$order->getShippingMethod());
        $selectedShippingMethod = explode('_', $order->getShippingMethod(), 2); //Alteração no explode para remoção apenas do primeiro '_'. Antes ele quebrava o array em 3 itens e não prosseguia, pois não identificava o método de envio.

        if (isset($shippingAll['mpfrenet']) && $selectedShippingMethod[0] == 'mpfrenet') {
            foreach ($shippingAll['mpfrenet'] as $k => $v) {
                $shippingArray[$v['seller_id']] = $v['submethod'][$selectedShippingMethod[1]]['base_amount'];
            }
        }
        try {
            $shippingAllCount = count($shippingAll);
        } catch (\Exception $e) {
            $shippingAllCount = false;
        }
        foreach ($sellerProArr as $key => $value) {
            $productIds = implode(',', $value);
            $data = [
                'order_id' => $lastOrderId,
                'product_ids' => $productIds,
                'seller_id' => $key,
                'total_tax' => $sellerTaxArr[$key],
                'tax_to_seller' => $taxToSeller,
            ];

            if ($selectedShippingMethod[0] == 'mpfrenet') {
                $data['shipping_charges'] = $shippingArray[$key];
            }

            if (!$shippingAllCount && $key == 0) {
                $shippingCharges = $order->getBaseShippingAmount();
                $data = [
                    'order_id' => $lastOrderId,
                    'product_ids' => $productIds,
                    'seller_id' => $key,
                    'shipping_charges' => $shippingCharges,
                    'total_tax' => $sellerTaxArr[$key],
                    'tax_to_seller' => $taxToSeller,
                ];
            }
            if (!empty($sellerCouponArr) && !empty($sellerCouponArr[$key])) {
                $data['coupon_amount'] = $sellerCouponArr[$key];
            }
            $collection = $this->_objectManager->create(
                'Webkul\Marketplace\Model\Orders'
            );
            $collection->setData($data);
            $collection->setCreatedAt($this->_date->gmtDate());
            $collection->setSellerPendingNotification(1);
            $collection->setUpdatedAt($this->_date->gmtDate());
            $collection->save();
            $sellerOrderId = $collection->getId();
            $this->_objectManager->create(
                'Webkul\Marketplace\Helper\Notification'
            )->saveNotification(
                \Webkul\Marketplace\Model\Notification::TYPE_ORDER,
                $sellerOrderId,
                $lastOrderId
            );
        }
        /*
        * Marketplace Order details save after Observer
        */
        $this->_eventManager->dispatch(
            'mp_order_save_after',
            ['order' => $order]
        );
    }
}
