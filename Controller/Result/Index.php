<?php
/**
 * Index
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */
 
namespace Kinspeed\Schools\Controller\Result;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory as SchoolCollection;


class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory
     */
    private $schoolCollection;
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    private $_coreRegistry;

    /**
     * @param \Magento\Framework\Controller\Result\JsonFactory               $resultJsonFactory
     * @param \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory $schoolCollection
     * @param Context                                                        $context
     * @param PageFactory                                                    $pageFactory
     * @param \Magento\Framework\Controller\ResultFactory                    $resultFactory
     * @param \Magento\Catalog\Model\CategoryFactory                         $categoryFactory
     * @param \Magento\Framework\Registry                                    $_coreRegistry
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        SchoolCollection $schoolCollection,
        Context $context,
        PageFactory $pageFactory,
        ResultFactory $resultFactory,
        CategoryFactory $categoryFactory,
        Registry $_coreRegistry
    )
    {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->schoolCollection = $schoolCollection;
        $this->resultFactory = $resultFactory;
        $this->categoryFactory = $categoryFactory;
        $this->_coreRegistry = $_coreRegistry;
    }

    /**
     * Index Action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $query = $this->getRequest()->getParam('q');
        $result = $this->resultJsonFactory->create();
        $resultRedirect = $this->resultRedirectFactory->create();

        $schoolCollection = $this->schoolCollection->create();
        $category = $this->categoryFactory->create()->loadByAttribute('name', 'SchoolFinder');
        try {
            $schoolCollection->addAttributeToSelect('*');
            $schoolCollection->addAttributeToFilter('school_name',
                [
                    'like' => '%'.$query.'%'
                ]);
            $schoolCollection->addAttributeToFilter('show_school', true);
            $result->setData(
                [
                    $schoolCollection->getData()
                ]);
        }
        catch (LocalizedException $e) {
            $result->setData(
                [
                    'query' => $query,
                    'error' => $e->getMessage()
                ]
            );
        }
        $this->_coreRegistry->register('school_results', $schoolCollection);
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->set('Search results for: \''. $query . '\'');
        return $resultPage;
    }
}
