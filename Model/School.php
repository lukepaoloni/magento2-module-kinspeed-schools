<?php

/**
 * School.php
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */

namespace Kinspeed\Schools\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Directory\Model\Country;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Registry;
use \Magento\Cms\Model\Template\FilterProvider;
use \Magento\Catalog\Model\CategoryFactory;
use Kinspeed\Schools\Model\School\Attribute\Backend\ImageAbstract;
use Kinspeed\Schools\Model\School\Attribute\Backend\ImageFactory;
use \Kinspeed\Address\Model\CountyFactory;
use \Kinspeed\Schools\Model\TypesFactory;
use \Kinspeed\Schools\Model\SuppliersFactory;
use Magento\Framework\UrlInterface;
use Magento\Reports\Test\Block\Adminhtml\Sales\TaxRule\Filter;
use Magento\Store\Model\StoreManagerInterface;
use Kinspeed\Schools\Api\Data\SchoolInterface;
use Kinspeed\Schools\Api\Data\SchoolExtensionInterface;

class School extends AbstractModel implements SchoolInterface
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG       = 'kinspeed_schools_school';
    const STATUS_APPROVED = true;
    const ENTITY          = 'kinspeed_school';
    const SCHOOL_NAME     = 'school_name';
    const IS_ACTIVE       = 'is_active';
    const ADDRESS_1          = 'address_1';
    const ADDRESS_2          = 'address_2';
    const ADDRESS_3          = 'address_3';
    const TOWN               = 'town';
    const POSTCODE           = 'postcode';
    const LOGO               = 'logo';
    const FIRST_NAME         = 'first_name';
    const LAST_NAME           = 'last_name';
    const JOB_TITLE                = 'job_title';
    const EMAIL                    = 'email';
    const ACTIVE_CUSTOMER          = 'active_customer';
    const COUNTY_ID                = 'county_id';
    const SCHOOL_TYPE_ID           = 'school_type_idno';
    const SCHOOL_SUPPLIER_ID       = 'school_supplier_idno';
    const PHONE                    = 'tel';
    const WEBSITE                  = 'website_address';
    const PUPILS_ON_ROLL           = 'pupils_on_roll';
    const NOTES                    = 'notes';
    const ENABLE_PARENT_ORDER      = 'enable_parent_order';
    const ENABLE_SCHOOL_FRAN_ORDER = 'enable_school_fran_order';
    const ENABLE_PPP_SCHOOL         = 'enable_ppp_school';
    const ENABLE_DTS_SCHOOL         = 'enable_dts_school';
    const REGISTERED_INTEREST       = 'registered_amount_interest';
    const COUNTRY_CODE              = 'country_code';

    const XML_CATEGORY_ID = 'kinspeed_schools/school_settings/category_id';
    const XML_GROUP_ID = 'kinspeed_schools/school_settings/customer_group_id';
    const XML_API_KEY = 'kinspeed_schools/school_map/api_key';
    const XML_EMAIL = 'kinspeed_schools/school_settings/email';
    const XML_NAME = 'kinspeed_schools/school_settings/name';

    const LINKED_SCHOOL = 'linked_school';

    /**
     * @var string
     */
    protected $_cacheTag = 'kinspeed_schools_school';
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'kinspeed_schools_school';
    /**
     * @var ImageFactory
     */
    private $imageFactory;
    /**
     * @var \Kinspeed\Address\Model\CountyFactory
     */
    private $countyFactory;
    /**
     * @var \Kinspeed\Schools\Model\TypesFactory
     */
    private $typesFactory;
    /**
     * @var \Kinspeed\Schools\Model\SuppliersFactory
     */
    private $suppliersFactory;
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;
    /**
     * @var \Magento\Framework\Api\ExtensionAttributesFactory
     */
    private $extensionFactory;
    /**
     * @var \Magento\Framework\Api\AttributeValueFactory
     */
    private $customAttributeFactory;
    /**
     * @var \Magento\Directory\Model\Country
     */
    private $country;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Kinspeed\Schools\Model\ResourceModel\School');
    }

    /**
     * School constructor.
     *
     * @param \Magento\Directory\Model\Country                              $country
     * @param \Magento\Cms\Model\Template\FilterProvider                    $filterProvider
     * @param \Kinspeed\Schools\Model\SuppliersFactory                      $suppliersFactory
     * @param \Kinspeed\Schools\Model\TypesFactory                          $typesFactory
     * @param \Kinspeed\Address\Model\CountyFactory                         $countyFactory
     * @param \Kinspeed\Schools\Model\School\Attribute\Backend\ImageFactory $imageFactory
     * @param CategoryFactory                                               $categoryFactory
     * @param \Magento\Framework\UrlInterface                               $urlBuilder
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface              $categoryRepository
     * @param \Magento\Store\Model\StoreManagerInterface                    $storeManager
     * @param Context                                                       $context
     * @param Registry                                                      $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory             $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory                  $customAttributeFactory
     * @param AbstractResource|null                                         $resource
     * @param AbstractDb|null                                               $resourceCollection
     * @param array                                                         $data
     */
    public function __construct(
        Country $country,
        FilterProvider $filterProvider,
        SuppliersFactory $suppliersFactory,
        TypesFactory $typesFactory,
        CountyFactory $countyFactory,
        ImageFactory $imageFactory,
        CategoryFactory $categoryFactory,
        UrlInterface $urlBuilder,
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->imageFactory = $imageFactory;
        $this->countyFactory = $countyFactory;
        $this->typesFactory = $typesFactory;
        $this->suppliersFactory = $suppliersFactory;
        $this->filterProvider = $filterProvider;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->extensionFactory = $extensionFactory;
        $this->customAttributeFactory = $customAttributeFactory;
        $this->country = $country;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return get_class_methods($this);
    }

    /**
     * @return string
     */
    public function getSchoolName()
    {
        return $this->_getData(self::SCHOOL_NAME);
    }

    public function getAdminUrl($id)
    {
        return $this->urlBuilder->getUrl(
            'kinspeed_schools/school/edit',
            [
                'entity_id' => $id
            ]
        );
    }

    public function getCountry()
    {
        $country = $this->country->loadByCode($this->_getData(self::COUNTRY_CODE))->getName();
        return $country;
    }

    public function isBulkOrderEnabledDTS()
    {
        return $this->_getData(self::ENABLE_DTS_SCHOOL);
    }
    public function isBulkOrderEnabledPPP()
    {
        return $this->_getData(self::ENABLE_PPP_SCHOOL);
    }

    public function getCountryId()
    {
        return $this->_getData(self::COUNTRY_CODE) ? $this->_getData(self::COUNTRY_CODE) : 'GB';
    }
    /**
     * @return string
     */
    public function getUrl()
    {
        $url = strtolower(
            trim(
                preg_replace('/ +/', '-', preg_replace('/[^A-Za-z0-9 ]/', '-',
                                                      urlencode(
                                                          html_entity_decode(
                                                              strip_tags(
                                                                  $this->getData('school_name') . '/' . $this->getData('postcode')
                                                              )
                                                          )
                                                      )
                ))
            )
        );
        return $url;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->_getData(self::POSTCODE) ? $this->_getData(self::POSTCODE) : NULL;
    }

    public function getCategoryUrl()
    {
        $category = $this->categoryFactory->create()->loadByAttribute('linked_school', $this->getId());
        try {
            $cat = $this->categoryRepository->get($category->getId(), $this->storeManager->getStore()->getId());
            return $cat->getUrl();
        }
        catch (NoSuchEntityException $e) {
            return $e->getMessage();
        }
    }

    public function getPath()
    {
        $category = $this->categoryFactory->create()->loadByAttribute('linked_school', $this->getData('entity_id'));
        if (!empty($category)) {
            try {
                $url = $this->categoryRepository->get($category->getId(), $this->storeManager->getStore()->getId())
                                                ->getUrl();
                return $url;
            } catch (NoSuchEntityException $e) {
                return $e->getMessage();
            }
        }
        return $this->urlBuilder->getUrl('schools/register/interest',
            [
                'id' => $this->getId()
            ]);
    }

    public function getFullAddress()
    {
        $address = $this->getAddress();
        $address .= ' ' . $this->getPostcode() . ' ' . $this->getCounty() . ' ' . $this->getCountry();
        return $address;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        $address = $this->getAddress1();

        if (!empty($this->getAddress2()))
            $address .= ' ' . $this->getAddress2();
        if (!empty($this->getAddress3()))
            $address .= ' ' . $this->getAddress3();

        return $address;
    }

    public function getLongitude()
    {
        return $this->getData('longitude') ? $this->getData('longitude') : NULL;
    }

    public function getLatitude()
    {
        return $this->getData('latitude') ? $this->getData('latitude') : NULL;
    }

    public function setLongitude($long)
    {
        $this->setData('longitude', $long);
    }

    public function setLatitude($lat)
    {
        $this->setData('latitude', $lat);
    }


    public function getAddressForSearch()
    {
        return $this->_getData(self::ADDRESS_1) . ', ' . $this->_getData(self::ADDRESS_2) . ', ' . $this->_getData(self::TOWN) . ', ' . $this->_getData(self::TOWN);
    }


    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->_getData(self::IS_ACTIVE);
    }

    /**
     * @return string
     */
    public function getTown()
    {
        return $this->_getData(self::TOWN) ? $this->_getData(self::TOWN) : NULL;
    }

    /**
     * {@inheritdoc}
     */
    public function getCounty()
    {
        $county = $this->countyFactory->create()->load($this->getData(self::COUNTY_ID))->getName();
        return $county;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        $schoolType = $this->typesFactory->create()->load($this->getData(self::SCHOOL_TYPE_ID));
        return $schoolType;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupplier()
    {
        $schoolSupplier = $this->suppliersFactory->create()->load($this->getData(self::SCHOOL_SUPPLIER_ID));
        return $schoolSupplier;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhoneNumber()
    {
        return $this->_getData(self::PHONE);
    }

    /**
     * {@inheritdoc}
     */
    public function getLogo()
    {
        try {
            return $this->getImageSrc(self::LOGO) ? $this->getImageSrc(self::LOGO) : null;
        }
        catch (LocalizedException $e) {
            return $e->getMessage();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsite()
    {
        return $this->_getData(self::WEBSITE);
    }

    /**
     * {@inheritdoc}
     */
    public function getPupilsOnRoll()
    {
        return $this->_getData(self::PUPILS_ON_ROLL);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_getData(self::FIRST_NAME) . ' ' . $this->_getData(self::LAST_NAME);
    }
    /**
     * {@inheritdoc}
     */
    public function getFirstName()
    {
        return $this->_getData(self::FIRST_NAME);
    }
    /**
     * {@inheritdoc}
     */
    public function getSurname()
    {
        return $this->_getData(self::LAST_NAME);
    }
    /**
     * {@inheritdoc}
     */
    public function getJobTitle()
    {
        return $this->_getData(self::JOB_TITLE);
    }
    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->_getData(self::EMAIL);
    }
    /**
     * {@inheritdoc}
     */
    public function getNotes()
    {
        try {
            return $this->filterProvider->getPageFilter()->filter($this->_getData(self::NOTES));
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * {@inheritdoc}
     */
    public function isActiveCustomer()
    {
        return $this->_getData(self::ACTIVE_CUSTOMER) ? $this->_getData(self::ACTIVE_CUSTOMER) : false;
    }
    /**
     * {@inheritdoc}
     */
    public function isEnabledParentOrder()
    {
        return $this->_getData('enable_parent_order');
    }
    /**
     * {@inheritdoc}
     */
    public function isEnabledSchoolFranOrder()
    {
        return $this->_getData('enable_school_fran_order');
    }

    /**
     * {@inheritdoc}
     */
    public function chargePostage()
    {
        return $this->getData('charge_postage');
    }
    /**
     * {@inheritdoc}
     */
    public function chargePostageToSchool()
    {
        return $this->getData('charge_postage_school');
    }
    /**
     * {@inheritdoc}
     */
    public function chargePostageToFranchisee()
    {
        return $this->getData('charge_postage_franchisee');
    }
    /**
     * {@inheritdoc}
     */
    public function showSchool()
    {
        return $this->_getData('show_school');
    }
    /**
     * {@inheritdoc}
     */
    public function isEnabledBulkDelivery()
    {
        return $this->getData('enable_bulk_delivery');
    }
    /**
     * {@inheritdoc}
     */
    public function isEnabledBulkDiscounts()
    {
        return $this->getData('enable_bulk_discounts');
    }
    /**
     * {@inheritdoc}
     */
    public function isEnabledBulkAdress()
    {
        return $this->getData('enable_bulk_address');
    }
    /**
     * {@inheritdoc}
     */
    public function isSchoolEnabledPPP()
    {
        return $this->getData('enable_ppp_school');
    }
    /**
     * {@inheritdoc}
     */
    public function isParentEnabledPPP()
    {
        return $this->getData('enable_ppp_parent');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Save from collection data
     *
     * @param array $data
     *
     * @return $this|bool
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function saveCollection(array $data)
    {
        if (isset($data[$this->getId()])) {
            $this->addData($data[$this->getId()]);
            $this->getResource()->save($this);
        }
        return $this;
    }

    public function getImageValueForForm($imageAttrCode)
    {
        /** @var ImageAbstract $image */
        $image = $this->imageFactory->create($imageAttrCode);
        return $image->getFileValueForForm($this);
    }

    /**
     * @param string $imageAttrCode
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImageSrc($imageAttrCode)
    {
        /** @var ImageAbstract $image */
        $image = $this->imageFactory->create($imageAttrCode);
        return $image->getFileInfo($this)->getUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function setSchoolName($name)
    {
        return $this->setData(self::SCHOOL_NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }
    /**
     * {@inheritdoc}
     */
    public function getAddress1()
    {
        return $this->_getData(self::ADDRESS_1);
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress1($address_1)
    {
        return $this->setData(self::ADDRESS_1, $address_1);
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress2()
    {
        return $this->_getData(self::ADDRESS_2);
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress2($address_2)
    {
        return $this->setData(self::ADDRESS_2, $address_2);
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress3()
    {
        return $this->_getData(self::ADDRESS_3);
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress3($address_3)
    {
        return $this->setData(self::ADDRESS_3, $address_3);
    }

    /**
     * {@inheritdoc}
     */
    public function setCounty($county_id)
    {
        return $this->setData(self::COUNTY_ID, $county_id);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type_id)
    {
        return $this->setData(self::SCHOOL_TYPE_ID, $type_id);
    }

    /**
     * {@inheritdoc}
     */
    public function setPhoneNumber($phone)
    {
        return $this->setData(self::PHONE, $phone);
    }

    /**
     * {@inheritdoc}
     */
    public function setLogo($logo)
    {
        return $this->setData(self::LOGO, $logo);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsite($website)
    {
        return $this->setData(self::WEBSITE, $website);
    }

    /**
     * {@inheritdoc}
     */
    public function setPupilsOnRoll($num_of_pupils)
    {
        return $this->setData(self::PUPILS_ON_ROLL, $num_of_pupils);
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstName($first_name)
    {
        return $this->setData(self::FIRST_NAME, $first_name);
    }

    /**
     * {@inheritdoc}
     */
    public function setSurname($last_name)
    {
        return $this->setData(self::LAST_NAME, $last_name);
    }

    /**
     * {@inheritdoc}
     */
    public function setJobTitle($job_title)
    {
        return $this->setData(self::JOB_TITLE, $job_title);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActiveCustomer($active_customer)
    {
        return $this->setData(self::ACTIVE_CUSTOMER, $active_customer);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsEnabledParentOrder($enable_parent_order)
    {
        return $this->setData(self::ENABLE_PARENT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsEnabledSchoolFranOrder($enable_school_fran_order)
    {
        return $this->setData(self::ENABLE_SCHOOL_FRAN_ORDER, $enable_school_fran_order);
    }

//    /**
//     * {@inheritdoc}
//     * @throws \Magento\Framework\Exception\LocalizedException
//     */
//    public function getExtensionAttributes()
//    {
//        $extensionAttributes = $this->extensionFactory->create(SchoolInterface::class);
//        $this->setExtensionAttributes($extensionAttributes);
//        return $extensionAttributes;
//    }
//
//    /**
//     * {@inheritdoc}
//     *
//     * @throws \Magento\Framework\Exception\LocalizedException
//     * return $this
//     */
//    public function setExtensionAttributes(SchoolExtensionInterface $extensionAttributes)
//    {
//        return $this->setsetExtensionAttributes($extensionAttributes);
//
//    }
    /**
     * @return integer
     */
    public function getTotalRegisteredInterests()
    {
        return $this->_getData(self::REGISTERED_INTEREST);
    }

    /**
     * Replaces the total registered interest with given value.
     *
     * @param $value
     *
     * @return void
     */
    public function setRegisteredInterest($value)
    {
        $this->setData(self::REGISTERED_INTEREST, $value);
    }

    public function addRegisteredInterest()
    {
        $previousValue = (int) $this->_getData(self::REGISTERED_INTEREST);
        $total = $previousValue + 1;
        $this->setData(self::REGISTERED_INTEREST, $total);
    }
}
