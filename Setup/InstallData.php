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

    /**
     * Init
     *
     * @param SchoolSetupFactory $schoolSetupFactory
     */
    public function __construct(SchoolSetupFactory $schoolSetupFactory)
    {
        $this->schoolSetupFactory = $schoolSetupFactory;
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
    }
}
