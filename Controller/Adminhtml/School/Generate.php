<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Controller\Adminhtml\School;

    use Magento\Backend\App\Action;
    use Magento\Backend\App\Action\Context;
    use Kinspeed\Schools\Model\School\Attribute\Backend\ImageFactory;
    use Kinspeed\Schools\Model\SchoolFactory;
    use Magento\Catalog\Model\CategoryFactory;
    use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
    use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
    use Magento\Customer\Model\AddressFactory;
    use Magento\Customer\Model\CustomerFactory;
    use Magento\Framework\App\ResponseInterface;

    class Generate extends Action
    {

        /** @var SchoolFactory $schoolFactory */
        protected $schoolFactory;
        /*
         *
         * @var Magento\Catalog\Model\CategoryFactory
         */
        private $categoryFactory;
        /**
         * @var \Magento\Customer\Model\CustomerFactory
         */
        private $customerFactory;
        /**
         * @var \Magento\Customer\Model\AddressFactory
         */
        private $addressFactory;
        /**
         * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
         */
        private $customerCollectionFactory;
        /**
         * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
         */
        private $categoryCollectionFactory;

        /**
         * @param Context                                                          $context
         * @param SchoolFactory                                                    $schoolFactory
         * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory
         * @param \Magento\Catalog\Model\CategoryFactory                           $categoryFactory
         * @param \Magento\Customer\Model\AddressFactory                           $addressFactory
         * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
         * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory  $categoryCollectionFactory
         */
        public function __construct(
            Context $context,
            SchoolFactory $schoolFactory,
            CustomerFactory $customerFactory,
            CategoryFactory $categoryFactory,
            AddressFactory $addressFactory,
            CustomerCollectionFactory $customerCollectionFactory,
            CategoryCollectionFactory $categoryCollectionFactory
        )
        {
            parent::__construct($context);
            $this->schoolFactory = $schoolFactory;
            $this->customerFactory = $customerFactory;
            $this->categoryFactory = $categoryFactory;
            $this->addressFactory = $addressFactory;
            $this->customerCollectionFactory = $customerCollectionFactory;
            $this->categoryCollectionFactory = $categoryCollectionFactory;
        }

        /**
         * {@inheritdoc}
         */
        protected function _isAllowed()
        {
            return $this->_authorization->isAllowed('Kinspeed_Schools::school');
        }

        /**
         * Generate action
         *
         * @return \Magento\Framework\Controller\ResultInterface
         * @throws \Magento\Framework\Exception\LocalizedException
         */
        public function execute()
        {
            $storeId = (int) $this->getRequest()->getParam('store_id');
            $data    = $this->getRequest()->getParams();
            $customer = $this->customerFactory->create();
            $category = $this->categoryFactory->create();
            $parentCategory = $this->categoryFactory->create()->load(3620);
            $customerAddress = $this->addressFactory->create();
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            if ($data) {
                $params         = [];
                $params['store'] = $storeId;
                if (!empty($data['entity_id'])) {
                    $params['entity_id'] = $data['entity_id'];
                }
                $school = $this->schoolFactory->create()->load($params['entity_id']);
                if (!$school->isActiveCustomer()) {
                    $params['entity_id'] = $school->getId();
                    $params['_current']  = true;
                    $this->messageManager->addErrorMessage(__($school->getSchoolName(
                                                              ) . ' is not an active SchoolTrends customer.'
                                                           )
                    );
                    return $resultRedirect->setPath('*/*/edit', $params);
                }
                // Create Category For School
                $category->setName($school->getSchoolName());
                $category->setIsActive($school->isActive());
                $category->setImage($school->getLogo(), array('image', 'small_image', 'thumbnail'), true, false);
                $category->setPath($parentCategory->getPath());
                $category->setStoreId($params['store']);
                // TODO: This isn't very stable as you have to manually enter the parent ID.
                $category->setParentId($parentCategory->getId());
                $category->setData('linked_school', $school->getId());
                $category->setData('include_in_menu', false);
                $category->setData('is_anchor', false);
                $category->setData('custom_use_parent_settings', true);
                $category->setUrlKey($school->getUrl());
                try {
                    $category->save();
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
                try {
                    // Create Customer For School
                    $customer->setGroupId(2);
                    $customer->setData('email', $school->getEmail());
                    $customer->setData('firstname', $school->getFirstName());
                    $customer->setData('lastname', $school->getSurname());
                    $customer->setData('linked_school', $school->getId());
                    //TODO: Remove before live. Used for testing.
                    $customer->setPassword('password');
                    $customer->save();
                    // Add Customer Address Details
                    $address = $customerAddress->setCustomerId($customer->getId());
                    $address->setData('firstname', $school->getFirstName());
                    $address->setData('lastname', $school->getSurname());
                    $address->setIsDefaultBilling('1');
                    $address->setIsDefaultShipping('1');
                    $address->setSaveInAddressBook('1');
                    $address->setData('company', $school->getSchoolName());
                    $address->setData('street', $school->getData('address_1'));
                    $address->setData('city', $school->getTown());
                    $address->setData('postcode', $school->getPostcode());
                    $address->setData('country_id', 'GB');
                    $address->setData('telephone', $school->getPhoneNumber());
                    $address->save();
                    //$customer->sendNewAccountEmail();
                    $this->messageManager->addSuccessMessage(__('You\'ve generated a category and customer record.'));
                    $this->_getSession()->setFormData(false);

                    return $resultRedirect->setPath('*/*/edit', $params);
                }
                catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
                $this->_getSession()->setFormData($this->getRequest()->getPostValue());

                return $resultRedirect->setPath('*/*/edit', $params);
            }

            return $resultRedirect->setPath('*/*/');
        }
    }