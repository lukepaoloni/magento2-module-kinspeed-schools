<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Console\Commands;

    use Magento\Framework\Exception\LocalizedException;
    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;
    use Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory;
    use \Mirasvit\SearchElastic\Model\Engine;

    class GenerateElasticSearchDataCommand extends Command
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
         * GenerateElasticSearchDataCommand constructor.
         *
         * @param \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory $collectionFactory
         * @param \Mirasvit\SearchElastic\Model\Engine                           $engine
         * @param \Magento\Framework\App\State                                   $state
         */
        public function __construct(
            CollectionFactory $collectionFactory,
            Engine $engine,
            \Magento\Framework\App\State $state
        )
        {
            parent::__construct();
            $this->collectionFactory = $collectionFactory;
            $this->engine = $engine;
            $this->state = $state;
            try {
                $state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
            }
            catch (LocalizedException $e) {
                $e->getMessage();
            }
        }

        protected function configure()
        {
            $this->setName('generate:elasticsearch:schools')
                ->setDescription('Generates ElasticSearch objects from Schools collection');
            parent::configure();
        }

        protected function execute(InputInterface $input, OutputInterface $output)
        {
            $collection = $this->collectionFactory->create();
            $elastic = $this->engine->getClient();
            try {
                $collection->addAttributeToSelect('*');
                /** @var \Kinspeed\Schools\Model\School $school */
                foreach ($collection as $school) {
                    if (!empty($school->getName())) {
                        $params =
                            [
                                'index' => 'schools',
                                'id'    => $school->getId(),
                                'type'  => 'school',
                                'body'  => [
                                    'school_id' => $school->getId(),
                                    'school_name' => $school->getSchoolName(),
                                    'address_1'   => $school->getAddress1(),
                                    'address_2'   => $school->getAddress2(),
                                    'address_3'   => $school->getAddress3(),
                                    'town'        => $school->getTown(),
                                    'postcode'    => $school->getPostcode(),
                                    'logo'        => $school->getLogo(),
                                    'url'         => $school->getPath(),
                                    'latitude'    => $school->getLatitude(),
                                    'longitude'   => $school->getLongitude()
                                ]
                            ];
                        $elastic->index($params);
                    }
                }
                $output->writeln('<info>Command successful</info>');
            }
            catch (LocalizedException $e) {
                $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            }
        }
    }