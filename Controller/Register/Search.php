<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Controller\Register;

    use Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory;
    use Kinspeed\Schools\Model\School;
    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Action\Context;
    use Magento\Framework\Controller\Result\JsonFactory;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Escaper;

    class Search extends Action
    {
        /**
         * @var \Kinspeed\Schools\Controller\Ajax\JsonFactory
         */
        private $resultJsonFactory;
        /**
         * @var \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory
         */
        private $collectionFactory;
        /**
         * @var \Magento\Framework\Escaper
         */
        private $escaper;

        /**
         * @param Context                                                        $context
         * @param \Magento\Framework\Controller\Result\JsonFactory               $resultJsonFactory
         * @param \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory $collectionFactory
         * @param \Magento\Framework\Escaper                                     $escaper
         */
        public function __construct(
            Context $context,
            JsonFactory $resultJsonFactory,
            CollectionFactory $collectionFactory,
            Escaper $escaper
        )
        {
            parent::__construct($context);
            $this->resultJsonFactory = $resultJsonFactory;
            $this->collectionFactory = $collectionFactory;
            $this->escaper = $escaper;
        }

        /**
         * Index Action
         *
         * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
         */
        public function execute()
        {
            $query = '%' . $this->escaper->escapeHtml(strtolower($this->getRequest()->getParam('term'))) . '%';
            $json = [];
            $redirect = $this->resultRedirectFactory->create();
            if (!$this->getRequest()->isAjax()) {
                return $redirect->setUrl($this->_url->getUrl('school/register/account'));
            }
            try {

                $collection = $this->collectionFactory->create()
                                    ->addAttributeToSelect('*')
                                    ->addAttributeToFilter(School::SCHOOL_NAME, ['like' => $query])
                                    ->addAttributeToFilter(School::ACTIVE_CUSTOMER, false);
                foreach ($collection as $school) {
                    /** @var \Kinspeed\Schools\Model\School $school */
                    if (!$school->isActive())
                        continue;

                    $json[] = [
                        'id' => $school->getId(),
                        'label' => $school->getSchoolName(),
                        'value' => $school->getSchoolName(),
                        'name' => $school->getSchoolName(),
                        'link' => $school->getPath(),
                        'image' => $school->getLogo(),
                        'address' => $school->getAddress(),
                        'city' => $school->getTown(),
                        'postcode' => $school->getPostcode(),
                    ];
                }
            }
            catch (LocalizedException $e) {
                $json = ['error' => $e->getMessage()];
            }
            return $this->resultJsonFactory->create()->setData($json);
        }
    }