<?php

/**
 * Uninstall.php
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */
namespace Kinspeed\Schools\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @var array
     */
    protected $tablesToUninstall = [
        SchoolSetup::ENTITY_TYPE_CODE . '_entity',
        SchoolSetup::ENTITY_TYPE_CODE . '_eav_attribute',
        SchoolSetup::ENTITY_TYPE_CODE . '_entity_datetime',
        SchoolSetup::ENTITY_TYPE_CODE . '_entity_decimal',
        SchoolSetup::ENTITY_TYPE_CODE . '_entity_int',
        SchoolSetup::ENTITY_TYPE_CODE . '_entity_text',
        SchoolSetup::ENTITY_TYPE_CODE . '_entity_varchar',
        'kinspeed_schools_types',
        'kinspeed_schools_suppliers'
    ];

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context) //@codingStandardsIgnoreLine
    {
        $setup->startSetup();

        foreach ($this->tablesToUninstall as $table) {
            if ($setup->tableExists($table)) {
                $setup->getConnection()->dropTable($setup->getTable($table));
            }
        }

        $setup->endSetup();
    }
}
