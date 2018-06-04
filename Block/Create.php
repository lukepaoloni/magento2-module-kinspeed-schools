<?php
/**
 * Register
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */

namespace Kinspeed\Schools\Block;

use Magento\Customer\Block\Form\Register;

class Create extends Register
{
    /**
     * @var string $_template
     */
    protected $_template = "create.phtml";

    
    protected function _prepareLayout ()
    {
        $this->pageConfig->getTitle()->set(__('Register For A School Account'));
    }
}