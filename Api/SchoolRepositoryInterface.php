<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Api;

    use Magento\Framework\Api\SearchCriteriaInterface;
    use Kinspeed\Schools\Api\Data\SchoolInterface;

    interface SchoolRepositoryInterface
    {


        /**
         * @param int $id
         * @return \Kinspeed\Schools\Api\Data\SchoolInterface
         * @throws \Magento\Framework\Exception\NoSuchEntityException
         */
        public function getById($id);

        /**
         * @param \Kinspeed\Schools\Api\Data\SchoolInterface $school
         * @return \Kinspeed\Schools\Api\Data\SchoolInterface
         */
        public function save(SchoolInterface $school);

        /**
         * @param \Kinspeed\Schools\Api\Data\SchoolInterface $school
         * @return void
         */
        public function delete(SchoolInterface $school);

        /**
         * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
         * @return \Kinspeed\Schools\Api\Data\SchoolSearchResultsInterface
         */
        public function getList(SearchCriteriaInterface $searchCriteria);
    }