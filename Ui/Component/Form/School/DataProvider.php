<?php
/**
 * DataProvider
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */
namespace Kinspeed\Schools\Ui\Component\Form\School;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Kinspeed\Schools\Model\School;
use Kinspeed\Schools\Model\School\Attribute\Backend\ImageFactory;
use Kinspeed\Schools\Model\ResourceModel\School\Collection;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;
    
    /**
     * @var FilterPool
     */
    protected $filterPool;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Collection $collection
     * @param FilterPool $filterPool
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $collection,
        FilterPool $filterPool,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection;
        $this->filterPool = $filterPool;
        $this->request = $request;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->loadedData) {
            $storeId = (int)$this->request->getParam('store');
            $school = $this->collection
                ->setStoreId($storeId)
                ->addAttributeToSelect('*')
                ->getFirstItem();
            $school->setStoreId($storeId);
            $school->addData($this->schoolImagesData($school));
            $this->loadedData[$school->getId()] = $school->getData();
        }
        return $this->loadedData;
    }

    private function schoolImagesData(School $school): array
    {
        $imagesData = [];
        $imageAttributeCodes = array_keys(ImageFactory::IMAGE_ATTRIBUTE_CODES);
        foreach ($imageAttributeCodes as $imageAttrCode) {
            if ($school->getData($imageAttrCode)) {
                $imagesData[$imageAttrCode] = $school->getImageValueForForm($imageAttrCode);
            }
        }
        return $imagesData;
    }
}
