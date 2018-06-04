<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Block\Register;

    use Kinspeed\Schools\Model\SchoolRepository;
    use Magento\Framework\Data\Form\FormKey;
    use Magento\Framework\Registry;
    use Magento\Framework\View\Element\Template;
    use Magento\Framework\View\Element\Template\Context;

    class Interest extends Template
    {
        /**
         * @var string $_template
         */
        protected $_template = "register/interest.phtml";
        /**
         * @var \Magento\Framework\Registry
         */
        private $coreRegistry;
        /**
         * @var \Kinspeed\Schools\Model\SchoolRepository
         */
        private $schoolRepository;
        /**
         * @var \Magento\Framework\Data\Form\FormKey
         */
        private $formKey;

        // write your methods here...

        /**
         * Interest constructor.
         *
         * @param \Magento\Framework\Data\Form\FormKey             $formKey
         * @param \Magento\Framework\Registry                      $coreRegistry
         * @param \Kinspeed\Schools\Model\SchoolRepository         $schoolRepository
         * @param \Magento\Framework\View\Element\Template\Context $context
         * @param array                                            $data
         */
        public function __construct(
            FormKey $formKey,
            Registry $coreRegistry,
            SchoolRepository $schoolRepository,
            Context $context,
            array $data = []
        )
        {
            parent::__construct($context, $data);
            $this->coreRegistry = $coreRegistry;
            $this->schoolRepository = $schoolRepository;
            $this->formKey = $formKey;
        }

        public function getSchool()
        {
            return $this->coreRegistry->registry('school');
        }

        public function getFormAction()
        {
            return $this->_urlBuilder->getUrl('schools/interest/post');
        }

        public function getFormKey()
        {
            return $this->formKey->getFormKey();
        }
    }