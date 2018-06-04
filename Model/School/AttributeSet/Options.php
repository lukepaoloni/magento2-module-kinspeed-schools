<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Model\School\AttributeSet;

    use Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory;
    use Magento\Framework\Data\OptionSourceInterface;
    use Magento\Framework\Exception\LocalizedException;

    class Options implements OptionSourceInterface
    {

        private $typesFactory;
        /**
         * @var \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory
         */
        private $collectionFactory;

        /**
         * Options constructor.
         *
         * @param \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory $collectionFactory
         */
        public function __construct(CollectionFactory $collectionFactory)
        {
            $this->collectionFactory = $collectionFactory;
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
            try {
                $options     = [];
                $collection = $this->collectionFactory->create();
                $schools = $collection->addAttributeToSelect('*');
                foreach ($schools as $school) {
                    /** @var \Kinspeed\Schools\Model\School $school */
                    $options[] = [
                        'label' => $school->getSchoolName(),
                        'value' => $school->getId()
                    ];
                }
                return $options;
            }
            catch (LocalizedException $e) {
                return $e->getMessage();
            }
        }
    }