<?php

namespace Zs\Xautosku\Setup;

use Braintree\Exception;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Model\Config;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
    private $collectionFactory;
    private $attributeRepository;
    private $config;

    public function __construct(EavSetupFactory $eavSetupFactory,
                                CollectionFactory $collectionFactory,
                                AttributeRepositoryInterface $attributeRepository,
                                Config $config)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->collectionFactory = $collectionFactory;
        $this->attributeRepository = $attributeRepository;
        $this->config = $config;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        try{
            $entityTypeId = $this->config
                ->getEntityType(\Magento\Catalog\Api\Data\CategoryAttributeInterface::ENTITY_TYPE_CODE)
                ->getEntityTypeId();
            $this->attributeRepository->get($entityTypeId, 'sku_prefix');
        }catch (\Magento\Framework\Exception\NoSuchEntityException $e){
            $setup->startSetup();

            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'sku_prefix',
                [
                    'type' => 'text',
                    'label' => 'SKU Prefix',
                    'input' => 'text',
                    'required' => false,
                    'sort_order' => 4,
                    'backend' => '',
                    'frontend' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'wysiwyg_enabled' => true,
                    'is_html_allowed_on_front' => true,
                    'visible_on_front' => true,
                    'group' => 'General Information',
                ]
            );
            $setup->endSetup();
        }
    }
}