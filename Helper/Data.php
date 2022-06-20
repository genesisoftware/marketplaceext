<?php

namespace Genesisoft\MarketplaceExt\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $config_path
     * @return mixed
     */
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue($configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
