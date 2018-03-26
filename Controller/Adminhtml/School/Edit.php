<?php
/**
 * Edit.php
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */
namespace Kinspeed\Schools\Controller\Adminhtml\School;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Kinspeed\Schools\Model\SchoolFactory;
use Kinspeed\Schools\Model\TypesFactory;

class Edit extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /** @var schoolFactory $objectFactory */
    protected $objectFactory;
    /**
     * @var \Kinspeed\Schools\Model\TypesFactory
     */
    private $typesFactory;
    
    /**
     * @param Context                              $context
     * @param PageFactory                          $resultPageFactory
     * @param Registry                             $registry
     * @param SchoolFactory                        $objectFactory
     * @param \Kinspeed\Schools\Model\Types $typesFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        SchoolFactory $objectFactory,
        TypesFactory $typesFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->objectFactory = $objectFactory;
        $this->typesFactory = $typesFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Kinspeed_Schools::school');
    }

    /**
     * Edit
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        
        // 1. Get ID
        $id = $this->getRequest()->getParam('entity_id');
        $objectInstance = $this->objectFactory->create();

        // 2. Initial checking
        if ($id) {
            $objectInstance->load($id);
            if (!$objectInstance->getId()) {
                $this->messageManager->addErrorMessage(__('This record no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $objectInstance->addData($data);
        }

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('entity_id', $id);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Kinspeed_Schools::school');
        $resultPage->getConfig()->getTitle()->prepend(__('School Edit'));

        return $resultPage;
    }
}
