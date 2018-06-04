<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Console\Commands;

    use Kinspeed\Schools\Model\SchoolFactory;
    use Magento\Framework\Exception\LocalizedException;
    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;
    use Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory;

    class ConvertPostcodeToLongLatCommand extends Command
    {
        /**
         * @var \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory
         */
        private $collectionFactory;

        /**
         * @var \Symfony\Component\Console\Helper\ProgressBar
         */
        private $progressBar;
        /**
         * @var \Mirasvit\SearchElastic\Model\Engine
         */
        private $engine;
        /**
         * @var \Magento\Framework\App\State
         */
        private $state;
        /**
         * @var \Kinspeed\Schools\Model\SchoolFactory
         */
        private $schoolFactory;

        /**
         * GenerateElasticSearchDataCommand constructor.
         *
         * @param \Kinspeed\Schools\Model\SchoolFactory                          $schoolFactory
         * @param \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory $collectionFactory
         * @param \Magento\Framework\App\State                                   $state
         */
        public function __construct(
            SchoolFactory $schoolFactory,
            CollectionFactory $collectionFactory,
            \Magento\Framework\App\State $state
        )
        {
            parent::__construct();
            $this->collectionFactory = $collectionFactory;
            $this->state = $state;
            try {
                $state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
            }
            catch (LocalizedException $e) {
                $e->getMessage();
            }
            $this->schoolFactory = $schoolFactory;
        }

        protected function configure()
        {
            $this->setName('generate:longlat:address')
                ->setDescription('Generates the long/lat for a given address.');
            parent::configure();
        }

        protected function execute(InputInterface $input, OutputInterface $output)
        {
            $collection = $this->collectionFactory->create();
            try {
                $collection->addAttributeToSelect('*');
                $sch = $this->schoolFactory->create();
                /** @var \Kinspeed\Schools\Model\School $school */
                foreach ($collection as $school) {
                    if (!empty($school->getName())) {
                        $address = $school->getAddress1() . " ".
                                $school->getAddress2() . " " .
                                $school->getAddress3() . " " .
                                $school->getTown() . " " .
                                $school->getPostcode();
                        $address = str_replace(' ', '+', $address);
                        $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$address.'&key=AIzaSyAe91eGXAtBt3bcJwI4FquEZy73nuKyP2Q');
                        $gmap = json_decode($geocode);
                        if (isset($gmap->results[0])) {
                            $lat = $gmap->results[0]->geometry->location->lat;
                            $lng = $gmap->results[0]->geometry->location->lng;
                        }
                        try {
                            $sch->load($school->getId());
                            $sch->setLongitude($lng);
                            $sch->setLatitude($lat);
                            $sch->save();
                        } catch (\Exception $e) {
                            $output->writeln($e->getMessage());
                        }
                    }
                }
                $output->writeln('<info>Command successful</info>');
            }
            catch (LocalizedException $e) {
                $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            }
        }
    }