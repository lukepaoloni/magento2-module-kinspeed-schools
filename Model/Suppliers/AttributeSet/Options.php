<?php
    /**
     * Created by PhpStorm.
     * User: luke.paoloni
     * Date: 28/03/2018
     * Time: 14:45
     */

    namespace Kinspeed\Schools\Model\Suppliers\AttributeSet;

    use Kinspeed\Schools\Model\SuppliersFactory;
    use Magento\Framework\Data\OptionSourceInterface;

    class Options implements OptionSourceInterface
    {

        private $suppliersFactory;

        public function __construct(SuppliersFactory $suppliersFactory)
        {
            $this->suppliersFactory = $suppliersFactory;
        }

        /**
         * Return array of options as value-label pairs
         *
         * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
         */
        public function toOptionArray()
        {
            return $this->getOptionArray();
        }

        public function getOptionArray()
        {
            $suppliers = $this->suppliersFactory->create()->getCollection();
            $options     = [];
            foreach ($suppliers->getData() as $supplier) {
                $options[] = [
                    'label' => $supplier['supplier_name'],
                    'value' => $supplier['entity_id']
                ];
            }

            return $options;
        }
    }