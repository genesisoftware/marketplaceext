<?php
namespace Genesisoft\MarketplaceExt\Plugin\Block\Product;

class CreateSetFieldsValue
{
    /**
     * Validate and Set Default Values for Fields
     *
     * @param array $persistentData
     * @param array $fields
     *
     * @return array
     */
    public function aroundSetFieldsValue(\Webkul\Marketplace\Block\Product\Create $subject, callable $proceed, &$persistentData, $fields)
    {
        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                if (empty($persistentData[$key])) {
                    $persistentData[$key] = [];
                }

                $subject->setFieldsValue($persistentData[$key], $fields[$key]);
            } else {
                if (empty($persistentData[$key])) {
                    $persistentData[$key] = $field;
                }
            }
        }

        return $persistentData;
    }
}
