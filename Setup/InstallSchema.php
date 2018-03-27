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
    use Kinspeed\Schools\Setup\EavTablesSetupFactory;

    /**
     * @codeCoverageIgnore
     */
    class InstallSchema implements InstallSchemaInterface
    {
        /**
         * @var EavTablesSetupFactory
         */
        protected $eavTablesSetupFactory;

        /**
         * Init
         *
         * @internal param EavTablesSetupFactory $EavTablesSetupFactory
         */
        public function __construct(EavTablesSetupFactory $eavTablesSetupFactory)
        {
            $this->eavTablesSetupFactory = $eavTablesSetupFactory;
        }

        /**
         * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup
         * @param \Magento\Framework\Setup\ModuleContextInterface $context
         *
         * @return string|void
         */
        public function install(SchemaSetupInterface $setup, ModuleContextInterface $context
        ) //@codingStandardsIgnoreLine
        {
            $setup->startSetup();
            try {
                /**
                 * Create School Suppliers Table
                 */
                $this->createSchoolSuppliers($setup);
                /**
                 * Create School Types Table
                 */
                $this->createSchoolTypes($setup);
                /**
                 * Create Main Table
                 */
                $this->createSchool($setup);
                /**
                 * Create School Entities Table
                 */
                $this->createEntitesTable($setup);
            }
            catch (\Zend_Db_Exception $dbException) {
                $dbException->getMessage();
            }
            $setup->endSetup();
        }

        /**
         * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
         *
         * @throws \Zend_Db_Exception
         */
        private function createSchool($setup)
        {
            $tableName = SchoolSetup::ENTITY_TYPE_CODE . '_entity';
            $table = $setup->getConnection()
                           ->newTable($setup->getTable($tableName))
                           ->addColumn(
                               'entity_id',
                               Table::TYPE_INTEGER,
                               null,
                               ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                               'Entity ID'
                           );
            $table->addColumn(
                'is_active',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => true],
                'Is Active'
            )->addColumn(
                'school_type_idno',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'School Type ID'
            )->addForeignKey(
                $setup->getFkName($tableName, 'school_type_idno', 'kinspeed_schools_school_types', 'entity_id'),
                'school_type_id',
                'kinspeed_schools_school_types',
                'entity_id',
                Table::ACTION_CASCADE
            )->addColumn(
                'school_supplier_idno',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'unsigned' => true],
                'School Supplier ID'
            )->addForeignKey(
                $setup->getFkName($tableName, 'school_supplier_idno', 'kinspeed_schools_school_suppliers', 'entity_id'),
                'school_supplier_idno',
                'kinspeed_schools_school_suppliers',
                'entity_id',
                Table::ACTION_CASCADE
            )->addColumn(
                'franchisee_idno',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'School Franchisee ID'
            );
            $table->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Creation Time'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            )->setComment('Entity Table');
            $setup->getConnection()->createTable($table);
        }

        /**
         * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
         *
         * @throws \Zend_Db_Exception
         */
        private function createSchoolSuppliers($setup)
        {
            $tableName = 'kinspeed_schools_school_suppliers';
            $table     = $setup->getConnection()->newTable($setup->getTable($tableName));
            $table->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn(
                'supplier_name',
                Table::TYPE_TEXT,
                null,
                null,
                ['nullable' => false, 'unsigned' => true]
            );
            $setup->getConnection()->createTable($table);
        }

        /**
         * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
         *
         * @throws \Zend_Db_Exception
         */
        private function createSchoolTypes($setup)
        {
            $tableName = 'kinspeed_schools_school_types';
            $table     = $setup->getConnection()->newTable($setup->getTable($tableName));
            $table->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )->addColumn(
                'school_type',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'unsigned' => true],
                'School Type'
            )->addColumn(
                'vat_status',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Vat Status'
            );
            $setup->getConnection()->createTable($table);
        }


        /**
         * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
         */
        private function createEntitesTable($setup)
        {
            /** @var \Kinspeed\Schools\Setup\EavTablesSetup $eavTablesSetup */
            $eavTablesSetup = $this->eavTablesSetupFactory->create(['setup' => $setup]);
            $eavTablesSetup->createEavTables(SchoolSetup::ENTITY_TYPE_CODE);
        }
    }
