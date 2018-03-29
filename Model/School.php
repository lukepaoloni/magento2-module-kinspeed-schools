<?php

/**
 * School.php
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */

namespace Kinspeed\Schools\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Registry;
use Kinspeed\Schools\Model\School\Attribute\Backend\ImageAbstract;
use Kinspeed\Schools\Model\School\Attribute\Backend\ImageFactory;

class School extends AbstractModel implements IdentityInterface
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG       = 'kinspeed_schools_school';
    const STATUS_APPROVED = true;
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
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Kinspeed\Schools\Model\ResourceModel\School');
    }

    public function __construct(
        ImageFactory $imageFactory,
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
    }

    public function getName()
    {
        return 'test';
    }

    public function getUrl()
    {
        return 'test';
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
