<?php
/**
 * InstallData
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */

namespace Kinspeed\Schools\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * School setup factory
     *
     * @var SchoolSetupFactory
     */
    protected $schoolSetupFactory;
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param SchoolSetupFactory $schoolSetupFactory
     */
    public function __construct(SchoolSetupFactory $schoolSetupFactory, EavSetupFactory $eavSetupFactory)
    {
        $this->schoolSetupFactory = $schoolSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) //@codingStandardsIgnoreLine
    {
        /** @var SchoolSetup $schoolSetup */
        $schoolSetup = $this->schoolSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $schoolSetup->installEntities();
        $entities = $schoolSetup->getDefaultEntities();
        foreach ($entities as $entityName => $entity) {
            $schoolSetup->addEntityType($entityName, $entity);
        }

        $setup->endSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'linked_school', [
            'type'     => 'int',
            'label'    => 'Linked School',
            'input'    => 'text',
            'visible'  => false,
            'default'  => null,
            'required' => false,
            'unique' => true,
            'global'   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL
        ]);
        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'linked_school', [
            'type'     => 'int',
            'label'    => 'Linked School',
            'input'    => 'text',
            'visible'  => false,
            'default'  => null,
            'unique' => true,
            'required' => false,
            'global'   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL
        ]);

    }
}
