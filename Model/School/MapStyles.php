<?php
    /**
     * @package: $Package
     * @company: $Company
     * @author : Luke Paoloni <$Email>
     */

    namespace Kinspeed\Schools\Model\School;
    class MapStyles
    {

        public function toOptionArray(): array
        {
            return array(
                array(
                    'value' => 'default',
                    'label' => 'Default Styles',
                ),
                array(
                    'value' => 'ultra_light_with_labels',
                    'label' => 'Ultra Light with Labels',
                ),
                array(
                    'value' => 'light_dream',
                    'label' => 'Light Dream',
                ),
                array(
                    'value' => 'blue_water',
                    'label' => 'Blue water',
                ),
                array(
                    'value' => 'pale_Dawn',
                    'label' => 'Pale Dawn',
                ),
                array(
                    'value' => 'paper',
                    'label' => 'Paper',
                ),
                array(
                    'value' => 'light_monochrome',
                    'label' => 'Light Monochrome',
                ),
                array(
                    'value' => 'hopper',
                    'label' => 'Hopper',
                )
            );
        }

    }