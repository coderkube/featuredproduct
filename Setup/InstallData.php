<?php

namespace Coderkube\Featuredproduct\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class InstallData implements InstallDataInterface
{

        /**
         * EAV setup factory
         *
         * @var EavSetupFactory
         */
    private $eavSetupFactory;
        /**
         * Init
         *
         * @param EavSetupFactory $eavSetupFactory
         */

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
            $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

            /** @var EavSetup $eavSetup */

            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'is_profeature',
                [
                            'group' => 'General',
                            'type' => 'int',
                            'label' => 'Is Featured?',
                            'input' => 'boolean',
                            'source' => Boolean::class,
                            'required' => false,
                            'user_defined' => true,
                            'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                            'used_in_product_listing' => true,
                            'apply_to' => 'simple,configurable,virtual,bundle,downloadable,grouped',
                            'is_used_in_grid' => true,
                            'is_visible_in_grid' => false,
                            'is_filterable_in_grid' => false,
                            'used_in_product_listing' => true,
                            'visible' => true,
                    ]
            );
    }
}
