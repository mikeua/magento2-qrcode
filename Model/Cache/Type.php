<?php

namespace Mike\QRCode\Model\Cache;

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;

class Type extends TagScope
{
    /**
     * Cache type
     */
    const TYPE_IDENTIFIER = 'qrcode';

    /**
     * Cache tag
     */
    const CACHE_TAG = 'QRCODE_PRODUCT';

    /**
     * @param FrontendPool $cacheFrontendPool
     */
    public function __construct(FrontendPool $cacheFrontendPool)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
}
