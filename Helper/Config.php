<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Helper;

    use Magento\Framework\App\Config\ScopeConfigInterface;
    use Magento\Store\Model\ScopeInterface;
    use Kinspeed\Schools\Model\School;

    class Config
    {
        /**
         * @var \Magento\Framework\App\Config\ScopeConfigInterface
         */
        private $scopeConfig;

        /**
         * Config constructor.
         *
         * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
         */
        public function __construct(ScopeConfigInterface $scopeConfig)
        {

            $this->scopeConfig = $scopeConfig;
        }

        public function getCategoryId()
        {
            return $this->scopeConfig->getValue(School::XML_CATEGORY_ID, ScopeInterface::SCOPE_STORE);
        }

        public function getCustomerGroupId()
        {
            return $this->scopeConfig->getValue(School::XML_GROUP_ID, ScopeInterface::SCOPE_STORE);
        }

        public function getApiKey()
        {
            return $this->scopeConfig->getValue(School::XML_API_KEY, ScopeInterface::SCOPE_STORE);
        }

        public function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
        {
            $sets = array();
            if(strpos($available_sets, 'l') !== false)
                $sets[] = 'abcdefghjkmnpqrstuvwxyz';
            if(strpos($available_sets, 'u') !== false)
                $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
            if(strpos($available_sets, 'd') !== false)
                $sets[] = '23456789';
            if(strpos($available_sets, 's') !== false)
                $sets[] = '!@#$%&*?';
            $all = '';
            $password = '';
            foreach($sets as $set)
            {
                $password .= $set[array_rand(str_split($set))];
                $all .= $set;
            }
            $all = str_split($all);
            for($i = 0; $i < $length - count($sets); $i++)
                $password .= $all[array_rand($all)];
            $password = str_shuffle($password);
            if(!$add_dashes)
                return $password;
            $dash_len = floor(sqrt($length));
            $dash_str = '';
            while(strlen($password) > $dash_len)
            {
                $dash_str .= substr($password, 0, $dash_len) . '-';
                $password = substr($password, $dash_len);
            }
            $dash_str .= $password;
            return $dash_str;
        }

        public function getEmail()
        {
            return $this->scopeConfig->getValue(School::XML_EMAIL, ScopeInterface::SCOPE_STORE);
        }

        public function getName()
        {
            return $this->scopeConfig->getValue(School::XML_NAME, ScopeInterface::SCOPE_STORE);
        }
    }