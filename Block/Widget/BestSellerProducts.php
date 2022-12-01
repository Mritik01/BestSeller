<?php

declare(strict_types=1);

namespace Bluethink\BestSeller\Block\Widget;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data;
use Magento\Widget\Block\BlockInterface;
use Bluethink\BestSeller\Model\Config\Source\Provider;

/**
 * Provide product collection
 */
class BestSellerProducts extends ListProduct implements BlockInterface
{
    public const BEST_SELLER_PRODUCT = 'best_seller';

    public const FEATURE_PRODUCT = 'feature';

    public const NEW_ARRIBALS_PRODUCT = 'new_arrivals';

    protected $_template = "widget/new.phtml";

    /**
     * @var BestSellersCollectionFactory
     */
    protected $_bestSellersCollectionFactory;

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Provider
     */
    protected $provider;

    /**
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param Data $urlHelper
     * @param CollectionFactory $productCollectionFactory
     * @param BestSellersCollectionFactory $bestSellersCollectionFactory
     * @param Provider $provider
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        Data $urlHelper,
        CollectionFactory $productCollectionFactory,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        Provider $provider,
        array $data = []
    ) {
        $this->_bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->provider = $provider;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }
   
    /**
     * Provides config data.
     *
     * @return object
     */
    public function getConfigData(): ?object
    {
        if ($this->provider->getProdutType() === self::BEST_SELLER_PRODUCT) {
            return $this->getBestSellerCollection();
        }
        if ($this->provider->getProdutType() === self::FEATURE_PRODUCT) {
            return $this->getFeatureProductCollection();
        }
        if ($this->provider->getProdutType() === self::NEW_ARRIBALS_PRODUCT) {
            return $this->getNewArrivalProductCollection();
        }
    }

    /**
     * Filter best seller product.
     *
     * @return mixed
     */
    public function getBestSellerCollection(): ?object
    {
        $productIds = [];
        $bestSellers = $this->_bestSellersCollectionFactory->create()
            ->setPeriod('month');
        foreach ($bestSellers as $product) {
            $productIds[] = $product->getProductId();
        }
        $collection = $this->_productCollectionFactory->create()->addIdFilter($productIds);
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('small_image')
            ->addStoreFilter($this->getStoreId())->setPageSize($this->getProductsCount());
        return $collection;
    }

    /**
     * Filter feature product.
     *
     * @return object
     */
    public function getFeatureProductCollection(): ?object
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('small_image')
            ->addStoreFilter($this->getStoreId())
            ->setPageSize($this->getProductsCount())
            ->addAttributeToFilter('feature_product', '1');
        return $collection;
    }

    /**
     * Filter new arrival product.
     *
     * @return object
     */
    public function getNewArrivalProductCollection(): ?object
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('small_image');
        $now  = date('Y-m-d H:i:s');
        $collection->addAttributeToFilter('news_from_date', ['lteq' => $now])
                   ->addAttributeToFilter('news_to_date', ['gteq' => $now]);
        return $collection;
    }

    /**
     * Provide store id.
     *
     * @return string
     */
    public function getStoreId(): ?string
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Get lable of product.
     *
     * @return string
     */
    public function getProductHeading(): ?string
    {
        return strtoupper(str_replace('_', ' ', $this->provider->getProdutType()));
    }

    /**
     * Get lable of product.
     *
     * @return string
     */
    public function getModuleStaus(): ?string
    {
        return $this->provider->getModuleStaus();
    }
}