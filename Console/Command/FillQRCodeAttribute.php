<?php

namespace Mike\QRCode\Console\Command;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Mike\QRCode\Model\Product\FillPublisher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FillQRCodeAttribute extends Command
{
    /** Config number of products to process */
    const COUNT_CONFIG = "catalog/mike_qr_code/count";

    private const COUNT = 'count';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var FillPublisher
     */
    private FillPublisher $qrcodeFillPublisher;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @param string $name The name of the command; passing null means it must be set in configure()
     * @param ScopeConfigInterface $scopeConfig
     *
     * @throws LogicException When the command name is empty
     */
    public function __construct(
        string $name,
        ScopeConfigInterface $scopeConfig,
        FillPublisher $qrcodeFillPublisher,
        CollectionFactory $collectionFactory
    )
    {
        parent::__construct($name);

        $this->scopeConfig = $scopeConfig;
        $this->qrcodeFillPublisher = $qrcodeFillPublisher;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addOption(
            self::COUNT,
            null,
            InputOption::VALUE_REQUIRED,
            'Count'
        );

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $this->scopeConfig->getValue(self::COUNT_CONFIG);
        $inputCount =  $input->getOption(self::COUNT);
        $exitCode = 0;

        if ($inputCount) {
            $count = $inputCount;
        }

        $output->writeln('<info>Provided product count is `' . $count . '`</info>');
        try {

            $collection = $this->collectionFactory->create();
            $collection->addFieldToSelect('name');
            $collection->getSelect()->limit($count);

            foreach ($collection as $item) {
                $this->qrcodeFillPublisher->execute($item);
                $output->writeln("<info>{$item->getId()} {$item->getName()} {$item->getStoreId()}</info>");
            }
        } catch (\Exception $e) {
            $output->writeln(sprintf(
                '<error>%s</error>',
                $e->getMessage()
            ));
            $exitCode = 1;
        }
        return $exitCode;
    }
}
