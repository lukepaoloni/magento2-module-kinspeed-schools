<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Api\Data;

    use Magento\Framework\Api\SearchResultsInterface;

    interface SchoolSearchResultsInterface extends SearchResultsInterface
    {
        /**
         * @return \Kinspeed\Schools\Api\Data\SchoolInterface[]
         */
        public function getItems();

        /**
         * @param \Kinspeed\Schools\Api\Data\SchoolInterface[] $items
         * @return $this
         */
        public function setItems(array $items);
    }