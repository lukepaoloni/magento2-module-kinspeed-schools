<?php
/**
 * SaveButton
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */
namespace Kinspeed\Schools\Block\Adminhtml\School\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 */
class SaveButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
        return $data;
    }
}
