<?php
/**
 * InstallData
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */

namespace Kinspeed\Schools\Setup;

use Kinspeed\Schools\Model\School;
use Magento\Catalog\Model\Category;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;

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
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * Init
     *
     * @param SchoolSetupFactory                 $schoolSetupFactory
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\Config          $eavConfig
     */
    public function __construct(
        SchoolSetupFactory $schoolSetupFactory,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    )
    {
        $this->schoolSetupFactory = $schoolSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
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
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup->addAttribute(Category::ENTITY, School::LINKED_SCHOOL, [
            'type'     => 'int',
            'label'    => 'School',
            'input'    => 'select',
            'visible'  => false,
            'default'  => null,
            'required' => false,
            'system' => 0,
            'unique' => true,
            'source' => 'Kinspeed\Schools\Model\School\AttributeSet\EavOptions',
            'global'   => ScopedAttributeInterface::SCOPE_GLOBAL
        ]);
        $eavSetup->addAttribute(
            Customer::ENTITY,
            School::LINKED_SCHOOL,
            [
                'type' => 'int',
                'label' => 'School',
                'input'    => 'select',
                'visible'  => false,
                'required' => false,
                'unique' => true,
                'system' => 0,
                'source' => 'Kinspeed\Schools\Model\School\AttributeSet\EavOptions',
                'global'   => ScopedAttributeInterface::SCOPE_GLOBAL
            ]
        );

        try {
            $used_in_forms[]="adminhtml_customer";
            $schoolAttribute = $this->eavConfig->getAttribute(Customer::ENTITY, School::LINKED_SCHOOL);
            $schoolAttribute->setData(
                'used_in_forms',
                $used_in_forms
            )->setData('is_used_for_customer_segment', true)
                            ->setData('is_system', 0)
                            ->setData('is_user_defined', 1)
                            ->setData('is_visible', 1);
            $schoolAttribute->save();
        }
        catch (\Exception $e) {
            $e->getMessage();
        }
    }
}
