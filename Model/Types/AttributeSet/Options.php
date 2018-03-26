<?php
    /**
     * Options
     *
     * @copyright Copyright © 2018 Kinspeed Ltd. All rights reserved.
     * @author    luke.paoloni@kinspeed.com
     */
    
    
    namespace Kinspeed\Schools\Model\Types\AttributeSet;
    
    use Magento\Framework\Data\OptionSourceInterface;
    use Kinspeed\Schools\Model\TypesFactory;
    use Kinspeed\Schools\Model\ResourceModel\Types;

    class Options implements OptionSourceInterface
    {
        /**
         * @var null|array
         */
        protected $options;
        /**
         * @var \Kinspeed\Schools\Model\TypesFactory
         */
        private $typesFactory;
        /**
         * @var \Kinspeed\Schools\Model\ResourceModel\Types
         */
        private $types;
    
        /**
         * @param \Kinspeed\Schools\Model\TypesFactory        $typesFactory
         * @param \Kinspeed\Schools\Model\ResourceModel\Types $types
         */
        public function __construct(
            TypesFactory $typesFactory,
            Types $types
        ) {
            $this->typesFactory = $typesFactory;
            $this->types = $types;
        }
    
        /**
         * @return array|null
         */
        public function toOptionArray()
        {
            if (null == $this->options) {
                $types = $this->typesFactory->create();
                $collection = $types->getCollection();
                
                $this->options = [['label' => '', 'value' => '']];
                foreach ( $collection as $type ) {
                    $this->options[] = [
                        'label' => __('%1 Type', $type->getData('school_type')),
                        'value' => $type->getId()
                    ];
                }
            }
            return $this->options;
        }
    }