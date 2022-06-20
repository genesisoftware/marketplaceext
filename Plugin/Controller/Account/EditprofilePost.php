<?php
namespace  Genesisoft\MarketplaceExt\Plugin\Controller\Account;

class EditprofilePost {

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
