<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Block\Register;

    use Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory;
    use Magento\Framework\Data\Form\FormKey;
    use Magento\Framework\View\Element\Template;
    use Magento\Framework\View\Element\Template\Context;

    class Account extends Template
    {
        /**
         * @var string $_template
         */
        protected $_template = "register/account/search.phtml";
        /**
         * @var \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory
         */
        private $collectionFactory;
        /**
         * @var \Magento\Framework\Data\Form\FormKey
         */
        private $formKey;

        /**
         * Account constructor.
         *
         * @param \Magento\Framework\Data\Form\FormKey                           $formKey
         * @param \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory $collectionFactory
         * @param \Magento\Framework\View\Element\Template\Context               $context
         * @param array                                                          $data
         */
        public function __construct(
            FormKey $formKey,
            CollectionFactory $collectionFactory,
            Context $context,
            array $data = []
        )
        {
            parent::__construct($context, $data);
            $this->collectionFactory = $collectionFactory;
            $this->formKey = $formKey;
        }
        public function getPostActionUrl()
        {
            return $this->getUrl('school/account/create');
        }

        public function getFormKey()
        {
            return $this->formKey->getFormKey();
        }
    }