<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Block\Adminhtml\Customer\Edit;

    use Magento\Customer\Api\CustomerRepositoryInterface;
    use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

    class Category extends GenericButton implements ButtonProviderInterface
    {
        /**
         * @var \Magento\Framework\AuthorizationInterface
         */
        protected $_authorization;
        /**
         * @var \Magento\Catalog\Model\CategoryFactory
         */
        private $categoryFactory;
        /**
         * @var \Magento\Customer\Api\CustomerRepositoryInterface
         */
        private $customerFactory;

        /**
         * Constructor
         *
         * @param \Magento\Catalog\Model\CategoryFactory  $categoryFactory
         * @param \Magento\Customer\Model\CustomerFactory $customerFactory
         * @param \Magento\Backend\Block\Widget\Context   $context
         * @param \Magento\Framework\Registry             $registry
         */
        public function __construct(
            \Magento\Catalog\Model\CategoryFactory $categoryFactory,
            \Magento\Customer\Model\CustomerFactory $customerFactory,
            \Magento\Backend\Block\Widget\Context $context,
            \Magento\Framework\Registry $registry
        ) {
            parent::__construct($context, $registry);
            $this->_authorization = $context->getAuthorization();
            $this->categoryFactory = $categoryFactory;
            $this->customerFactory = $customerFactory;
        }

        /**
         * Retrieve button-specified settings
         *
         * @return array|string
         */
        public function getButtonData()
        {
            $customerId = $this->getCustomerId();
            $data = [];
            try {
                $customer = $this->customerFactory->create()->load($customerId);
                $category = $this->categoryFactory->create()->loadByAttribute('linked_school', $customer->getData('linked_school'));
                $authorize = $this->_authorization->isAllowed('Magento_Catalog::category');
                $canModify = $customerId && $authorize && $category;
                if ($canModify) {
                    $url= $this->getUrl(
                       'catalog/category/edit',
                     ['id' => $category->getId()]
                    );
                    $data = [
                        'label' => __('View Category'),
                        'on_click' => sprintf("location.href = '%s';", $url),
                        'class' => 'add',
                        'sort_order' => 0,
                    ];
            }
            return $data;
            }
            catch (NoSuchEntityException $e) {
                return $e->getMessage();
            }
            catch (LocalizedException $e) {
                return $e->getMessage();
            }
        }
    }