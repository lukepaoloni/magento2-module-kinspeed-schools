<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Controller\Adminhtml\School;

    use Kinspeed\Schools\Helper\Config;
    use Kinspeed\Schools\Model\School;
    use Magento\Backend\App\Action;
    use Magento\Backend\App\Action\Context;
    use Kinspeed\Schools\Model\SchoolFactory;
    use Magento\Catalog\Api\CategoryRepositoryInterface;
    use Magento\Catalog\Model\CategoryFactory;
    use Magento\Customer\Api\Data\AddressInterface;
    use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
    use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
    use Magento\Customer\Model\AddressFactory;
    use Magento\Customer\Model\CustomerFactory;
    use \Magento\Customer\Api\AddressRepositoryInterface;
    use \Magento\Customer\Api\CustomerRepositoryInterface;
    use Magento\Framework\App\Area;
    use Magento\Framework\App\Config\ScopeConfigInterface;
    use Magento\Framework\Encryption\Encryptor;
    use \Kinspeed\Schools\Api\SchoolRepositoryInterface;
    use Magento\Framework\Escaper;
    use Magento\Framework\Exception\MailException;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Magento\Framework\Mail\Template\TransportBuilder;
    use Magento\Framework\Translate\Inline\StateInterface;
    use Magento\Store\Model\ScopeInterface;
    use Magento\Store\Model\Store;
    use Magento\Store\Model\StoreManagerInterface;
    use Magento\Customer\Api\Data\CustomerInterfaceFactory;

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
         * @var \Magento\Customer\Api\CustomerRepositoryInterface
         */
        private $customerRepository;
        /**
         * @var \Magento\Framework\Encryption\Encryptor
         */
        private $encryptor;
        /**
         * @var \Magento\Customer\Api\AddressRepositoryInterface
         */
        private $addressRepository;
        /**
         * @var \Kinspeed\Schools\Api\SchoolRepositoryInterface
         */
        private $schoolRepository;
        /**
         * @var \Magento\Catalog\Api\CategoryRepositoryInterface
         */
        private $categoryRepository;
        /**
         * @var \Kinspeed\Schools\Helper\Config
         */
        private $config;
        /**
         * @var \Magento\Store\Model\StoreManagerInterface
         */
        private $storeManager;
        /**
         * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
         */
        private $customerInterfaceFactory;
        /**
         * @var \Magento\Framework\Escaper
         */
        private $escaper;
        /**
         * @var \Magento\Framework\Mail\Template\TransportBuilder
         */
        private $transportBuilder;
        /**
         * @var \Magento\Framework\Translate\Inline\StateInterface
         */
        private $inlineTranslation;
        /**
         * @var \Magento\Framework\App\Config\ScopeConfigInterface
         */
        private $scopeConfig;
        /**
         * @var \Magento\Customer\Api\Data\AddressInterface
         */
        private $address;

        /**
         * @param Context                                                          $context
         * @param SchoolFactory                                                    $schoolFactory
         * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory
         * @param \Magento\Catalog\Model\CategoryFactory                           $categoryFactory
         * @param \Magento\Customer\Model\AddressFactory                           $addressFactory
         * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
         * @param \Magento\Customer\Api\CustomerRepositoryInterface                $customerRepository
         * @param \Magento\Framework\Encryption\Encryptor                          $encryptor
         * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory  $categoryCollectionFactory
         * @param \Magento\Customer\Api\AddressRepositoryInterface                 $addressRepository
         * @param \Kinspeed\Schools\Api\SchoolRepositoryInterface                  $schoolRepository
         * @param \Kinspeed\Schools\Helper\Config                                  $config
         * @param \Magento\Catalog\Api\CategoryRepositoryInterface                 $categoryRepository
         * @param \Magento\Store\Model\StoreManagerInterface                       $storeManager
         * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory              $customerInterfaceFactory
         * @param \Magento\Framework\Escaper                                       $escaper
         * @param \Magento\Framework\Mail\Template\TransportBuilder                $transportBuilder
         * @param \Magento\Framework\Translate\Inline\StateInterface               $inlineTranslation
         * @param ScopeConfigInterface                                             $scopeConfig
         * @param \Magento\Customer\Api\Data\AddressInterface                      $address
         */
        public function __construct(
            Context $context,
            SchoolFactory $schoolFactory,
            CustomerFactory $customerFactory,
            CategoryFactory $categoryFactory,
            AddressFactory $addressFactory,
            CustomerCollectionFactory $customerCollectionFactory,
            CustomerRepositoryInterface $customerRepository,
            Encryptor $encryptor,
            CategoryCollectionFactory $categoryCollectionFactory,
            AddressRepositoryInterface $addressRepository,
            SchoolRepositoryInterface $schoolRepository,
            Config $config,
            CategoryRepositoryInterface $categoryRepository,
            StoreManagerInterface $storeManager,
            CustomerInterfaceFactory $customerInterfaceFactory,
            Escaper $escaper,
            TransportBuilder $transportBuilder,
            StateInterface $inlineTranslation,
            ScopeConfigInterface $scopeConfig,
            AddressInterface $address
        )
        {
            parent::__construct($context);
            $this->schoolFactory             = $schoolFactory;
            $this->customerFactory           = $customerFactory;
            $this->categoryFactory           = $categoryFactory;
            $this->addressFactory            = $addressFactory;
            $this->customerCollectionFactory = $customerCollectionFactory;
            $this->categoryCollectionFactory = $categoryCollectionFactory;
            $this->customerRepository        = $customerRepository;
            $this->encryptor                 = $encryptor;
            $this->addressRepository         = $addressRepository;
            $this->schoolRepository          = $schoolRepository;
            $this->categoryRepository        = $categoryRepository;
            $this->config                    = $config;
            $this->storeManager = $storeManager;
            $this->customerInterfaceFactory = $customerInterfaceFactory;
            $this->escaper = $escaper;
            $this->transportBuilder = $transportBuilder;
            $this->inlineTranslation = $inlineTranslation;
            $this->scopeConfig = $scopeConfig;
            $this->address = $address;
        }

        /**
         * {@inheritdoc}
         */
        protected function _isAllowed()
        {
            return $this->_authorization->isAllowed('Kinspeed_Schools::school');
        }

        public function getConfigCategoryId()
        {
            return $this->config->getCategoryId();
        }

        public function getCustomerGroupId()
        {
            return $this->config->getCustomerGroupId();
        }

        /**
         * Generate action
         *
         * @return \Magento\Framework\Controller\ResultInterface
         * @throws \Magento\Framework\Exception\LocalizedException
         */
        public function execute()
        {
            $storeId = (int)$this->getRequest()->getParam('store_id');
            $data    = $this->getRequest()->getParams();
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            if ($data) {
                $params          = [];
                $params['store'] = $storeId;
                if (!empty($data['entity_id'])) {
                    $params['entity_id'] = $data['entity_id'];
                }

                $school = $this->schoolRepository->getById($params['entity_id']);
                if (!$school->isActiveCustomer()) {
                    $params['entity_id'] = $school->getId();
                    $params['_current']  = true;
                    $this->messageManager->addErrorMessage(
                        __($school->getSchoolName() . ' is not an active SchoolTrends customer.')
                    );

                    return $resultRedirect->setPath('*/*/edit', $params);
                }
                try {
                    $this->createCustomer($school, $storeId);
                    $this->createCategory($school, $storeId);
                    $this->messageManager->addSuccessMessage(__('You\'ve created a category and customer record.'));
                    $this->_getSession()->setFormData(false);

                    return $resultRedirect->setPath('*/*/edit', $params);
                }
                catch (NoSuchEntityException $noSuchEntityException) {
                    $this->messageManager->addErrorMessage($noSuchEntityException->getMessage());
                }
                $this->_getSession()->setFormData($this->getRequest()->getPostValue());

                return $resultRedirect->setPath('*/*/edit', $params);
            }

            return $resultRedirect->setPath('*/*/');
        }

        /**
         * @param $school
         * @param $storeId
         *
         * @throws \Magento\Framework\Exception\LocalizedException
         * @throws \Magento\Framework\Exception\NoSuchEntityException
         */
        private function createCategory(&$school, &$storeId)
        {
            $category = $this->categoryFactory->create();
            $parentCategory = $this->categoryFactory->create()->load($this->getConfigCategoryId());

            /** @var \Kinspeed\Schools\Model\School $school */
            $category->setName($school->getSchoolName());
            $category->setIsActive($school->isActive());
            $category->setParentId($parentCategory->getId());
            $category->setImage($school->getLogo(), array('image', 'small_image', 'thumbnail'), true, false);
            $category->setStoreId($storeId);
            $category->setData(School::LINKED_SCHOOL, (int) $school->getId());
            $category->setCustomAttribute(School::LINKED_SCHOOL, (int) $school->getId());
            $category->setIncludeInMenu(false);
            $category->setData('is_anchor', false);
            $category->setPosition($parentCategory->getPosition() + 1);
            $category->setData('custom_use_parent_settings', true);
            $category->setUrlKey($school->getUrl());
            $this->categoryRepository->save($category);
            $path = $parentCategory->getPath() . '/'. $category->getId();
            $category->setPath($path);
            $this->categoryRepository->save($category);
        }

        /**
         * @param $school
         *
         * @param $storeId
         *
         * @throws \Magento\Framework\Exception\InputException
         * @throws \Magento\Framework\Exception\LocalizedException
         * @throws \Magento\Framework\Exception\State\InputMismatchException
         */
        private function createCustomer($school, $storeId)
        {
            /** @var \Kinspeed\Schools\Model\School $school */
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
            if (!$websiteId)
                $websiteId = 1;
            $password = $this->config->generateStrongPassword();
            $encryptedPassword =$this->encryptor->getHash($password, true);
            $this->customerRepository->save(
                $this->customerFactory
                ->create()
                ->getDataModel()
                ->setWebsiteId($websiteId)
                ->setGroupId($this->getCustomerGroupId())
                ->setFirstname($school->getFirstName())
                ->setLastname($school->getSurname())
                ->setEmail($school->getEmail())
                ->setCustomAttribute(School::LINKED_SCHOOL, (int) $school->getId())
                , $encryptedPassword);

            $customer = $this->customerFactory
                ->create()
                ->setWebsiteId($websiteId)
                ->loadByEmail($school->getEmail());
            $customer->sendNewAccountEmail()
                     ->sendPasswordResetConfirmationEmail();
            $this->createCustomerAddress($school, $customer);
            
        }

        private function sendPasswordToCustomer($school, $password)
        {
            /** @var \Kinspeed\Schools\Model\School $school */
            $to = [
                'name' => $school->getName(),
                'email' => $school->getEmail()
            ];
            $sender = [
                'name' => $this->config->getEmail(),
                'email' => $this->config->getName()
            ];
            $this->inlineTranslation->suspend();
            $transport = $this->transportBuilder
                    ->setTemplateIdentifier('send_customer_password')
                    ->setTemplateOptions(
                        [
                            'area' => Area::AREA_FRONTEND,
                            'store' => $this->storeManager->getStore()->getId()
                        ]
                    )
                    ->setTemplateVars(
                        [
                            'password' => $password
                        ]
                    )
                ->setFrom($sender)
                ->addTo($to['email'])
                ->getTransport();
            try {
                $transport->sendMessage();
            }
            catch (MailException $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
            $this->inlineTranslation->resume();
        }

        /**
         * @param                                             $school
         *
         * @param                                             $customer
         *
         * @throws \Magento\Framework\Exception\LocalizedException
         * @throws \Magento\Framework\Exception\NoSuchEntityException
         */
        private function createCustomerAddress($school, $customer)
        {
            /** @var \Magento\Customer\Model\Address $address */
            /** @var \Magento\Customer\Model\Customer $customer */
            /** @var \Kinspeed\Schools\Model\School $school */
            $address = $this->address;
            $street = [
                $school->getAddress1(),
                $school->getAddress2()
            ];
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
            $customer = $this->customerRepository->get($school->getEmail(), $websiteId);
            $id = $customer->getId();
            $address->setCustomerId($customer->getId());
            $address->setFirstname($school->getFirstName());
            $address->setLastname($school->getSurname());
            $address->setIsDefaultBilling(true);
            $address->setIsDefaultShipping(true);
            $address->setCompany($school->getSchoolName());
            $address->setStreet($street);
            $address->setCity($school->getTown());
            $address->setCountryId($school->getCountryId());
            $address->setPostcode($school->getPostcode());
            $address->setTelephone($school->getPhoneNumber());

            $this->addressRepository->save($address);
        }
    }