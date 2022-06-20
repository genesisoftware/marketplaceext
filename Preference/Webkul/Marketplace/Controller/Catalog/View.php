<?php
namespace Genesisoft\MarketplaceExt\Preference\Webkul\Marketplace\Controller\Catalog;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class View extends \Webkul\Marketplace\Controller\Catalog\View
{

    protected $authSession;

    public function __construct(Context $context, \Magento\Catalog\Helper\Product\View $viewHelper, \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory, PageFactory $resultPageFactory, \Psr\Log\LoggerInterface $logger = null, \Magento\Framework\Json\Helper\Data $jsonHelper = null, \Magento\Backend\Model\Auth\Session $authSession)
    {
        parent::__construct($context, $viewHelper, $resultForwardFactory, $resultPageFactory, $logger, $jsonHelper);
        $this->authSession = $authSession;
    }

    /**
     * Product view action
     *
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'controle/marketplace/product') !== false) {
            // Get initial data from request
            $categoryId = (int) $this->getRequest()->getParam('category', false);
            $productId = (int) $this->getRequest()->getParam('id');
            $specifyOptions = $this->getRequest()->getParam('options');

            // Prepare helper and params
            $params = new \Magento\Framework\DataObject();
            $params->setCategoryId($categoryId);
            $params->setSpecifyOptions($specifyOptions);

            // Render page
            try {
                $page = $this->resultPageFactory->create();
                $this->viewHelper = $this->_objectManager->create("Webkul\Marketplace\Helper\Product\View");
                $this->viewHelper->prepareAndRender($page, $productId, $this, $params);
                return $page;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                return $this->noProductRedirect();
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $resultForward = $this->resultForwardFactory->create();
                $resultForward->forward('noroute');
                return $resultForward;
            }
        } else {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }
}
