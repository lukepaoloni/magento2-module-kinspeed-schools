<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Helper;

    use \Kinspeed\Schools\Model\SchoolFactory;

    class Validate
    {
        /**
         * @var \Kinspeed\Schools\Model\SchoolFactory
         */
        private $schoolFactory;

        /**
         * Validate constructor.
         *
         * @param \Kinspeed\Schools\Model\SchoolFactory $schoolFactory
         */
        public function __construct(
            SchoolFactory $schoolFactory
        ) {
            $this->schoolFactory = $schoolFactory;
        }

        /**
         * @param $id
         *
         * @return bool
         */
        public function isCustomerFieldsValid($id)
        {
            $school = $this->schoolFactory->create()->load($id);
            if (
                !empty($school->getFirstName()) &&
                !empty($school->getSurname()) &&
                !empty($school->getPhoneNumber()) &&
                !empty($school->getEmail())

            )
                return true;
            return false;
        }

        public function isSchoolFieldsValid($id)
        {
            $school = $this->schoolFactory->create()->load($id);
            if (
                !empty($school->getSchoolName()) &&
                !empty($school->getUrl()) &&
                !empty($school->getLogo())
            )
                return true;
            return false;
        }
    }