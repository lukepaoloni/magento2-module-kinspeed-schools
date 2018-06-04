<?php
    /**
     * Index
     *
     * @copyright Copyright © 2017 Kinspeed. All rights reserved.
     * @author    luke.paoloni@kinspeed.com
     */

    namespace Kinspeed\Schools\Controller\Finder;

    use Magento\Catalog\Model\CategoryFactory;
    use Magento\Framework\App\Action\Action;
    use Magento\Framework\Controller\Result\JsonFactory;
    use Magento\Framework\Controller\ResultFactory;
    use Magento\Framework\Escaper;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Registry;
    use Magento\Framework\View\Result\PageFactory;
    use Magento\Framework\App\Action\Context;
    use \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory as SchoolCollection;
    use \Mirasvit\SearchElastic\Model\Engine as ElasticSearchEngine;

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
         * @var \Magento\Framework\Escaper
         */
        private $_escaper;
        /**
         * @var \Mirasvit\SearchElastic\Model\Engine
         */
        private $engine;

        /**
         * @param \Magento\Framework\Controller\Result\JsonFactory               $resultJsonFactory
         * @param \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory $schoolCollection
         * @param Context                                                        $context
         * @param PageFactory                                                    $pageFactory
         * @param \Magento\Framework\Controller\ResultFactory                    $resultFactory
         * @param \Magento\Catalog\Model\CategoryFactory                         $categoryFactory
         * @param \Magento\Framework\Registry                                    $_coreRegistry
         * @param \Magento\Framework\Escaper                                     $_escaper
         * @param \Mirasvit\SearchElastic\Model\Engine                           $engine
         */
        public function __construct(
            JsonFactory $resultJsonFactory,
            SchoolCollection $schoolCollection,
            Context $context,
            PageFactory $pageFactory,
            ResultFactory $resultFactory,
            CategoryFactory $categoryFactory,
            Registry $_coreRegistry,
            Escaper $_escaper,
            ElasticSearchEngine $engine
        )
        {
            $this->pageFactory = $pageFactory;
            parent::__construct($context);
            $this->resultJsonFactory = $resultJsonFactory;
            $this->schoolCollection  = $schoolCollection;
            $this->resultFactory     = $resultFactory;
            $this->categoryFactory   = $categoryFactory;
            $this->_coreRegistry     = $_coreRegistry;
            $this->_escaper          = $_escaper;
            $this->engine            = $engine;
        }

        /**
         * Index Action
         *
         * @return \Magento\Framework\Controller\Result\Json|\Magento\Framework\View\Result\Page
         */
        public function execute()
        {
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage    = $this->pageFactory->create();
            $query         = $this->_escaper->escapeHtml($this->getRequest()->getParam('term'));
	        $resultJson = $this->resultJsonFactory->create();
            if ($query) {
                $elasticSearch = $this->engine;
                $data          = [
                    'index' => 'schools',
                    'type'  => 'school',
                    'body'  => [
                        'query' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => ['school_name' => $this->_escaper->escapeHtml($query)]
                                    ],
                                    [
                                        'match' => ['postcode' => $this->_escaper->escapeHtml($query)]
                                    ],
                                    [
                                        'match' => ['town' => $this->_escaper->escapeHtml($query)]
                                    ],
                                ]
                            ]
                        ]
                    ]
                ];
                $esQuery       = $elasticSearch->getClient()->search($data);
                $returnResults = $esQuery['hits']['hits'];
                $returnArray   = [];
                $ajaxResult    = [];
                foreach ($returnResults as $returnResult) {
	                $url = isset($returnResult['_source']['url']) ? $returnResult['_source']['url'] : null;
	                $long = isset($returnResult['_source']['longitude']) ? $returnResult['_source']['longitude'] : null;
	                $lat = isset($returnResult['_source']['latitude']) ? $returnResult['_source']['latitude'] : null;
                    $returnArray[] = [
                        'school_name' => $returnResult['_source']['school_name'],
                        'address_1'   => $returnResult['_source']['address_1'],
                        'address_2'   => $returnResult['_source']['address_2'],
                        'address_3'   => $returnResult['_source']['address_3'],
                        'town'        => $returnResult['_source']['town'],
                        'postcode'    => $returnResult['_source']['postcode'],
                        'logo'        => $returnResult['_source']['logo'],
                        'url'         => $url,
                        'longitude'   => $long,
                        'latitude'    => $lat
                    ];
                    $address = $returnResult['_source']['address_1'];
                    $address .= $returnResult['_source']['address_2'] ? ' ' . $returnResult['_source']['address_2'] : '';
                    $ajaxResult[]  = [
                        'label' => $returnResult['_source']['school_name'],
                        'id' => $returnResult['_id'],
                        'value' => $returnResult['_source']['school_name'] .
                            ', ' .
                            $returnResult['_source']['address_1'] .
                            ', ' .
                            $returnResult['_source']['town'] .
                            ', ' .
                            $returnResult['_source']['postcode'],
                        'longitude' => $long,
                        'latitude' => $lat,
                        'school' => [
                            'name' => $returnResult['_source']['school_name'],
                            'address' => [
                                'street' => $address,
                                'city' => $returnResult['_source']['town'],
                                'postcode' => $returnResult['_source']['postcode']
                            ],
                            'logo' => $returnResult['_source']['logo']
                        ]
                    ];
                }
                if ($this->getRequest()->isAjax()) {
                    return $resultJson->setData($ajaxResult);
                }
                else {
                    $this->_coreRegistry->register('school_results', $returnArray);
                }
            }
            return $resultPage;
        }
    }
