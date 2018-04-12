<?php
/**
 * Save
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */
namespace Kinspeed\Schools\Controller\Adminhtml\School;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Kinspeed\Schools\Model\School\Attribute\Backend\ImageFactory;
use Kinspeed\Schools\Model\SchoolFactory;

class Save extends Action
{
    /** @var SchoolFactory $objectFactory */
    protected $objectFactory;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;
    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    private $addressFactory;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param Context                                 $context
     * @param SchoolFactory                           $objectFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\AddressFactory  $addressFactory
     * @param \Magento\Catalog\Model\CategoryFactory  $categoryFactory
     */
    public function __construct(
        Context $context,
        SchoolFactory $objectFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->objectFactory = $objectFactory;
        parent::__construct($context);
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Kinspeed_Schools::school');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');
        $data = $this->getRequest()->getParams();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $params = [];
            $objectInstance = $this->objectFactory->create();
            $objectInstance->setStoreId($storeId);
            $params['store'] = $storeId;
            if (empty($data['entity_id'])) {
                $data['entity_id'] = null;
            } else {
                $objectInstance->load($data['entity_id']);
                $params['entity_id'] = $data['entity_id'];
                $customer = $this->customerFactory->create()->setWebsiteId(1)->loadByEmail($objectInstance->getEmail());
                if (!empty($customer))
                    /** @var \Magento\Customer\Model\CustomerFactory $customer */
                    $this->updateCustomer($customer, $data);
            }
            $imageData = $this->preparedImagesData($data);
            $data = array_merge($data, $imageData);
            $category = $this->categoryFactory->create()->loadByAttribute('linked_school', $objectInstance->getId());
            if (!empty($category))
                $this->updateCategory($category, $data);
            $objectInstance->addData($data);

            $this->_eventManager->dispatch(
                'kinspeed_schools_school_prepare_save',
                ['object' => $this->objectFactory, 'request' => $this->getRequest()]
            );

            try {
                $objectInstance->save();
                $this->messageManager->addSuccessMessage(__('You saved this record.'));
                $this->_getSession()->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $params['entity_id'] = $objectInstance->getId();
                    $params['_current'] = true;
                    return $resultRedirect->setPath('*/*/edit', $params);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the record.'));
            }

            $this->_getSession()->setFormData($this->getRequest()->getPostValue());
            return $resultRedirect->setPath('*/*/edit', $params);
        }
        return $resultRedirect->setPath('*/*/');
    }

    protected function preparedImagesData(array $data): array
    {
        $imagesData = [];
        $imageAttributeCodes = array_keys(ImageFactory::IMAGE_ATTRIBUTE_CODES);
        foreach ($imageAttributeCodes as $imageAttrCode) {
            if (empty($data[$imageAttrCode])) {
                $imagesData[$imageAttrCode]['delete'] = true;
            }
        }
        return $imagesData;
    }

    private function generateUrlKey(&$data)
    {
        $url = strtolower(
                trim(
                    preg_replace('/ +/', '-', preg_replace('/[^A-Za-z0-9 ]/', '-',
                                                           urlencode(
                                                               html_entity_decode(
                                                                   strip_tags(
                                                                       $data['school_name']
                                                                   )
                                                               )
                                                           )
                    ))
                )
            );
        $url .= '/' . strtolower(
            trim(
                preg_replace('/ +/', '-', preg_replace('/[^A-Za-z0-9 ]/', '-',
                                                       urlencode(
                                                           html_entity_decode(
                                                               strip_tags(
                                                                   $data['postcode']
                                                               )
                                                           )
                                                       )
                ))
            )
        );
        return $url;
    }

    /**
     * @param \Magento\Catalog\Model\CategoryFactory $category
     * @param $data
     */
    // TODO: It's not updating the category properly.
    private function updateCategory(&$category, &$data)
    {
        try {
            $category->setData('name', $data['school_name']);
            $category->setData('is_active', $data['active_customer']);
            $category->setData('image', $data['logo'][0]['url'], array('image', 'small_image', 'thumbnail'), true, false);
            $category->setData('linked_school', $data['entity_id']);
            $category->setData('url_key', $this->generateUrlKey($data));
            $category->save();
            $this->messageManager->addSuccessMessage(__('You\'ve updated the category successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }


    /**
     * @param \Magento\Customer\Model\CustomerFactory $customer
     * @param                                         $data
     */
    private function updateCustomer(&$customer, &$data)
    {
        // Create Customer For School
        try {
            $customer->setData('email', $data['email']);
            $customer->setData('firstname', $data['first_name']);
            $customer->setData('lastname', $data['last_name']);
            $customer->setData('linked_school', $data['entity_id']);
            $customer->save();
            $this->messageManager->addSuccessMessage(__('You\'ve updated the customer successfully. If you would like to update the shipping/billing address details, please click the \'View Customer\' button and edit them under \'Addresses\'.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        //TODO: Currently creates a new address.
        // Add Customer Address Details
//        try {
//            $address = $this->addressFactory->create()->setCustomer($customer);
//            $address->setData('firstname', $data['first_name']);
//            $address->setData('lastname', $data['last_name']);
//            $address->setData('company', $data['school_name']);
//            $address->setData('street', $data['address_1']);
//            $address->setData('city', $data['town']);
//            $address->setData('postcode', $data['postcode']);
//            $address->setData('telephone', $data['tel']);
//            $address->setData('country_id', 'GB');
//            $address->save();
//            $this->messageManager->addSuccessMessage(__('You\'ve updated the customer address details successfully.'));
//
//        } catch (\Exception $e) {
//            $this->messageManager->addErrorMessage($e->getMessage());
//        }
    }

}
