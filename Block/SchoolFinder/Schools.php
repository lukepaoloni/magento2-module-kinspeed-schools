<?php

namespace Kinspeed\Schools\Block\SchoolFinder;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use \Kinspeed\Schools\Model\ResourceModel\School\Collection as SchoolCollection;
use Magento\Store\Model\ScopeInterface;

class Schools extends Template
{
    /**
     * @var string
     */
    const MAP_STYLES_CONFIG_PATH = 'kinspeed_schools/school_map/map_style';

    /**
     * @var string
     */
    const MAP_PIN_CONFIG_PATH = 'kinspeed_schools/school_map/map_pin';

    /**
     * @var string
     */
    const ASK_LOCATION_CONFIG_PATH = 'kinspeed_schools/school_map/ask_location';


    /**
     * @var string
     */
    const API_KEY_CONFIG_PATH = 'kinspeed_schools/school_map/api_key';

    /**
     * @var string
     */
    const UNIT_LENGTH_CONFIG_PATH = 'kinspeed_schools/school_map/unit_length';

    /**
     * @var int
     */
    const LATITUDE_CONFIG_PATH = 'kinspeed_schools/school_map/latitude';

    /**
     * @var int
     */
    const LONGITUDE_CONFIG_PATH = 'kinspeed_schools/school_map/longitude';

    /**
     * @var int
     */
    const ZOOM_CONFIG_PATH = 'kinspeed_schools/school_map/zoom';


    /**
     * @var string
     */
    const RADIUS_CONFIG_PATH = 'kinspeed_schools/school_map/radius';

    /**
     * @var string
     */
    const STROKE_WEIGHT_CONFIG_PATH = 'kinspeed_schools/school_radius/circle_stroke_weight';

    /**
     * @var string
     */
    const STROKE_OPACITY_CONFIG_PATH = 'kinspeed_schools/school_radius/circle_stroke_opacity';

    /**
     * @var string
     */
    const STROKE_COLOR_CONFIG_PATH = 'kinspeed_schools/school_radius/circle_stroke_color';

    /**
     * @var string
     */
    const FILL_OPACITY_CONFIG_PATH = 'kinspeed_schools/school_radius/circle_fill_opacity';

    /**
     * @var string
     */
    const FILL_COLOR_CONFIG_PATH = 'kinspeed_schools/school_radius/circle_fill_color';
    /**
     * @var string $_template
     */
    protected $_template = "Kinspeed_Schools::school-finder/search.phtml";
    /**
     * @var \Magento\Framework\Registry
     */
    private $_coreRegistry;
    /**
     * @var \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory
     */
    private $schoolCollection;

    /**
     * Construct
     *
     * @param \Magento\Backend\Block\Template\Context                 $context
     * @param \Magento\Framework\Registry                             $_coreRegistry
     * @param \Kinspeed\Schools\Model\ResourceModel\School\Collection $schoolCollection
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $_coreRegistry,
        SchoolCollection $schoolCollection,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_coreRegistry = $_coreRegistry;
        $this->schoolCollection = $schoolCollection;
    }

    public function getSchools($paging = null)
    {
        $schoolCollection = $this->schoolCollection;

        try {
            $schoolCollection->addAttributeToSelect('*');
            $schoolCollection->addAttributeToFilter('show_school', true);
            $schoolCollection->addAttributeToSort('school_name', 'ASC');
            if ($paging) {
                //get values of current page
                $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
                //get values of current limit
                $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 15;
                $schoolCollection->setPageSize($pageSize);
                $schoolCollection->setCurPage($page);
            }
            return $schoolCollection;
        }
        catch (LocalizedException $e) {
            return $e->getMessage();
        }
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('All Schools'));
        if ($this->getSchools()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'kinspeed.schools.pager'
            )->setAvailableLimit(array(5=>5,10=>10,15=>15))->setShowPerPage(true)->setCollection(
                $this->getSchools()
            );
            $this->setChild('pager', $pager);
            $this->getSchools()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * get map style from configuration
     *
     * @return string
     */
    public function getMapStyles(): string
    {
        return $this->_scopeConfig->getValue(self::MAP_STYLES_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get map pin from configuration
     *
     * @return string or null
     */
    public function getMapPin()
    {
        return $this->_scopeConfig->getValue(self::MAP_PIN_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get location settings from configuration
     *
     * @return int
     */
    public function getLocationSettings(): int
    {
        return (int)$this->_scopeConfig->getValue(self::ASK_LOCATION_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get template settings from configuration, i.e full width or page width
     *
     * @return string
     */
    public function getTemplateSettings(): string
    {
        return $this->_scopeConfig->getValue(self::TEMPLATE_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get api key settings from configuration
     *
     * @return string
     */
    public function getApiKeySettings(): string
    {
        return $this->_scopeConfig->getValue(self::API_KEY_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get unit of length settings from configuration
     *
     * @return string
     */
    public function getUnitOfLengthSettings(): string
    {
        return $this->_scopeConfig->getValue(self::UNIT_LENGTH_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get zoom settings from configuration
     *
     * @return int
     */
    public function getZoomSettings(): int
    {
        return (int)$this->_scopeConfig->getValue(self::ZOOM_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get latitude settings from configuration
     *
     * @return float
     */
    public function getLatitudeSettings(): float
    {
        return (float)$this->_scopeConfig->getValue(self::LATITUDE_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get longitude settings from configuration
     *
     * @return float
     */
    public function getLongitudeSettings(): float
    {
        return (float)$this->_scopeConfig->getValue(self::LONGITUDE_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get radius settings from configuration
     *
     * @return float
     */
    public function getRadiusSettings(): float
    {
        return (float)$this->_scopeConfig->getValue(self::RADIUS_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get stroke weight settings from configuration
     *
     * @return float
     */
    public function getStrokeWeightSettings(): float
    {
        return (float)$this->_scopeConfig->getValue(self::STROKE_WEIGHT_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get stroke opacity settings from configuration
     *
     * @return float
     */
    public function getStrokeOpacitySettings(): float
    {
        return (float)$this->_scopeConfig->getValue(self::STROKE_OPACITY_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get stroke color settings from configuration
     *
     * @return string
     */
    public function getStrokeColorSettings(): string
    {
        return $this->_scopeConfig->getValue(self::STROKE_COLOR_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get fill opacity settings from configuration
     *
     * @return string
     */
    public function getFillOpacitySettings(): float
    {
        return (float)$this->_scopeConfig->getValue(self::FILL_OPACITY_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get fill color settings from configuration
     *
     * @return string
     */
    public function getFillColorSettings(): string
    {
        return $this->_scopeConfig->getValue(self::FILL_COLOR_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * get media url
     *
     * @return string
     */
    public function getMediaUrl(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA );
    }

}