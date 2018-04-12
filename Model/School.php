<?php

/**
 * School.php
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */

namespace Kinspeed\Schools\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\DataObject\IdentityInterface;
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

class School extends AbstractModel implements IdentityInterface
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG       = 'kinspeed_schools_school';
    const STATUS_APPROVED = true;
    const ENTITY = 'kinspeed_school';
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
     * @param \Magento\Cms\Model\Template\FilterProvider                    $filterProvider
     * @param \Kinspeed\Schools\Model\SuppliersFactory                      $suppliersFactory
     * @param \Kinspeed\Schools\Model\TypesFactory                          $typesFactory
     * @param \Kinspeed\Address\Model\CountyFactory                         $countyFactory
     * @param \Kinspeed\Schools\Model\School\Attribute\Backend\ImageFactory $imageFactory
     * @param CategoryFactory                                               $categoryFactory
     * @param \Magento\Framework\UrlInterface                               $urlBuilder
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface              $categoryRepository
     * @param \Magento\Store\Model\StoreManagerInterface                    $storeManager
     * @param \Magento\Framework\Model\Context                              $context
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null  $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null            $resourceCollection
     * @param array                                                         $data
     */
    public function __construct(
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
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->imageFactory = $imageFactory;
        $this->countyFactory = $countyFactory;
        $this->typesFactory = $typesFactory;
        $this->suppliersFactory = $suppliersFactory;
        $this->filterProvider = $filterProvider;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
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
        return $this->getData('school_name');
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
        return $this->getData('postcode');
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
        return $this->urlBuilder->getUrl('school/register-interest',
            [
                'id' => $this->getId()
            ]);
    }

    /**
     * @param bool $address1
     * @param bool $address2
     * @param bool $address3
     * @param bool $full_address
     *
     * @return array
     */
    public function getAddress($address1 = true, $address2 = true, $address3 = true, $full_address = false)
    {
        $address = [];
        $addressLine1 = !empty($this->getData('address_1')) ? $this->getData('address_1') : null;
        $addressLine2 = !empty($this->getData('address_2')) ? $this->getData('address_2') : null;
        $addressLine3 = !empty($this->getData('address_3')) ? $this->getData('address_3') : null;

        array_push($address, (!is_null($addressLine1)) ? $this->getData('address_1') : '');
        array_push($address, (!is_null($addressLine2)) ? $this->getData('address_2') : '');
        array_push($address, (!is_null($addressLine3)) ? $this->getData('address_3') : '');

        if ($full_address)
        {
            array_push($address, $this->getTown());
            array_push($address, $this->getCounty());

        }
        array_push($address, $this->getData('postcode'));
        if ($full_address)
            array_push($address, 'United Kingdom');

        return $address;
    }

    public function getAddressForSearch()
    {
        return $this->getData('address_1') . ', ' . $this->getData('address_2') . ', ' . $this->getData('town') . ', ' . $this->getData('postcode');
    }


    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getData('is_active');
    }

    /**
     * @return string
     */
    public function getTown()
    {
        return $this->getData('town');
    }

    /**
     * @return object
     */
    public function getCounty()
    {
        $county = $this->countyFactory->create()->load($this->getData('county_id'));
        return $county;
    }

    /**
     * @return object
     */
    public function getType()
    {
        $schoolType = $this->typesFactory->create()->load($this->getData('school_type_idno'));
        return $schoolType;
    }

    /**
     * @return object
     */
    public function getSupplier()
    {
        $schoolSupplier = $this->suppliersFactory->create()->load($this->getData('school_supplier_idno'));
        return $schoolSupplier;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->getData('tel');
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->getImageSrc('logo');
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->getData('website_address');
    }

    /**
     * @return int
     */
    public function getPupilsOnRoll()
    {
        return $this->getData('pupils_on_roll');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData('first_name') . $this->getData('last_name');
    }

    public function getFirstName()
    {
        return $this->getData('first_name');
    }

    public function getSurname()
    {
        return $this->getData('last_name');
    }

    /**
     * @return string
     */
    public function getJobTitle()
    {
        return $this->getData('job_title');
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getData('email');
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        try {
            return $this->filterProvider->getPageFilter()->filter($this->getData('notes'));
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * @return bool
     */
    public function isActiveCustomer()
    {
        return $this->getData('active_customer');
    }
    /**
     * @return bool
     */
    public function isEnabledParentOrder()
    {
        return $this->getData('enable_parent_order');
    }
    /**
     * @return bool
     */
    public function isEnabledSchoolFranOrder()
    {
        return $this->getData('enable_school_fran_order');
    }

    /**
     * @return bool
     */
    public function chargePostage()
    {
        return $this->getData('charge_postage');
    }
    /**
     * @return bool
     */
    public function chargePostageToSchool()
    {
        return $this->getData('charge_postage_school');
    }
    /**
     * @return bool
     */
    public function chargePostageToFranchisee()
    {
        return $this->getData('charge_postage_franchisee');
    }
    /**
     * @return bool
     */
    public function showSchool()
    {
        return $this->getData('show_school');
    }
    /**
     * @return bool
     */
    public function isEnabledBulkDelivery()
    {
        return $this->getData('enable_bulk_delivery');
    }
    /**
     * @return bool
     */
    public function isEnabledBulkDiscounts()
    {
        return $this->getData('enable_bulk_discounts');
    }
    /**
     * @return bool
     */
    public function isEnabledBulkAdress()
    {
        return $this->getData('enable_bulk_address');
    }
    /**
     * Is PPP enabled for schools.
     *
     * @return bool
     */
    public function isSchoolEnabledPPP()
    {
        return $this->getData('enable_ppp_school');
    }
    /**
     * Is PPP enabled for parents.
     *
     * @return bool
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
     * @return $this|bool
     */
    public function saveCollection(array $data)
    {
        if (isset($data[$this->getId()])) {
            $this->addData($data[$this->getId()]);
            $this->getResource()->save($this);
        }
        return $this;
    }

    public function getImageValueForForm(string $imageAttrCode): array
    {
        /** @var ImageAbstract $image */
        $image = $this->imageFactory->create($imageAttrCode);
        return $image->getFileValueForForm($this);
    }

    /**
     * @param string $imageAttrCode
     * @return mixed
     */
    public function getImageSrc(string $imageAttrCode)
    {
        /** @var ImageAbstract $image */
        $image = $this->imageFactory->create($imageAttrCode);
        return $image->getFileInfo($this)->getUrl();
    }

}
