<?php
namespace Genesisoft\MarketplaceExt\Plugin\Block\Product;

use Magento\Framework\App\Request\DataPersistorInterface;

class CreateGetPersistentData
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;


    function __construct(DataPersistorInterface $dataPersistor)
    {
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Get Persistent Data for Product
     *
     * @return array
     */
    public function aroundGetPersistentData(\Webkul\Marketplace\Block\Product\Create $subject, callable $proceed)
    {
        $persistentData = (array)$this->dataPersistor->get('seller_catalog_product');
        $fields = [
            "set" => "",
            "type" => "",
            "product" => [
                "name" => "",
                "enable_quotation_mode" => "",
                "category_ids" => [],
                "description" => "",
                "short_description" => "",
                "sku" => "",
                "price" => "",
                "special_price" => "",
                "special_from_date" => "",
                "special_to_date" => "",
                "product_has_weight" => 1,
                "weight" => "",
                "mp_product_cart_limit" => "",
                "visibility" => "",
                "tax_class_id" => "",
                "meta_title" => "",
                "meta_keyword" => "",
                "meta_description" => "",
                "category_ids" => [],

                "quantity_and_stock_status" => [
                    "is_in_stock" => 1,
                    "qty" => ""
                ],
                "image" => "",
                "small_image" => "",
                "thumbnail" => ""
            ],
        ];

        $persistentData = $subject->setFieldsValue($persistentData, $fields);
        if (empty($persistentData['product'])) {
            $persistentData['product'] = $fields['product'];
        }

        $this->dataPersistor->clear('seller_catalog_product');
        return $persistentData;
    }

}
