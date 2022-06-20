<?php
namespace  Genesisoft\MarketplaceExt\Plugin\Controller\Account\EditprofilePost;

class Plugin {

    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
       $this->request = $request;
    }

    function beforeExecute() {
        $taxvat = $this->request->getParam('taxvat');
        $taxvat = str_replace(['-','.','/'], '',$taxvat);
        $this->request->setParam('taxvat',$taxvat);
        return;
    }
}
