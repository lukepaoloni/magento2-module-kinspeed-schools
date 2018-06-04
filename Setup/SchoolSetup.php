<?php
    /**
     * SchoolSetup
     *
     * @copyright Copyright © 2017 Kinspeed. All rights reserved.
     * @author    luke.paoloni@kinspeed.com
     */

    namespace Kinspeed\Schools\Setup;

    use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
    use Magento\Eav\Setup\EavSetup;

    /**
     * @codeCoverageIgnore
     */
    class SchoolSetup extends EavSetup
    {
        /**
         * Entity type for School EAV attributes
         */
        const ENTITY_TYPE_CODE = 'kinspeed_school';

        /**
         * Retrieve Entity Attributes
         *
         * @return array
         * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
         */
        protected function getAttributes()
        {
            $attributes = [];
            $this->getSchoolsEntity($attributes);
            $this->getCustomersEntity($attributes);
            $this->getSettingsEntity($attributes);
            return $attributes;
        }

        private function getSchoolsEntity(&$attributes)
        {
            /*
             * School Data
             */
            $attributes['is_active'] = [
                'type'       => 'int',
                'label'      => 'Is Active',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => true, //true/false
                'sort_order' => 0,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'School',
            ];
            $attributes['school_name'] = [
                'type'           => 'varchar',
                'label'          => 'School Name',
                'input'          => 'text',
                'required'       => true, //true/false
                'sort_order'     => 1,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'School',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            $attributes['address_1']   = [
                'type'           => 'varchar',
                'label'          => 'Address 1',
                'input'          => 'text',
                'required'       => true, //true/false
                'sort_order'     => 2,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'School',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            
            $attributes['address_2']   = [
                'type'           => 'varchar',
                'label'          => 'Address 2',
                'input'          => 'text',
                'required'       => false, //true/false
                'sort_order'     => 3,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'School',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            $attributes['elastic_search_id'] = [
                'type' => 'varchar',
                'label' => 'Elastic_search_id',
                'input' => 'text',
                'required' => false, //true/false
                'sort_order' => 999,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                //'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            $attributes['address_3']   = [
                'type'           => 'varchar',
                'label'          => 'Address 3',
                'input'          => 'text',
                'required'       => false, //true/false
                'sort_order'     => 4,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'School',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            $attributes['town']        = [
                'type'           => 'varchar',
                'label'          => 'Town',
                'input'          => 'text',
                'required'       => true, //true/false
                'sort_order'     => 5,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'School',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            $attributes['country_code'] = [
                'type' => 'varchar',
                'label' => 'Country',
                'input' => 'select',
                'required' => true, //true/false
                'sort_order' => 6,
                'source'     => 'Magento\Directory\Model\Config\Source\Country',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'School',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:3;s:15:"min_text_length";i:1;}',
                'default' => 'GB'
            ];

            $attributes['longitude'] = [
                'type' => 'varchar',
                'label' => 'Longitude',
                'input' => 'text',
                'required' => false, //true/false
                'sort_order' => 999,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General'
            ];

            $attributes['latitude'] = [
                'type' => 'varchar',
                'label' => 'Latitude',
                'input' => 'text',
                'required' => false, //true/false
                'sort_order' => 999,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General'
            ];

            $attributes['county_id'] = [
                'type'           => 'varchar',
                'label'          => 'County',
                'input'          => 'select',
                'required'       => false, //true/false
                'sort_order'     => 4,
                'source'         => 'Kinspeed\Address\Model\County\AttributeSet\Options',
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'School',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            $attributes['school_type_idno']     = [
                'type'       => 'int',
                'label'      => 'School Type',
                'input'      => 'select',
                'source'     => 'Kinspeed\Schools\Model\Type\AttributeSet\Options',
                'required'   => false, //true/false
                'sort_order' => 99,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'School',
            ];
            $attributes['school_supplier_idno'] = [
                'type'       => 'int',
                'label'      => 'School Supplier',
                'input'      => 'select',
                'source'     => 'Kinspeed\Schools\Model\Supplier\AttributeSet\Options',
                'required'   => false, //true/false
                'sort_order' => 99,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'School',
            ];
            $attributes['postcode'] = [
                'type'           => 'varchar',
                'label'          => 'Postcode',
                'input'          => 'text',
                'required'       => true, //true/false
                'sort_order'     => 5,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'General',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:30;s:15:"min_text_length";i:1;}',
            ];
            $attributes['tel']             = [
                'type'           => 'varchar',
                'label'          => 'Tel',
                'input'          => 'text',
                'required'       => true, //true/false
                'sort_order'     => 5,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'School',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            $attributes['folder_name']     = [
                'type'           => 'varchar',
                'label'          => 'Folder',
                'input'          => 'text',
                'required'       => false, //true/false
                'sort_order'     => 7,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'School',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            $attributes['website_address'] = [
                'type'           => 'varchar',
                'label'          => 'Website Address',
                'input'          => 'text',
                'required'       => false, //true/false
                'sort_order'     => 8,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'School',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            $attributes['pupils_on_roll']  = [
                'type'       => 'int',
                'label'      => 'Pupils On roll',
                'input'      => 'text',
                'required'   => false, //true/false
                'sort_order' => 9,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'School'
            ];
            $attributes['logo']            = [
                'type'       => 'varchar',
                'label'      => 'Logo',
                'input'      => 'image',
                'backend'    => 'Kinspeed\Schools\Model\School\Attribute\Backend\Logo',
                'required'   => false,
                'sort_order' => 99,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'General',
            ];
            
            $attributes['registered_amount_interest'] = [
                'type' => 'int',
                'label' => 'Registered Amount of Interest',
                'input' => 'text',
                'required' => false, //true/false
                'sort_order' => 9999,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                //'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
        }

        private function getCustomersEntity(&$attributes)
        {
            /*
             * Customer Data
             */
            $attributes['first_name'] = [
                'type'           => 'varchar',
                'label'          => 'First Name',
                'input'          => 'text',
                'required'       => false, //true/false
                'sort_order'     => 2,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'Customer',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            $attributes['last_name'] = [
                'type'           => 'varchar',
                'label'          => 'Surname',
                'input'          => 'text',
                'required'       => false, //true/false
                'sort_order'     => 3,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'Customer',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            $attributes['job_title'] = [
                'type'           => 'varchar',
                'label'          => 'Job Title',
                'input'          => 'text',
                'required'       => false, //true/false
                'sort_order'     => 4,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'Customer',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            $attributes['email']     = [
                'type'           => 'varchar',
                'label'          => 'Email',
                'input'          => 'text',
                'required'       => false, //true/false
                'sort_order'     => 5,
                'global'         => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'          => 'Customer',
                'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
            ];
            $attributes['notes']     = [
                'type'            => 'text',
                'label'           => 'Notes',
                'input'           => 'textarea',
                'required'        => false, //true/false
                'sort_order'      => 6,
                'global'          => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'           => 'Customer',
                'wysiwyg_enabled' => true,
            ];
        }

        private function getSettingsEntity(&$attributes)
        {
            /*
             * School Settings
             */
            // $attributes['linked_category']           = [
            //     'type'       => 'int',
            //     'label'      => 'Linked Category',
            //     'input'      => 'input',
            //     'required'   => false, //true/false
            //     'visible'    => false,
            //     'sort_order' => 999,
            //     'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
            //     'group'      => 'Settings',
            // ];
            $attributes['active_customer']           = [
                'type'       => 'int',
                'label'      => 'Active Customer',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => false, //true/false
                'sort_order' => 999,
                'default'    => false,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'Settings',
            ];
            $attributes['enable_parent_order']       = [
                'type'       => 'int',
                'label'      => 'Enable Parent Order',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => false, //true/false
                'sort_order' => 1,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'Settings',
            ];
            $attributes['enable_school_fran_order']  = [
                'type'       => 'int',
                'label'      => 'Enable School Franchisee Order',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => false, //true/false
                'sort_order' => 2,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'Settings',
            ];
            $attributes['charge_postage']            = [
                'type'       => 'int',
                'label'      => 'Charge Postage',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => false, //true/false
                'sort_order' => 3,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'Settings',
            ];
            $attributes['charge_postage_school']     = [
                'type'       => 'int',
                'label'      => 'Charge Postage School',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => false, //true/false
                'sort_order' => 4,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'Settings',
            ];
            $attributes['charge_postage_franchisee'] = [
                'type'       => 'int',
                'label'      => 'Charge Postage School',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => false, //true/false
                'sort_order' => 5,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'Settings',
            ];
            $attributes['show_school']               = [
                'type'       => 'int',
                'label'      => 'Show School',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => false, //true/false
                'sort_order' => 6,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'Settings',
            ];
            $attributes['enable_bulk_delivery']      = [
                'type'       => 'int',
                'label'      => 'Enable Bulk Delivery',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => true, //true/false
                'sort_order' => 7,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'Settings',
            ];
            $attributes['enable_bulk_discounts']     = [
                'type'       => 'int',
                'label'      => 'Enable Bulk Discounts',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => true, //true/false
                'sort_order' => 8,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'Settings',
            ];
            $attributes['enable_bulk_address']       = [
                'type'       => 'int',
                'label'      => 'Enable Bulk Address',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => false, //true/false
                'sort_order' => 9,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'Settings',
            ];
            $attributes['enable_dts_school'] = [
                'type' => 'int',
                'label' => 'Enable DTS School',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false, //true/false
                'sort_order' => 9,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
            ];
            $attributes['enable_ppp_school']         = [
                'type'       => 'int',
                'label'      => 'Enable PPP School',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => false, //true/false
                'sort_order' => 10,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'Settings',
            ];
            $attributes['enable_ppp_parent']         = [
                'type'       => 'int',
                'label'      => 'Enable PPP Parent',
                'input'      => 'select',
                'source'     => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required'   => false, //true/false
                'sort_order' => 11,
                'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'      => 'Settings',
            ];
        }

        /**
         * Retrieve default entities: school
         *
         * @return array
         */
        public function getDefaultEntities()
        {
            $entities = [
                self::ENTITY_TYPE_CODE => [
                    'entity_model'                => 'Kinspeed\Schools\Model\ResourceModel\School',
                    'attribute_model'             => 'Kinspeed\Schools\Model\ResourceModel\Eav\Attribute',
                    'table'                       => self::ENTITY_TYPE_CODE . '_entity',
                    'increment_model'             => null,
                    'additional_attribute_table'  => self::ENTITY_TYPE_CODE . '_eav_attribute',
                    'entity_attribute_collection' => 'Kinspeed\Schools\Model\ResourceModel\Attribute\Collection',
                    'attributes'                  => $this->getAttributes()
                ]
            ];

            return $entities;
        }
    }
