<?php
    /**
     * Thumbnail
     *
     * @copyright Copyright © 2018 Kinspeed. All rights reserved.
     * @author    luke.paoloni@kinspeed.com
     */

    namespace Kinspeed\Schools\Ui\Component\Listing\Column;

    use Magento\Catalog\Helper\Image;
    use Magento\Framework\UrlInterface;
    use Magento\Framework\View\Element\UiComponentFactory;
    use Magento\Framework\View\Element\UiComponent\ContextInterface;
    use Magento\Store\Model\StoreManagerInterface;
    use Magento\Ui\Component\Listing\Columns\Column;

    class Thumbnail extends Column
    {
        const ALT_FIELD = 'title';

        /**
         * Url path
         */
        const URL_PATH_EDIT = 'kinspeed_schools/school/edit';

        /**
         * @var \Magento\Store\Model\StoreManagerInterface
         */
        protected $storeManager;

        /**
         * @param ContextInterface $context
         * @param UiComponentFactory $uiComponentFactory
         * @param Image $imageHelper
         * @param UrlInterface $urlBuilder
         * @param StoreManagerInterface $storeManager
         * @param array $components
         * @param array $data
         */
        public function __construct(
            ContextInterface $context,
            UiComponentFactory $uiComponentFactory,
            Image $imageHelper,
            UrlInterface $urlBuilder,
            StoreManagerInterface $storeManager,
            array $components = [],
            array $data = []
        ) {
            $this->storeManager = $storeManager;
            $this->imageHelper = $imageHelper;
            $this->urlBuilder = $urlBuilder;
            parent::__construct($context, $uiComponentFactory, $components, $data);
        }

        /**
         * Prepare Data Source
         *
         * @param array $dataSource
         * @return array
         */
        public function prepareDataSource(array $dataSource)
        {
            if(isset($dataSource['data']['items'])) {
                $fieldName = $this->getData('name');
                $storeId = $this->context->getFilterParam('store_id');
                foreach($dataSource['data']['items'] as & $item) {
                    $url = '';
                    if(isset($item['logo'])) {
                        $url = $this->storeManager->getStore()->getBaseUrl(
                                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                            ).'kinspeed_schools/files/logo/'.$item['entity_id']. '/'.$item[$fieldName];

                        $item[$fieldName . '_src'] = $url;
                        $item[$fieldName . '_alt'] = $this->getAlt($item) ?: '';
                        $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                            self::URL_PATH_EDIT,
                            ['entity_id' => $item['entity_id'], 'store' => $storeId]
                        );
                        $item[$fieldName . '_orig_src'] = $url;
                    }
                }
            }

            return $dataSource;
        }

        /**
         * @param array $row
         *
         * @return null|string
         */
        protected function getAlt($row)
        {
            $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
            return isset($row[$altField]) ? $row[$altField] : null;
        }
    }