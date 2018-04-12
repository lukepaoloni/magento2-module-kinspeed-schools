<?php
/**
 * GenerateButton
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */
namespace Kinspeed\Schools\Block\Adminhtml\School\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class GenerateButton
 */
class CategoryButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;
    /**
     * @var \Kinspeed\Schools\Model\SchoolFactory
     */
    private $schoolFactory;

    /**
     * Constructor
     *
     * @param Context                                 $context
     * @param Registry                                $registry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Catalog\Model\CategoryFactory  $categoryFactory
     * @param \Kinspeed\Schools\Model\SchoolFactory   $schoolFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Kinspeed\Schools\Model\SchoolFactory $schoolFactory
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry = $registry;
        $this->customerFactory = $customerFactory;
        $this->categoryFactory = $categoryFactory;
        $this->schoolFactory = $schoolFactory;
    }


    /**
     * @return array|string
     */
    public function getButtonData()
    {

        $id = $this->registry->registry('entity_id');
        $school = $this->schoolFactory->create()->load($id);
        $category = $this->categoryFactory->create()->loadByAttribute('linked_school', $id);
        if (!empty($category)) {
            $data = [
                'label'          => __('View Category'),
                'class'          => 'category',
                'id'             => 'school-edit-category-button',
                'data_attribute' => [
                    'url' => $this->getGenerateUrl()
                ],
                'on_click'       => 'window.location.href=\''.
                        $this->getGenerateUrl('catalog/category/edit', ['id' => $category->getId()])
                .'\'',
                'sort_order'     => 30,
            ];

            return $data;
        }
        return '';
    }

    /**
     * @param string $path
     * @param array  $params
     *
     * @return string
     */
    public function getGenerateUrl($path = '', $params = [])
    {
        return $this->urlBuilder->getUrl($path, $params);
    }
}
