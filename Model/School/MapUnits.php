<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Model\School;

    class MapUnits
    {
        public function toOptionArray(): array
        {
            return array(
                array(
                    'value' => 'default',
                    'label' => 'Miles',
                ),
                array(
                    'value' => 'kilometres',
                    'label' => 'Kilometres',
                )
            );
        }
    }