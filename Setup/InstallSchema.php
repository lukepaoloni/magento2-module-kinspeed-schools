<?php
/**
 * installSchema.php
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */
namespace Kinspeed\Schools\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) //@codingStandardsIgnoreLine
    {
        $setup->startSetup();

        /**
         * Create table 'kinspeed_schools_school'
         */
        try {
            $table = $setup->getConnection()->newTable(
                $setup->getTable( 'kinspeed_schools_school' )
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                NULL,
                [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true ],
                'School ID'
            )->addColumn(
                'school_name',
                Table::TYPE_TEXT,
                500,
                [ 'nullable' => false ],
                'School Name'
            )->addColumn(
                'school_type_idno',
                Table::TYPE_INTEGER,
                NULL,
                [ 'nullable' => true, 'unsigned' => true ],
                'School Type ID'
            )->addForeignKey(
                $setup->getFkName('kinspeed_schools_school', 'school_type_idno',
                                  'kinspeed_schools_school_types', 'entity_id'),
                'school_type_idno',
                $setup->getTable('kinspeed_schools_school_types'),
                'entity_id',
                Table::ACTION_CASCADE
            )->addColumn(
                'school_supplier_idno',
                Table::TYPE_INTEGER,
                NULL,
                [ 'nullable' => true, 'unsigned' => true ],
                'School Supplier ID'
            )->addForeignKey(
                $setup->getFkName('kinspeed_schools_school', 'school_supplier_idno',
                                  'kinspeed_schools_school_suppliers', 'entity_id'),
                'school_supplier_idno',
                $setup->getTable('kinspeed_schools_school_suppliers'),
                'entity_id',
                Table::ACTION_CASCADE
            )->addColumn(
                'title',
                Table::TYPE_TEXT,
                50,
                [ 'nullable' => true ],
                'Name Title Prefix'
            )->addColumn(
                'first_name',
                Table::TYPE_TEXT,
                500,
                [ 'nullable' => true ],
                'School Customer First Name'
            )->addColumn(
                'last_name',
                Table::TYPE_TEXT,
                500,
                [ 'nullable' => true ],
                'School Customer Surname'
            )->addColumn(
                'is_active',
                Table::TYPE_BOOLEAN,
                null,
                [ 'nullable' => false, 'unsigned' => true, 'default' => true ],
                'School Customer Surname'
            )->addColumn(
                'job_title',
                Table::TYPE_TEXT,
                255,
                [ 'nullable' => true ],
                'School Customer Job Title'
            )->addColumn(
                'address_1',
                Table::TYPE_TEXT,
                500,
                [ 'nullable' => true ],
                'School Address Line 1'
            )->addColumn(
                'address_2',
                Table::TYPE_TEXT,
                500,
                [ 'nullable' => true ],
                'School Address Line 2'
            )->addColumn(
                'address_3',
                Table::TYPE_TEXT,
                500,
                [ 'nullable' => true ],
                'School Address Line 3'
            )->addColumn(
                'town',
                Table::TYPE_TEXT,
                500,
                [ 'nullable' => true ],
                'School Town'
            )->addColumn(
                'postcode',
                Table::TYPE_TEXT,
                50,
                [ 'nullable' => true ],
                'School Postcode'
            )->addColumn(
                'mobile',
                Table::TYPE_TEXT,
                200,
                [ 'nullable' => true ],
                'School Customer Mobile'
            )->addColumn(
                'tel',
                Table::TYPE_TEXT,
                50,
                [ 'nullable' => true ],
                'School Telephone'
            )->addColumn(
                'fax',
                Table::TYPE_TEXT,
                50,
                [ 'nullable' => true ],
                'School Fax'
            )->addColumn(
                'email',
                Table::TYPE_TEXT,
                200,
                [ 'nullable' => true ],
                'School Email'
            )->addColumn(
                'logo',
                Table::TYPE_TEXT,
                500,
                [ 'nullable' => true ],
                'School Logo'
            )->addColumn(
                'folder_name',
                Table::TYPE_TEXT,
                500,
                [ 'nullable' => true ],
                'School Folder Name'
            )->addColumn(
                'franchisee_idno',
                Table::TYPE_INTEGER,
                NULL,
                [ 'nullable' => true, 'unsigned' => true ],
                'School Franchisee ID'
            )->addColumn(
                'enable_parent_order',
                Table::TYPE_BOOLEAN,
                NULL,
                [ 'nullable' => false, 'unsigned' => true, 'default' => true ],
                'Enable Parent Orders'
            )->addColumn(
                'enable_school_fran_order',
                Table::TYPE_BOOLEAN,
                NULL,
                [ 'nullable' => false, 'unsigned' => true, 'default' => true ],
                'Enable School Franchise Orders'
            )->addColumn(
                'show_school',
                Table::TYPE_BOOLEAN,
                NULL,
                [ 'nullable' => false, 'unsigned' => true, 'default' => true ],
                'Show School'
            )->addColumn(
                'invoice_email',
                Table::TYPE_TEXT,
                NULL,
                [ 'nullable' => true ],
                'Invoice Email'
            )->addColumn(
                'account_type',
                Table::TYPE_INTEGER,
                NULL,
                [ 'nullable' => false, 'unsigned' => true, 'default' => '1' ],
                'Account Type'
            )->addColumn(
                'hp_total',
                Table::TYPE_INTEGER,
                NULL,
                [ 'nullable' => false, 'unsigned' => true, 'default' => '0' ],
                'HP Total'
            )->addColumn(
                'website_address',
                Table::TYPE_TEXT,
                500,
                [ 'nullable' => true ],
                'Schools Website Address'
            )->addColumn(
                'notes',
                Table::TYPE_TEXT,
                5000,
                [ 'nullable' => true ],
                'School Notes'
            )->addColumn(
                'enable_bulk_delivery',
                Table::TYPE_BOOLEAN,
                NULL,
                [ 'nullable' => false, 'unsigned' => true, 'default' => true ],
                'Enable Bulk Delivery'
            )->addColumn(
                'enable_collection',
                Table::TYPE_BOOLEAN,
                NULL,
                [ 'nullable' => false, 'unsigned' => true, 'default' => true ],
                'Enable Collection'
            )->addColumn(
                'external_ref',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'External Reference'
            )->addColumn(
                'consolidation_day',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Consolidation Day'
            )->addColumn(
                'holiday_dates',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Holiday Dates'
            )->addColumn(
                'pupils_on_roll',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'unsigned' => true],
                'Pupils On Roll'
            )->addColumn(
                'enable_ppp_school',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => false],
                'Enable PPP School'
            )->addColumn(
                'enable_ppp_parent',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => false],
                'Enable PPP Parent'
            )->setComment(
                'School Table'
            );
            // Add more columns here
            $setup->getConnection()->createTable($table);
            
            $schoolSupplierTable = $setup->getConnection()->newTable(
                $setup->getTable( 'kinspeed_schools_school_suppliers' )
            )->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true, 'primary' => true],
                    'School Supplier ID'
            )->addColumn(
                'supplier_name',
                Table::TYPE_TEXT,
                500,
                ['nullable' => false],
                'School Supplier Name'
            )->setComment('School Suppliers');
            $setup->getConnection()->createTable($schoolSupplierTable);
    
            $schoolTypesTable = $setup->getConnection()->newTable(
                $setup->getTable( 'kinspeed_schools_school_types' )
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'primary' => true],
                'School Types ID'
            )->addColumn(
                'school_type',
                Table::TYPE_TEXT,
                500,
                ['nullable' => false],
                'School Type'
            )->addColumn(
                'vat_status',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'default', '0'],
                'VAT Status'
            )->setComment('School Types');
            $setup->getConnection()->createTable($schoolTypesTable);
            $setup->endSetup();
        }
        catch ( \Zend_Db_Exception $e ) {
            $e->getMessage();
        }
        
    }
}
