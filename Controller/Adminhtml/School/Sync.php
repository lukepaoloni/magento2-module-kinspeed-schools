<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Controller\Adminhtml\School;

    use Magento\Backend\App\Action;
    use Magento\Backend\App\Action\Context;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\View\Result\PageFactory;
    use Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory;
    use \Mirasvit\SearchElastic\Model\Engine;

    class Sync extends Action
    {
        /**
         * @var \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory
         */
        private $collectionFactory;

        /**
         * @var \Mirasvit\SearchElastic\Model\Engine
         */
        private $engine;
        /**
         * @var PageFactory
         */
        protected $resultPageFactory;

        /**
         * @param Context                                                        $context
         * @param PageFactory                                                    $resultPageFactory
         * @param \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory $collectionFactory
         * @param \Mirasvit\SearchElastic\Model\Engine                           $engine
         */
        public function __construct(
            Context $context,
            PageFactory $resultPageFactory,
            CollectionFactory $collectionFactory,
            Engine $engine
        ) {
            parent::__construct($context);
            $this->resultPageFactory = $resultPageFactory;
            $this->collectionFactory = $collectionFactory;
            $this->engine = $engine;
        }

        /**
         * Check the permission to run it
         *
         * @return boolean
         */
        protected function _isAllowed()
        {
            return $this->_authorization->isAllowed('Kinspeed_Schools::school');
        }

        /**
         * Index action
         *
         * @return \Magento\Framework\Controller\Result\Redirect
         */
        public function execute()
        {
            $elastic = $this->engine->getClient();
            $collection = $this->collectionFactory->create();
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($this->getUrl('*/*/'));
            try {
                $collection->addAttributeToSelect('*');
                $collection->addAttributeToFilter('is_active', true);
                /** @var \Kinspeed\Schools\Model\School $school */
                foreach ($collection as $school) {
                    if (!empty($school->getName())) {
                        $params =
                            [
                                'index' => 'schools',
                                'id'    => $school->getId(),
                                'type'  => 'school',
                                'body'  => [
                                    'school_id' => $school->getId(),
                                    'school_name' => $school->getSchoolName(),
                                    'address_1'   => $school->getAddress1(),
                                    'address_2'   => $school->getAddress2(),
                                    'address_3'   => $school->getAddress3(),
                                    'town'        => $school->getTown(),
                                    'postcode'    => $school->getPostcode(),
                                    'logo'        => $school->getLogo(),
                                    'url'         => $school->getPath()
                                ]
                            ];
                        $elastic->index($params);
                    }
                }
                $this->messageManager->addSuccessMessage('Successfully synced School collection with SchoolFinder');
            }
            catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage('Error: ' . $e->getMessage());
            }
            return $resultRedirect;
        }
    }
