<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Genesisoft\MarketplaceExt\Preference\Magento\Elasticsearch\Model\ResourceModel\Fulltext\Collection;

use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\SearchResultApplierInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Data\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Magento\Framework\App\Request\Http;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

use Webkul\Marketplace\Helper\Data as MpHelper;
use Webkul\Marketplace\Model\Product as SellerProduct;
use Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory as MpProductCollection;

/**
 * Resolve specific attributes for search criteria.
 */
class SearchResultApplier implements SearchResultApplierInterface
{
    /**
     * @var Collection|\Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
     */
    private $collection;

    /**
     * @var SearchResultInterface
     */
    private $searchResult;

    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $currentPage;

    protected $collectionFactory;
    protected $request;
    protected $_objectManager;

    /**
     * @param Collection $collection
     * @param SearchResultInterface $searchResult
     * @param int $size
     * @param int $currentPage
     */
    public function __construct(
        Collection $collection,
        SearchResultInterface $searchResult,
        int $size,
        int $currentPage,
        ProductCollection $collectionFactory,
        Http $request,
        ObjectManagerInterface $objectManager,
        MpProductCollection $mpProductCollectionFactory
    ) {
        $this->collection = $collection;
        $this->searchResult = $searchResult;
        $this->size = $size;
        $this->currentPage = $currentPage;
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        $this->_objectManager = $objectManager;
        $this->_mpProductCollectionFactory = $mpProductCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $handle = $this->request->getFullActionName();
        if($handle == "marketplace_seller_collection"){
            $paramData = $this->request->getParams();
            $partner = $this->getProfileDetail();
            try {
                $sellerId = $partner->getSellerId();
            } catch (\Exception $e) {
                $sellerId = 0;
            }

            $querydata = $this->_mpProductCollectionFactory->create()
                ->addFieldToFilter('seller_id', $sellerId)
                ->addFieldToFilter('status', ['neq' => SellerProduct::STATUS_DISABLED])
                ->addFieldToSelect('mageproduct_id')
                ->setOrder('mageproduct_id');

            $productCollection = $this->collectionFactory->create();
            $productCollection->addAttributeToSelect('*');
            $productCollection->addAttributeToFilter('entity_id', ['in' => $querydata->getData()]);
            $productCollection->addAttributeToFilter('visibility', ['in' => [4]]);
            $productCollection->addAttributeToFilter('status', ['neq' => SellerProduct::STATUS_DISABLED]);
            $productCollection->addStoreFilter();

            $items = $productCollection->getItems();
            if (empty($items)) {
                $this->collection->getSelect()->where('NULL');
                return;
            }

            if($this->currentPage > 1){
                $offset = ($this->currentPage - 1) * $this->size;
            }else{
                $offset = 0;
            }
            $this->collection->getSelect()->limit($this->size, $offset);
        }else{
            if (empty($this->searchResult->getItems())) {
                $this->collection->getSelect()->where('NULL');
                return;
            }
            $items = $this->sliceItems($this->searchResult->getItems(), $this->size, $this->currentPage);
        }

        $ids = [];
        foreach ($items as $item) {
            $ids[] = (int)$item->getId();
        }
        $this->collection->getSelect()->where('e.entity_id IN (?)', $ids);
        $orderList = join(',', $ids);
        $this->collection->getSelect()->reset(\Magento\Framework\DB\Select::ORDER);
        $this->collection->getSelect()->order(new \Zend_Db_Expr("FIELD(e.entity_id,$orderList)"));
    }

    /**
     * Slice current items
     *
     * @param array $items
     * @param int $size
     * @param int $currentPage
     * @return array
     */
    private function sliceItems(array $items, int $size, int $currentPage): array
    {
        if ($size !== 0) {
            // Check that current page is in a range of allowed page numbers, based on items count and items per page,
            // than calculate offset for slicing items array.
            $totalPages = (int) ceil(count($items)/$size);
            $currentPage = min($currentPage, $totalPages);
            $offset = ($currentPage - 1) * $size;
            if ($offset < 0) {
                $offset = 0;
            }

            $items = array_slice($items, $offset, $size);
        }

        return $items;
    }

    /**
     * Get Seller Profile Details
     *
     * @return \Webkul\Marketplace\Model\Seller | bool
     */
    public function getProfileDetail()
    {
        $helper = $this->_objectManager->create('Webkul\Marketplace\Helper\Data');
        return $helper->getProfileDetail(MpHelper::URL_TYPE_COLLECTION);
    }
}
