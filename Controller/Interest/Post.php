<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Controller\Interest;

    use Magento\Framework\App\Action\Action;
    use Magento\Framework\Data\Form\FormKey\Validator;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Magento\Framework\View\Result\PageFactory;
    use Magento\Framework\App\Action\Context;
    use Magento\Framework\Escaper;
    use Kinspeed\Schools\Model\SchoolFactory;
    use Faonni\ReCaptcha\Helper\Data as ReCaptchaHelper;
    use Faonni\ReCaptcha\Model\Provider;

    class Post extends Action
    {
        /**
         * @var PageFactory
         */
        protected $pageFactory;
        /**
         * @var \Magento\Framework\Registry
         */
        private $registry;
        /**
         * @var \Magento\Framework\Data\Form\FormKey\Validator
         */
        private $formKeyValidator;
        /**
         * @var \Kinspeed\Schools\Model\SchoolFactory
         */
        private $schoolFactory;
        /**
         * @var \Magento\Framework\Escaper
         */
        private $escaper;
        /**
         * @var \Faonni\ReCaptcha\Helper\Data
         */
        private $helper;
        /**
         * @var \Faonni\ReCaptcha\Model\Provider
         */
        private $provider;

        /**
         * @param \Faonni\ReCaptcha\Helper\Data                  $helper
         * @param \Faonni\ReCaptcha\Model\Provider               $provider
         * @param \Magento\Framework\Escaper                     $escaper
         * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
         * @param \Kinspeed\Schools\Model\SchoolFactory          $schoolFactory
         * @param Context                                        $context
         * @param PageFactory                                    $pageFactory
         */
        public function __construct(
            ReCaptchaHelper $helper,
            Provider $provider,
            Escaper $escaper,
            Validator $formKeyValidator,
            SchoolFactory $schoolFactory,
            Context $context,
            PageFactory $pageFactory
        )
        {
            parent::__construct($context);
            $this->pageFactory = $pageFactory;
            $this->formKeyValidator = $formKeyValidator;
            $this->schoolFactory = $schoolFactory;
            $this->escaper = $escaper;
            $this->helper = $helper;
            $this->provider = $provider;
        }

        /**
         * Index Action
         *
         * @return \Magento\Framework\Controller\ResultInterface
         */
        public function execute()
        {
            $resultRedirect = $this->resultRedirectFactory->create();
            $recaptcha = $this->escaper->escapeHtml($this->getRequest()->getParam('g-recaptcha-response'));
            $id = $this->escaper->escapeHtml($this->getRequest()->getParam('school'));
            if (!empty($recaptcha) && !empty($id)) {
                if (
                    $this->formKeyValidator->validate($this->getRequest())
                    &&
                    $this->provider->validate($recaptcha, $this->helper->getSecretKey())
                ) {

                    try {

                        $schoolFactory = $this->schoolFactory->create();
                        $school        = $schoolFactory->load($id);
                        $school->addRegisteredInterest();
                        $school->save();
                        $resultRedirect->setUrl(
                            $this->_url->getUrl(
                                $this->_redirect->getRefererUrl(),
                                ['status' => 'success']
                            )
                        );
                    }
                    catch (\Exception $e) {
                        $this->messageManager->addErrorMessage(__($e->getMessage()));
                        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    }
                }
                else {
                    $this->messageManager->addErrorMessage(__('Error! Unable to validate form.'));
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                }
            } else {
                $this->messageManager->addErrorMessage(__('Error! Unable to validate form.'));
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            }

            return $resultRedirect;
        }
    }