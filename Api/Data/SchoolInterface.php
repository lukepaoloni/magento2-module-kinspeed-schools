<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Api\Data;

    use Magento\Framework\Api\ExtensibleDataInterface;

    interface SchoolInterface extends ExtensibleDataInterface
    {
        /**
         * @return int
         */
        public function getId();

        /**
         * @param int $id
         * @return void
         */
        public function setId($id);

        /**
         * @return string
         */
        public function getSchoolName();

        /**
         * @param string $name
         * @return void
         */
        public function setSchoolName($name);

        /**
         * @param $id
         *
         * @return string
         */
        public function getAdminUrl($id);

        /**
         * @return string
         */
        public function getPath();

        /**
         * @return string
         */
        public function getPostcode();

        /**
         * @return bool
         */
        public function isBulkOrderEnabledDTS();

        /**
         * @return bool
         */
        public function isBulkOrderEnabledPPP();

        /**
         * @param $postcode
         *
         * @return void
         */
        public function setPostcode($postcode);

        /**
         * @return string
         */
        public function getAddress1();

        /**
         * @param $address_1
         *
         * @return void
         */
        public function setAddress1($address_1);

        /**
         * @return string
         */
        public function getAddress2();

        /**
         * @param $address_2
         *
         * @return void
         */
        public function setAddress2($address_2);

        /**
         * @return string
         */
        public function getAddress3();

        /**
         * @param $address_3
         *
         * @return void
         */
        public function setAddress3($address_3);

        /**
         * @param $county_id
         *
         * @return void
         */
        public function setCounty($county_id);

        /**
         * @param $type_id
         *
         * @return void
         */
        public function setType($type_id);

        /**
         * @return string
         */
        public function getPhoneNumber();

        /**
         * @param $phone
         *
         * @return void
         */
        public function setPhoneNumber($phone);

        /**
         * @return string
         */
        public function getLogo();

        /**
         * @param $logo
         *
         * @return void
         */
        public function setLogo($logo);

        /**
         * @return string
         */
        public function getWebsite();

        /**
         * @param $website
         *
         * @return void
         */
        public function setWebsite($website);

        /**
         * @return int
         */
        public function getPupilsOnRoll();

        /**
         * @param $num_of_pupils
         *
         * @return void
         */
        public function setPupilsOnRoll($num_of_pupils);

        /**
         * @return string
         */
        public function getName();

        /**
         * @return string
         */
        public function getFirstName();

        /**
         * @return string
         */
        public function getSurname();

        /**
         * @param $first_name
         *
         * @return void
         */
        public function setFirstName($first_name);

        /**
         * @param $last_name
         *
         * @return void
         */
        public function setSurname($last_name);

        /**
         * @return string
         */
        public function getJobTitle();

        /**
         * @param $job_title
         *
         * @return void
         */
        public function setJobTitle($job_title);

        /**
         * @return string
         */
        public function getEmail();

        /**
         * @param $email
         *
         * @return void
         */
        public function setEmail($email);

        /**
         * @return string
         */
        public function getNotes();

        /**
         * @return integer
         */
        public function getTotalRegisteredInterests();

        /**
         * @param $value
         *
         * @return void
         */
        public function setRegisteredInterest($value);

        /**
         * @return void
         */
        public function addRegisteredInterest();

        /**
         * @return bool
         */
        public function isActiveCustomer();

        /**
         * @return bool
         */
        public function isActive();

        /**
         * @param $active_customer
         *
         * @return void
         */
        public function setIsActiveCustomer($active_customer);

        /**
         * @return bool
         */
        public function isEnabledParentOrder();

        /**
         * @param $enable_parent_order
         *
         * @return void
         */
        public function setIsEnabledParentOrder($enable_parent_order);

        /**
         * @return bool
         */
        public function isEnabledSchoolFranOrder();

        /**
         * @param $enable_school_fran_order
         *
         * @return void
         */
        public function setIsEnabledSchoolFranOrder($enable_school_fran_order);

        /**
         * @return bool
         */
        public function showSchool();

        /**
         * @param \Kinspeed\Schools\Api\Data\string $imageAttrCode
         *
         * @return string
         */
        public function getImageSrc($imageAttrCode);

    }