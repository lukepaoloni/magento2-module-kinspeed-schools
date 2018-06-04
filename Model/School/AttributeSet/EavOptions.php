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
    use \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

    class EavOptions extends AbstractSource
    {
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
         * Retrieve All options
         *
         * @return array
         */
        public function getAllOptions()
        {
            $this->_options = $this->getOptionArray();
            return $this->_options;
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