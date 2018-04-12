<?php
/**
 * Search
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */

namespace Kinspeed\Schools\Block\SchoolFinder;

use Magento\Framework\View\Element\Template;

class Search extends Template
{
    /**
     * @var string $_template
     */
    protected $_template = "Kinspeed_Schools::school-finder/search.phtml";
    /**
     * @var \Magento\Framework\Registry
     */
    private $_coreRegistry;

    /**
     * Construct
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $_coreRegistry
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $_coreRegistry,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_coreRegistry = $_coreRegistry;
    }

    /**
     * Get form action URL for POST booking request
     *
     * @return string
     */
    public function getFormAction()
    {
        // companymodule is given in routes.xml
        // controller_name is folder name inside controller folder
        // action is php file name inside above controller_name folder

        return $this->getBaseUrl() . 'schoolsearch/result/';
        // here controller_name is index, action is booking
    }

    public function getItems()
    {
        return $this->_coreRegistry->registry('school_results');
    }
}