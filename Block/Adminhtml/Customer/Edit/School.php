<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Block\Adminhtml\Customer\Edit;

    use Kinspeed\Schools\Model\SchoolFactory;
    use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
    use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

    class School extends GenericButton implements ButtonProviderInterface
    {
        /**
         * @var \Magento\Framework\AuthorizationInterface
         */
        protected $_authorization;
        /**
         * @var \Magento\Customer\Model\CustomerFactory
         */
        private $customerFactory;
        /**
         * @var \Kinspeed\Schools\Model\SchoolFactory
         */
        private $schoolFactory;

        /**
         * Constructor
         *
         * @param \Magento\Backend\Block\Widget\Context   $context
         * @param \Magento\Framework\Registry             $registry
         * @param \Magento\Customer\Model\CustomerFactory $customerFactory
         * @param \Kinspeed\Schools\Model\SchoolFactory   $schoolFactory
         */
        public function __construct(
            \Magento\Backend\Block\Widget\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Customer\Model\CustomerFactory $customerFactory,
            \Kinspeed\Schools\Model\SchoolFactory $schoolFactory
        ) {
            parent::__construct($context, $registry);
            $this->_authorization = $context->getAuthorization();
            $this->customerFactory = $customerFactory;
            $this->schoolFactory = $schoolFactory;
        }

        /**
         * Retrieve button-specified settings
         *
         * @return array
         */
        public function getButtonData()
        {
            $customerId = $this->getCustomerId();
            $data = [];
            $customer = $this->customerFactory->create()->load($customerId);
            $school = $this->schoolFactory->create()->load($customer->getData('linked_school'));
            $canModify = $customerId && $this->_authorization->isAllowed('Kinspeed_Schools::school') && $school;
            if ($canModify) {
                $url= $this->getUrl(
                    'kinspeed_schools/school/edit',
                    ['entity_id' => $school->getId()]
                );
                $data = [
                    'label' => __('View School'),
                    'on_click' => sprintf("location.href = '%s';", $url),
                    'class' => 'add',
                    'sort_order' => 0,
                ];
            }
            return $data;
        }
    }