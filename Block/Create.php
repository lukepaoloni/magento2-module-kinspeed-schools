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
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;
    
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;
    
    /**
     * Create constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context                 $context
     * @param \Magento\Directory\Helper\Data                                   $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface                         $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config                         $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory  $regionCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Framework\Module\Manager                                $moduleManager
     * @param \Magento\Customer\Model\Session                                  $customerSession
     * @param \Magento\Customer\Model\Url                                      $customerUrl
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = []
    ) {
        $this->_customerUrl = $customerUrl;
        $this->_moduleManager = $moduleManager;
        $this->_customerSession = $customerSession;
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $data
        );
        $this->_isScopePrivate = false;
    }
    

    
    protected function _prepareLayout ()
    {
        $this->pageConfig->getTitle()->set(__('Register For A School Account'));
    }
    public function getPostActionUrl ()
    {
        return parent::getPostActionUrl();
    }
    
    public function getFormData ()
    {
        return parent::getFormData();
    }
    
    public function isNewsletterEnabled()
    {
        return parent::isNewsletterEnabled();
    }
}