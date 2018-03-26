<?php

/**
 * Types.php
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */
namespace Kinspeed\Schools\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Types extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('kinspeed_schools_school_types', 'entity_id');
    }
}
