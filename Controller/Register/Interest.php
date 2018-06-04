<?php
/**
 * Index
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */
 
namespace Kinspeed\Schools\Controller\Register;

use Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory;
use Kinspeed\Schools\Model\SchoolRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;

class Interest extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;
    /**
     * @var \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var \Kinspeed\Schools\Model\SchoolRepository
     */
    private $schoolRepository;
    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Kinspeed\Schools\Model\SchoolRepository   $schoolRepository
     * @param \Magento\Framework\Escaper                 $escaper
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     */
    public function __construct(
        Registry $coreRegistry,
        SchoolRepository $schoolRepository,
        Escaper $escaper,
        Context $context,
        PageFactory $pageFactory
    )
    {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
        $this->schoolRepository = $schoolRepository;
        $this->escaper = $escaper;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Index Action
     * 
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->pageFactory->create();
        $params = $this->getRequest()->getParams();
        $title = 'Register School Interest';

        if (isset($params['id']) && !empty($params['id'])) {
            $id = (int) $params['id'];
            $id = $this->escaper->escapeHtml($id);
            try {
                $school = $this->schoolRepository->getById($id);
                if (!$school->isActiveCustomer()) {
                    $title = 'Register School Interest: ' . $school->getSchoolName();
                    $this->coreRegistry->register('school', $school);
                }
            }
            catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
            catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
        }
        $resultPage->getConfig()->getTitle()->set(__($title));
        return $resultPage;
    }
}
