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
         * {@inheritdoc}
         * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
         * @SuppressWarnings(PHPMD.UnusedFormalParameter)
         */
        public function install(SchemaSetupInterface $setup, ModuleContextInterface $context
        ) //@codingStandardsIgnoreLine
        {
            $setup->startSetup();
            try {
                /**
                 * Create Main Table
                 */
                $this->createSchoolsMainTable($setup);
                /**
                 * Create School Suppliers Table
                 */
                $this->createSchoolSuppliers($setup);
                /**
                 * Create School Types Table
                 */
                $this->createSchoolTypes($setup);
                /**
                 * Create School Interest Table
                 */
                $this->createSchoolInterests($setup);
            }
            catch (\Zend_Db_Exception $e) {
                return $e->getMessage();
            }
            /** @var \Kinspeed\Schools\Setup\EavTablesSetup $eavTablesSetup */
            $eavTablesSetup = $this->eavTablesSetupFactory->create(['setup' => $setup]);
            $eavTablesSetup->createEavTables(SchoolSetup::ENTITY_TYPE_CODE);
            $setup->endSetup();
        }

        /**
         * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
         *
         * @throws \Zend_Db_Exception
         */
        private function createSchoolsMainTable($setup)
        {
            $tableName = SchoolSetup::ENTITY_TYPE_CODE . '_entity';
            $table     = $setup->getConnection()
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
        private function createSchoolInterests($setup)
        {
            $tableName = SchoolSetup::ENTITY_TYPE_CODE . 'register_interests';
            $table     = $setup->getConnection()
                               ->newTable($setup->getTable($tableName))
                               ->addColumn(
                                   'entity_id',
                                   Table::TYPE_INTEGER,
                                   null,
                                   ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                                   'Entity ID'
                               );
            $table->addColumn(
                'notes',
                Table::TYPE_TEXT,
                1000,
                ['nullable' => true],
                'Notes'
            )->addColumn(
                'school_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true]
            )->addForeignKey(
                $setup->getFkName($tableName, 'school_id', 'kinspeed_schools_entity', 'entity_id'),
                'school_id',
                'kinspeed_schools_entity',
                'entity_id',
                Table::ACTION_CASCADE
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
        private function createSchool($setup)
        {
            $tableName = SchoolSetup::ENTITY_TYPE_CODE . '_entity';
            $table     = $setup->getConnection()
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
                $setup->getFkName($tableName, 'school_type_idno', 'kinspeed_schools_types', 'entity_id'),
                'school_type_id',
                'kinspeed_schools_types',
                'entity_id',
                Table::ACTION_CASCADE
            )->addColumn(
                'school_supplier_idno',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'unsigned' => true],
                'School Supplier ID'
            )->addForeignKey(
                $setup->getFkName($tableName, 'school_supplier_idno', 'kinspeed_schools_suppliers', 'entity_id'),
                'school_supplier_idno',
                'kinspeed_schools_suppliers',
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
            $tableName = 'kinspeed_schools_suppliers';
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
                ['nullable' => false, 'unsigned' => true],
                'Supplier Name'
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
            $tableName = 'kinspeed_schools_types';
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
    }
