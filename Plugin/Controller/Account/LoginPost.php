<?php

namespace Genesisoft\MarketplaceExt\Plugin\Controller\Account;

class LoginPost
{
    protected $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Webkul\Marketplace\Helper\Data $helper)
    {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    public function afterExecute(\Magento\Customer\Controller\Account\LoginPost $subject, $result)
    {
        $redirectActive = $this->scopeConfig->getValue('marketplace/customerlogin/redirect_customer_enable');

        if (!$this->helper->isCustomerLoggedIn()) {
            $result->setPath('customer/account');
        } elseif ($redirectActive && !$this->helper->isSeller()) {
            $page = $this->scopeConfig->getValue('marketplace/customerlogin/redirect_customer_page');
            if (empty($page)) {
                $result->setPath('/');
            } else {
                $result->setPath($page);
            }
        }
        return $result;
    }

}
