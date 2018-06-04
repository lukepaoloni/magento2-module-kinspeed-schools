<?php
/**
 * Save
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */
namespace Kinspeed\Schools\Controller\Adminhtml\School;

use Kinspeed\Schools\Api\SchoolRepositoryInterface;
use Kinspeed\Schools\Helper\Config;
use Kinspeed\Schools\Model\School;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Kinspeed\Schools\Model\School\Attribute\Backend\ImageFactory;
use Kinspeed\Schools\Model\SchoolFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use \Mirasvit\SearchElastic\Model\Engine as ElasticSearchEngine;

class Save extends Action
{
    /** @var SchoolFactory $objectFactory */
    protected $objectFactory;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;
    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    private $addressFactory;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;
    /**
     * @var \Mirasvit\SearchElastic\Model\Engine
     */
    private $esEngine;
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;
    /**
     * @var \Kinspeed\Schools\Api\SchoolRepositoryInterface
     */
    private $schoolRepository;
    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressRepository;
    /**
     * @var \Magento\Backend\App\Action\Context
     */
    private $context;
    /**
     * @var \Kinspeed\Schools\Helper\Config
     */
    private $config;

    /**
     * @param Context                                           $context
     * @param SchoolFactory                                     $objectFactory
     * @param \Kinspeed\Schools\Api\SchoolRepositoryInterface   $schoolRepository
     * @param \Magento\Customer\Model\CustomerFactory           $customerFactory
     * @param \Magento\Customer\Model\AddressFactory            $addressFactory
     * @param \Magento\Catalog\Model\CategoryFactory            $categoryFactory
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface  $categoryRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\AddressRepositoryInterface  $addressRepository
     * @param \Magento\Framework\Escaper                        $escaper
     * @param \Mirasvit\SearchElastic\Model\Engine              $esEngine
     * @param \Kinspeed\Schools\Helper\Config                   $config
     */
    public function __construct(
        Context $context,
        SchoolFactory $objectFactory,
        SchoolRepositoryInterface $schoolRepository,
        CustomerFactory $customerFactory,
        AddressFactory $addressFactory,
        CategoryFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        Escaper $escaper,
        ElasticSearchEngine $esEngine,
        Config $config
    ) {
        $this->objectFactory = $objectFactory;
        parent::__construct($context);
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->categoryFactory = $categoryFactory;
        $this->esEngine = $esEngine;
        $this->categoryRepository = $categoryRepository;
        $this->customerRepository = $customerRepository;
        $this->escaper = $escaper;
        $this->schoolRepository = $schoolRepository;
        $this->addressRepository = $addressRepository;
        $this->context = $context;
        $this->config = $config;
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
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $storeId = (int) $this->getRequest()->getParam('store_id');
        $data = $this->getRequest()->getParams();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $params = [];
            $params['store'] = $storeId;

            if (empty($data['entity_id'])) {
                $this->messageManager->addErrorMessage(__('School ID was not specified.'));
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
            $params['entity_id'] = $this->escaper->escapeHtml($data['entity_id']);
            $imageData = $this->preparedImagesData($data);
            $data = array_merge($data, $imageData);

            $school = $this->schoolRepository->getById($data['entity_id']);

            $this->_eventManager->dispatch(
                'kinspeed_schools_school_prepare_save',
                ['object' => $this->objectFactory, 'request' => $this->getRequest()]
            );

            if ($school->isActiveCustomer()) {
                try {
                    $this->saveCustomer($data, $school);
                    $this->saveCategory($data, $storeId);
                    $this->messageManager->addSuccessMessage(__('You updated the customer & category for this school.')
                    );
                }
                catch (NoSuchEntityException $noSuchEntityException) {
                    $this->messageManager->addErrorMessage(__($noSuchEntityException->getMessage()));
                }
            }

            try {
                $this->saveLongAndLat($data, $school);
                $this->saveSchool($data);
                $this->saveToElasticSearch($data);
                $this->messageManager->addSuccessMessage(__('You saved this record.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $params['entity_id'] = $school->getId();
                    $params['_current'] = true;
                    return $resultRedirect->setPath('*/*/edit', $params);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the record.'));
            }

            $this->_getSession()->setFormData($this->getRequest()->getPostValue());
            return $resultRedirect->setPath('*/*/edit', $params);
        }
        return $resultRedirect->setPath('*/*/');
    }

    private function saveLongAndLat(&$data, &$school)
    {
        /** @var \Kinspeed\Schools\Model\School $school */
        if ($data['address_1'] != $school->getAddress1()
            ||
            $data['address_2'] != $school->getAddress2()
            ||
            $data['address_3'] != $school->getAddress3()
            ||
            $data['postcode'] != $school->getPostcode()
        ) {
            $address = $school->getFullAddress();
            $address = str_replace(' ', '+', $address);
            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?address=' . $address . '&key=' . $this->config->getApiKey(
                                         )
            );
            $gmap    = json_decode($geocode);
            if (isset($gmap->results[0])) {
                $lat               = $gmap->results[0]->geometry->location->lat;
                $lng               = $gmap->results[0]->geometry->location->lng;
                $data['longitude'] = $lng;
                $data['latitude']  = $lat;
            }
        }
    }

    private function saveSchool(&$data)
    {
        $schoolFactory = $this->objectFactory->create();
        $schoolFactory->addData($data);
        $this->schoolRepository->save($schoolFactory);
    }

    /**
     * @param $data
     * @param $storeId
     *
     * @return null
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function saveCategory(&$data, &$storeId)
    {
        $category = $this->categoryFactory->create()->loadByAttribute(School::LINKED_SCHOOL, $data['entity_id']);
        if (!$category)
            return null;

        $categoryRepository = $this->categoryRepository->get($category->getId(), $storeId);

        $categoryRepository->setName($this->escaper->escapeHtml($data['school_name']));
        $categoryRepository->setIsActive($this->escaper->escapeHtml((bool) $data['active_customer']));
        $categoryRepository->setImage($this->escaper->escapeHtml($data['logo'][0]['url']), array('image', 'small_image', 'thumbnail'), true, false);
        $categoryRepository->setCustomAttribute(School::LINKED_SCHOOL, $this->escaper->escapeHtml($data['entity_id']));

        $this->categoryRepository->save($categoryRepository);
    }

    /**
     * @param $data
     *
     * @param $school
     *
     * @return null
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    private function saveCustomer(&$data, &$school)
    {
        /** @var \Kinspeed\Schools\Model\School $school */
        $customer   = $this->customerRepository->get($school->getEmail());
        if (!$customer)
            return null;
        $customer->setEmail($this->escaper->escapeHtml($data['email']));
        $customer->setFirstname($this->escaper->escapeHtml($data['first_name']));
        $customer->setLastname($this->escaper->escapeHtml($data['last_name']));
        $customer->setCustomAttribute(School::LINKED_SCHOOL, $this->escaper->escapeHtml($data['entity_id']));
        $customer->setGroupId($this->getCustomerGroupId());

        $this->customerRepository->save($customer);

        $addresses = $customer->getAddresses();
        $this->saveCustomerAddresses($addresses, $data);
    }

    /**
     * @param $addresses
     * @param $data
     *
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveCustomerAddresses(&$addresses, &$data)
    {
        /** @var \Magento\Customer\Model\Address $address */
        foreach ($addresses as $address) {

            $address         = $this->addressRepository->getById($address->getId());
            if (!$address)
                return null;
            $street = [
                $data['address_1'],
                $data['address_2']
            ];
            $address->setFirstname($this->escaper->escapeHtml($data['first_name']));
            $address->setLastname($this->escaper->escapeHtml($data['last_name']));
            $address->setCompany($this->escaper->escapeHtml($data['school_name']));
            $address->setStreet($this->escaper->escapeHtml($street));
            $address->setCity($this->escaper->escapeHtml($data['town']));
            $address->setPostcode($this->escaper->escapeHtml($data['postcode']));
            $address->setTelephone($this->escaper->escapeHtml($data['tel']));
            $address->setCountryId($this->escaper->escapeHtml($data['country_code']));

            $this->addressRepository->save($address);
        }
    }

    public function saveToElasticSearch($data)
    {
        if ($data) {
            $elasticSearch = $this->esEngine->getClient();
            $logo = '';
            if (isset($data['logo'][0]['url'])) {
                $logo = $data['logo'][0]['url'];
            }
            $indexed = $elasticSearch->index(
                [
                    'index' => 'schools',
                    'id' => $data['entity_id'],
                    'type' => 'school',
                    'body' => [
                        'school_id' => $data['entity_id'],
                        'school_name' => $data['school_name'],
                        'address_1' => $data['address_1'],
                        'address_2' => $data['address_2'],
                        'address_3' => $data['address_3'],
                        'town' => $data['town'],
                        'postcode' => $data['postcode'],
                        'logo' => $logo
                    ]
                ]
            );
        }
    }

    protected function preparedImagesData(array $data): array
    {
        $imagesData = [];
        $imageAttributeCodes = array_keys(ImageFactory::IMAGE_ATTRIBUTE_CODES);
        foreach ($imageAttributeCodes as $imageAttrCode) {
            if (empty($data[$imageAttrCode])) {
                $imagesData[$imageAttrCode]['delete'] = true;
            }
        }
        return $imagesData;
    }

}
