<?php

namespace Mike\QRCode\ViewModel\Product;

use Magento\Catalog\Helper\Data;
use Magento\Framework\App\Cache;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Mike\QRCode\Service\QRApiService;
use Magento\Framework\App\Cache\State;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Product QR Code view model.
 */
class QRCode extends DataObject implements ArgumentInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'QRCODE_PRODUCT';

    /**
     * Cache id
     */
    const CACHE_ID = 'qrcode';

    /**
     * Cache lifetime
     */
    const CACHE_LIFETIME = 86400;

    /**
     * @var Data
     */
    private Data $catalogData;

    /**
     * @var QRApiService
     */
    private QRApiService $apiService;

    /**
     * @var Cache
     */
    private Cache $cache;

    /**
     * @var State
     */
    private State $cacheState;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param Data $catalogData
     * @param QRApiService $QRApiService
     * @param Cache $cache
     * @param State $cacheState
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Data $catalogData,
        QRApiService $QRApiService,
        Cache $cache,
        State $cacheState,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct();

        $this->catalogData = $catalogData;
        $this->apiService = $QRApiService;
        $this->cache = $cache;
        $this->cacheState = $cacheState;
        $this->storeManager = $storeManager;
    }

    /**
     * Returns product name qr code.
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getQRCode(): string
    {
        if ($this->catalogData->getProduct()) {
            $sku = $this->catalogData->getProduct()->getSku();
            $cacheId = $this->getCacheId($sku);
            if($cache = $this->loadCache($cacheId)){
                return $cache;
            }

            $productName = $this->catalogData->getProduct()->getNameQr();
            if ($productName) {
                $result = $this->apiService->execute($productName);
                if ($result) {
                    $base64Image = 'data:image/png;base64, ' . $result;
                    $this->saveCache($base64Image, $cacheId);
                    return $base64Image;
                }
            }
        }
        return '';
    }

    /**
     * @param $method
     * @param array $vars
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCacheId($method, array $vars = array()): string
    {
        return base64_encode($this->storeManager->getStore()->getId() . self::CACHE_ID . $method . implode('', $vars));
    }

    /**
     * @param $cacheId
     * @return string
     */
    public function loadCache($cacheId): string
    {
        if ($this->cacheState->isEnabled(self::CACHE_ID)) {
            return $this->cache->load($cacheId);
        }

        return false;
    }

    /**
     * @param $data
     * @param $cacheId
     * @param int $cacheLifetime
     * @return bool
     */
    public function saveCache($data, $cacheId, int $cacheLifetime = self::CACHE_LIFETIME): bool
    {
        if ($this->cacheState->isEnabled(self::CACHE_ID)) {
            $this->cache->save($data, $cacheId, array(self::CACHE_TAG), $cacheLifetime);
            return true;
        }
        return false;
    }
}
