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
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context) //@codingStandardsIgnoreLine
    {
        $setup->startSetup();

        if ($setup->tableExists('kinspeed_schools_school')) {
            $setup->getConnection()->dropTable($setup->getTable('kinspeed_schools_school'));
            $setup->getConnection()->dropTable($setup->getTable('kinspeed_schools_school_suppliers'));
            $setup->getConnection()->dropTable($setup->getTable('kinspeed_schools_school_types'));
        }

        $setup->endSetup();
    }
}
