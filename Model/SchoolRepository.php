<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Model;

    use Magento\Framework\Api\SearchCriteriaInterface;
    use Magento\Framework\Api\SortOrder;
    use Magento\Framework\Exception\AlreadyExistsException;
    use Magento\Framework\Exception\LocalizedException;
    use Magento\Framework\Exception\NoSuchEntityException;
    use Kinspeed\Schools\Api\Data\SchoolInterface;
    use Kinspeed\Schools\Api\Data\SchoolSearchResultsInterface;
    use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
    use Kinspeed\Schools\Api\Data\SchoolSearchResultsInterfaceFactory;
    use Kinspeed\Schools\Api\SchoolRepositoryInterface;
    use Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory as SchoolCollectionFactory;
    use Kinspeed\Schools\Model\ResourceModel\School\Collection;
    use Kinspeed\Schools\Model\SchoolFactory;
    use Kinspeed\Schools\Model\ResourceModel\School;

    class SchoolRepository implements SchoolRepositoryInterface
    {
        /**
         * @var School
         */
        private $schoolFactory;

        /**
         * @var SchoolCollectionFactory
         */
        private $schoolCollectionFactory;

        /**
         * @var \Kinspeed\Schools\Model\ResourceModel\School
         */
        protected $resourceModel;

        /**
         * @var SchoolSearchResultsInterfaceFactory
         */
        private $searchResultFactory;
        /**
         * @var CollectionProcessorInterface
         */
        private $collectionProcessor;

        /**
         * SchoolRepository constructor.
         *
         * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface|null $collectionProcessor
         * @param \Kinspeed\Schools\Model\ResourceModel\School                            $resourceModel
         * @param \Kinspeed\Schools\Model\SchoolFactory                                   $schoolFactory
         * @param \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory          $schoolCollectionFactory
         * @param \Kinspeed\Schools\Api\Data\SchoolSearchResultsInterfaceFactory           $schoolSearchResultInterfaceFactory
         */
        public function __construct(
            CollectionProcessorInterface $collectionProcessor = null,
            School $resourceModel,
            SchoolFactory $schoolFactory,
            SchoolCollectionFactory $schoolCollectionFactory,
            SchoolSearchResultsInterfaceFactory $schoolSearchResultInterfaceFactory
        ) {
            $this->schoolFactory = $schoolFactory;
            $this->schoolCollectionFactory = $schoolCollectionFactory;
            $this->searchResultFactory = $schoolSearchResultInterfaceFactory;
            $this->resourceModel = $resourceModel;
            $this->collectionProcessor = $collectionProcessor;
        }

        /**
         * @param int $id
         *
         * @return \Kinspeed\Schools\Api\Data\SchoolInterface
         * @throws \Magento\Framework\Exception\NoSuchEntityException
         * @throws \Magento\Framework\Exception\LocalizedException
         */
        public function getById($id)
        {
            $school = $this->schoolFactory->create()->load($id);
            if (! $school->getId()) {
                throw new NoSuchEntityException(__('Unable to find school with ID "%1"', $id));
            }
            return $school;
        }

        /**
         * @param \Kinspeed\Schools\Api\Data\SchoolInterface $school
         *
         * @return \Kinspeed\Schools\Api\Data\SchoolInterface|string
         */
        public function save(SchoolInterface $school)
        {
            try {
                return $this->resourceModel->save($school);
            }
            catch (AlreadyExistsException $e) {
                return $e->getMessage();
            }
            catch (\Exception $e) {
                return $e->getMessage();
            }
        }

        /**
         * @param \Kinspeed\Schools\Api\Data\SchoolInterface $school
         *
         * @return void
         */
        public function delete(SchoolInterface $school)
        {
            try {
                $this->resourceModel->delete($school);
            }
            catch (\Exception $e) {
                $e->getMessage();
            }
        }

        /**
         * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
         *
         * @return \Kinspeed\Schools\Api\Data\SchoolSearchResultsInterface|string
         */
        public function getList(SearchCriteriaInterface $searchCriteria)
        {

            /** @var \Kinspeed\Schools\Model\ResourceModel\School\Collection $collection */
            $collection = $this->schoolCollectionFactory->create();
            try {
                $collection->addAttributeToSelect('*');
                $collection->addAttributeToFilter('is_active', true);
                $this->collectionProcessor->process($searchCriteria, $collection);
                $collection->load();

                $searchResult = $this->searchResultFactory->create();
                $searchResult->setSearchCriteria($searchCriteria);
                $searchResult->setItems($collection->getItems());
                $searchResult->setTotalCount($collection->getSize());

                return $searchResult;
            }
            catch (LocalizedException $e) {
                return $e->getMessage();
            }
        }

        private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
        {
            foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
                $fields = $conditions = [];
                foreach ($filterGroup->getFilters() as $filter) {
                    $fields[] = $filter->getField();
                    $conditions[] = [$filter->getConditionType() => $filter->getValue()];
                }
                $collection->addFieldToFilter($fields, $conditions);
            }
        }

        private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
        {
            foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
                $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
                $collection->addOrder($sortOrder->getField(), $direction);
            }
        }

        private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
        {
            $collection->setPageSize($searchCriteria->getPageSize());
            $collection->setCurPage($searchCriteria->getCurrentPage());
        }

        private function buildSearchResult(SearchCriteriaInterface $searchCriteria, Collection $collection)
        {
            $searchResults = $this->searchResultFactory->create();

            $searchResults->setSearchCriteria($searchCriteria);
            $searchResults->setItems($collection->getItems());
            $searchResults->setTotalCount($collection->getSize());

            return $searchResults;
        }
    }