<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Controller\Account;

    use Kinspeed\Schools\Api\SchoolRepositoryInterface;
    use Kinspeed\Schools\Helper\Config;
    use Kinspeed\Schools\Model\SchoolFactory;
    use Magento\Framework\App\Action\Action;
    use Magento\Framework\App\Area;
    use Magento\Framework\Data\Form\FormKey\Validator;
    use Magento\Framework\Escaper;
    use Magento\Framework\Exception\MailException;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Magento\Framework\Mail\Template\TransportBuilder;
    use Magento\Framework\Translate\Inline\StateInterface;
    use Magento\Framework\View\Result\PageFactory;
    use Magento\Framework\App\Action\Context;
    use Magento\Store\Model\Store;
    use Faonni\ReCaptcha\Model\Provider;
    use Faonni\ReCaptcha\Helper\Data as ReCaptchaHelper;

    class Create extends Action
    {
        /**
         * @var \Magento\Framework\Data\Form\FormKey\Validator
         */
        private $formKeyValidator;
        /**
         * @var \Magento\Framework\Escaper
         */
        private $escaper;
        /**
         * @var \Kinspeed\Schools\Api\SchoolRepositoryInterface
         */
        private $schoolRepository;
        /**
         * @var \Kinspeed\Schools\Model\SchoolFactory
         */
        private $schoolFactory;
        /**
         * @var \Kinspeed\Schools\Helper\Config
         */
        private $config;
        /**
         * @var \Magento\Framework\Mail\Template\TransportBuilder
         */
        private $transportBuilder;
        /**
         * @var \Magento\Framework\Translate\Inline\StateInterface
         */
        private $inlineTranslation;
        /**
         * @var \Faonni\ReCaptcha\Helper\Data
         */
        private $recaptchaHelper;
        /**
         * @var \Faonni\ReCaptcha\Model\Provider
         */
        private $provider;

        /**
         * @param Context                                            $context
         * @param \Magento\Framework\Data\Form\FormKey\Validator     $formKeyValidator
         * @param \Magento\Framework\Escaper                         $escaper
         * @param \Kinspeed\Schools\Api\SchoolRepositoryInterface    $schoolRepository
         * @param \Kinspeed\Schools\Model\SchoolFactory              $schoolFactory
         * @param \Kinspeed\Schools\Helper\Config                    $config
         * @param \Magento\Framework\Mail\Template\TransportBuilder  $transportBuilder
         * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
         * @param \Faonni\ReCaptcha\Helper\Data                      $recaptchaHelper
         * @param \Faonni\ReCaptcha\Model\Provider                   $provider
         */
        public function __construct(
            Context $context,
            Validator $formKeyValidator,
            Escaper $escaper,
            SchoolRepositoryInterface $schoolRepository,
            SchoolFactory $schoolFactory,
            Config $config,
            TransportBuilder $transportBuilder,
            StateInterface $inlineTranslation,
            ReCaptchaHelper $recaptchaHelper,
            Provider $provider
        )
        {
            parent::__construct($context);
            $this->formKeyValidator = $formKeyValidator;
            $this->escaper = $escaper;
            $this->schoolRepository = $schoolRepository;
            $this->schoolFactory = $schoolFactory;
            $this->config = $config;
            $this->transportBuilder = $transportBuilder;
            $this->inlineTranslation = $inlineTranslation;
            $this->recaptchaHelper = $recaptchaHelper;
            $this->provider = $provider;
        }

        /**
         * Index Action
         *
         * @return \Magento\Framework\Controller\Result\Redirect
         */
        public function execute()
        {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setUrl($this->_url->getUrl('school/register/account'));
            $post = $this->getRequest()->getParams();
            $recaptcha = $post['g-recaptcha-response'];
            $customer = $post['customer'];
            $school = $post['school'];
           if (
               !$this->formKeyValidator->validate($this->getRequest()) ||
               empty($recaptcha) ||
               !$this->provider->validate($recaptcha, $this->recaptchaHelper->getSecretKey()) ||
               empty($school['name']) ||
               empty($school['street']) ||
               empty($school['postcode']) ||
               empty($customer['name']) ||
               empty($customer['surname']) ||
               empty($customer['email'])
           ) {
               $this->messageManager->addErrorMessage(__('Error: Unable to validate request'));
               return $resultRedirect;
           }

            $sender = [
                'name' => $this->escaper->escapeHtml($customer['name'] . ' ' . $customer['surname']),
                'email' => $this->escaper->escapeHtml($customer['email'])
            ];
            $this->inlineTranslation->suspend();
            $variables = [
                'name' => $sender['name'],
                'email' => $sender['email'],
                'phone' => $this->escaper->escapeHtml($customer['phone']),
                'schoolName' => $this->escaper->escapeHtml($school['name']),
                'street' => $this->escaper->escapeHtml($school['street']),
                'city' => $this->escaper->escapeHtml($school['city']),
                'postcode' => $this->escaper->escapeHtml($school['postcode']),
                'date' => date('Y')
            ];
            $to = $this->config->getEmail();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('send_school_customer_register_form')
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => Store::DEFAULT_STORE_ID
                    ]
                )
                ->setTemplateVars($variables)
                ->setFrom($sender)
                ->addTo($to)
                ->getTransport();
            try {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                $this->messageManager->addSuccessMessage(__('Thank you for registering! You will be contacted shortly regarding your account.'));
            }
            catch (MailException $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }

            return $resultRedirect;
        }
    }