<?php
namespace Mike\QRCode\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddProductAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $_moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $_eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->_moduleDataSetup = $moduleDataSetup;
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $this->_moduleDataSetup]);

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'name_qr', [
            'type' => 'varchar',
            'backend' => '',
            'frontend' => '',
            'label' => 'Name QR',
            'input' => 'text',
            'class' => '',
            'source' => '',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'used_in_product_listing' => false,
            'unique' => false,
        ]);

        $ATTRIBUTE_GROUP = 'General';
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $allAttributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);
        foreach ($allAttributeSetIds as $attributeSetId) {
            $groupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $ATTRIBUTE_GROUP);
            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $groupId,
                'name_qr'
            );
        }
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public static function getVersion(): string
    {
        return '1.0.0';
    }
}
