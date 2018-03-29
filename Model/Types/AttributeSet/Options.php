<?php
    /**
     * Created by PhpStorm.
     * User: luke.paoloni
     * Date: 28/03/2018
     * Time: 14:45
     */

    namespace Kinspeed\Schools\Model\Types\AttributeSet;

    use Kinspeed\Schools\Model\TypesFactory;
    use Magento\Framework\Data\OptionSourceInterface;

    class Options implements OptionSourceInterface
    {

        private $typesFactory;

        public function __construct(TypesFactory $typesFactory)
        {
            $this->typesFactory = $typesFactory;
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
            $types = $this->typesFactory->create()->getCollection();
            $options     = [];
            foreach ($types->getData() as $type) {
                $options[] = [
                    'label' => $type['school_type'],
                    'value' => $type['entity_id']
                ];
            }

            return $options;
        }
    }