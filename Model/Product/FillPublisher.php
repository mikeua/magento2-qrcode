<?php

namespace Mike\QRCode\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\MessageQueue\PublisherInterface;

class FillPublisher
{
    /**
     * Topic name
     */
    const TOPIC_NAME = 'mike.qrcode.fill';

    /**
     * @var PublisherInterface
     */
    private PublisherInterface $publisher;

    /**
     * @param PublisherInterface $publisher
     */
    public function __construct(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @param ProductInterface $product
     * @return void
     */
    public function execute(ProductInterface $product): void
    {
        $this->publisher->publish(self::TOPIC_NAME, $product);
    }
}
