<?php

declare(strict_types=1);

namespace Bluethink\BestSeller\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Provider to fetch config value
 */
class Provider
{
    public const XML_PATH_MODULE_STATUS = 'best_seller_section/general/enable';

    public const XML_PATH_PRODUCT_TYPE = 'best_seller_section/general/list_mode';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Provider Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

   /**
    * Get Module Status from configuration
    *
    * @return null|string
    */
    public function getModuleStaus(): ?string
    {
        return $this->getStoreValue(self::XML_PATH_MODULE_STATUS);
    }

   /**
    * Get product type from configuration
    *
    * @return null|string
    */
    public function getProdutType(): ?string
    {
        return $this->getStoreValue(self::XML_PATH_PRODUCT_TYPE);
    }

    /**
     * Get store value from configuration
     *
     * @param string $configPath
     * @return null|string
     * @throws NoSuchEntityException
     */
    protected function getStoreValue(string $configPath): ?string
    {
        return $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }
}
