<?php
namespace Mike\QRCode\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Action;
use Psr\Log\LoggerInterface;

class FillConsumer
{
    /**
     * @var Action
     */
    private Action $productAction;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * FillConsumer constructor.
     *
     * @param Action $action
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action $action,
        LoggerInterface $logger
    ) {
        $this->productAction = $action;
        $this->logger = $logger;
    }

    /**
     * @param ProductInterface $product
     * @return void
     */
    public function processMessage(ProductInterface $product): void
    {
        $this->logger->info($product->getId() . ' ' . $product->getName() . ' ' . $product->getStoreId());
        $this->productAction->updateAttributes([$product->getId()], ['name_qr' => $product->getName()], $product->getStoreId());
    }
}
