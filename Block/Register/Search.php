<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Block\Register;

    use Magento\Framework\View\Element\Template;

    class Search extends Template
    {
        /**
         * @var string $_template
         */
        protected $_template = "Kinspeed_Schools::register/account/search.phtml";
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
            return $this->getBaseUrl() . 'school/finder/';
        }

        public function getItems()
        {
            $results = $this->_coreRegistry->registry('school_results');
            return $results;
        }

        public function getAjaxUrl()
        {
            return $this->getUrl('school/register/search');
        }
    }