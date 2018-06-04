<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Console\Commands;

    use Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory;
    use Kinspeed\Schools\Model\SchoolFactory;
    use Magento\Framework\App\State;
    use Magento\Framework\Exception\LocalizedException;
    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;

    class SetDefaultValue extends Command
    {
        const ATTRIBUTE_ARGUMENT       = 'attribute_code';
        const VALUE_ARGUMENT = 'value';
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
         * @var SchoolFactory
         */
        private $schoolFactory;

        /**
         * SetDefaultValue constructor.
         *
         * @param SchoolFactory                                                  $schoolFactory
         * @param \Kinspeed\Schools\Model\ResourceModel\School\CollectionFactory $collectionFactory
         * @param \Magento\Framework\App\State                                   $state
         */
        public function __construct(
            SchoolFactory $schoolFactory,
            CollectionFactory $collectionFactory,
            State $state
        )
        {
            parent::__construct();
            $this->state = $state;
            $this->schoolFactory = $schoolFactory;
            try {
                $state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
            }
            catch (LocalizedException $e) {
                $e->getMessage();
            }
            $this->collectionFactory = $collectionFactory;
        }

        protected function configure()
        {
            $options = [
                new InputOption(
                    self::ATTRIBUTE_ARGUMENT,
                    '-a',
                    InputOption::VALUE_REQUIRED,
                    'Attribute Code'
                ),
                new InputOption(
                    self::VALUE_ARGUMENT,
                    '-v',
                    InputOption::VALUE_REQUIRED,
                    'Attribute Value'
                )
            ];
            $this->setName('schools:set:all')
                 ->setDescription('Set a value for all schools given an attribute.')
                ->setDefinition($options);
            parent::configure();
        }

        protected function execute(InputInterface $input, OutputInterface $output)
        {
            $attribute = $input->getOption(self::ATTRIBUTE_ARGUMENT);
            $value = $input->getOption(self::VALUE_ARGUMENT);

            if (!is_null($attribute))
                throw new \InvalidArgumentException('Argument ' . self::ATTRIBUTE_ARGUMENT . ' is missing');
            if (is_null($value))
                throw new \InvalidArgumentException('Argument ' . self::ATTRIBUTE_ARGUMENT . ' is missing');

            try {
                $collection = $this->collectionFactory->create();
                $schools = $collection->addAttributeToSelect('*');
                foreach ($schools as $school) {
                    /** @var \Kinspeed\Schools\Model\School $school */
                    $school->setData($attribute, $value);
                    $school->save();
                }
            }
            catch (LocalizedException $e) {
                $output->writeln('<error>'.$e->getMessage().'</error>');
            }
            catch (\Exception $e) {
                $output->writeln('<error>'.$e->getMessage().'</error>');
            }
        }
    }