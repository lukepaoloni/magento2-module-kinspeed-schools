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
class GenerateButton implements ButtonProviderInterface
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
        $customer = $this->customerFactory->create()->setWebsiteId(1)->loadByEmail($school->getEmail());
        $category = $this->categoryFactory->create()->loadByAttribute('linked_school', $id);
        if (empty($customer) || empty($category)) {
            $data = [
                'label'          => __('Generate'),
                'class'          => 'generate',
                'id'             => 'school-edit-generate-button',
                'data_attribute' => [
                    'url' => $this->getGenerateUrl()
                ],
                'on_click'       =>
                    'deleteConfirm(\'' . __("Are you sure you want to do this?") . '\', \'' . $this->getGenerateUrl(
                    ) . '\')',
                'sort_order'     => 30,
            ];

            return $data;
        }
        return '';
    }

    /**
     * @return string
     */
    public function getGenerateUrl()
    {
        return $this->urlBuilder->getUrl('*/*/generate', ['entity_id' => $this->registry->registry('entity_id')]);
    }
}
