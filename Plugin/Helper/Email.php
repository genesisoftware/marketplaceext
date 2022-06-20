<?php

namespace Genesisoft\MarketplaceExt\Plugin\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Email
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function beforeSendQuerypartnerEmail($subject, $data, $emailTemplateVariables, $senderInfo, $receiverInfo)
    {

        $seller_contact = $this->scopeConfig->getValue('marketplace/general_settings/seller_contact', ScopeInterface::SCOPE_STORE);
        if($seller_contact){
            $receiverInfo['email'] = $this->scopeConfig->getValue('marketplace/general_settings/adminemail', ScopeInterface::SCOPE_STORE);;
        }

        return [$data, $emailTemplateVariables, $senderInfo, $receiverInfo];
    }

}

