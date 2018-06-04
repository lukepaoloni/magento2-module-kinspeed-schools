<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Controller\Ajax;

    use Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory;
    use Kinspeed\Schools\Model\School;
    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context;
    use Magento\Framework\Controller\Result\JsonFactory;
    use Magento\Framework\Exception\LocalizedException;

    class Schools extends Action
    {
        /**
         * @var \Kinspeed\Schools\Controller\Ajax\JsonFactory
         */
        private $resultJsonFactory;
        /**
         * @var \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory
         */
        private $collectionFactory;

        /**
         * @param Context                                                        $context
         * @param \Magento\Framework\Controller\Result\JsonFactory               $resultJsonFactory
         * @param \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory $collectionFactory
         */
        public function __construct(
            Context $context,
            JsonFactory $resultJsonFactory,
            CollectionFactory $collectionFactory
        )
        {
            parent::__construct($context);
            $this->resultJsonFactory = $resultJsonFactory;
            $this->collectionFactory = $collectionFactory;
        }

        /**
         * Index Action
         *
         * @return \Magento\Framework\View\Result\Page
         */
        public function execute()
        {
            // TODO: Add is_active filter
            $json = [];
            try {

                $collection = $this->collectionFactory->create()
                              ->addAttributeToSelect([
                                  'school_name',
                                  'address_1',
                                  'address_2',
                                  'town',
                                  'logo',
                                  'website_address',
                                  'postcode',
                                  'latitude',
                                  'longitude',
                                  'logo'
                             ]);
                foreach ($collection as $school) {
                    /** @var \Kinspeed\Schools\Model\School $school */
                    if (!$school->isActive())
                        continue;
                    $id     = $school->getId();
                    $name = $school->getSchoolName();
                    $address = $school->getAddress();
                    $city = $school->getTown();
                    $link = $school->getPath();
                    $image = $school->getLogo();
                    $latitude = $school->getLatitude();
                    $longitude = $school->getLongitude();
                    $is_st_customer = $school->isActiveCustomer();
                    $country = $school->getCountryId();
                    $postcode = $school->getPostcode();

                    $json[] = [
                        'school_id' => $id,
                        'name' => $name,
                        'link' => $link,
                        'image' => $image,
                        'address' => $address,
                        'city' => $city,
                        'postcode' => $postcode,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'is_available_at_school_trends' => $is_st_customer,
                        'country' => $country
                    ];
                }
            }
            catch (LocalizedException $e) {
                $json = ['error' => $e->getMessage()];
            }
            return $this->resultJsonFactory->create()->setData($json);
        }
    }