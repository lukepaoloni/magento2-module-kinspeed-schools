<?php

/**
 * Types.php
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */

namespace Kinspeed\Schools\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Types extends AbstractModel implements IdentityInterface
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'kinspeed_schools_school_types';

    /**
     * @var string
     */
    protected $_cacheTag = 'kinspeed_schools_school_types';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'kinspeed_schools_school_types';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Kinspeed\Schools\Model\ResourceModel\Types');
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
}
