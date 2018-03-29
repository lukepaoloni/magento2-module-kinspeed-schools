 1. Add your custom fields on:

    Kinspeed/Schools/Setup/InstallSchema.php

2. Add your custom columns to the grid

    Kinspeed/Schools/view/adminhtml/ui_component/kinspeed_schools_types_listing.xml

3. Add your custom fields to the form

    Kinspeed/Schools/view/adminhtml/ui_component/kinspeed_schools_types_form.xml

4. Set the Admin Menu tab where you want your Module can be found:

    Kinspeed/Schools/etc/adminhtml/menu.xml

5. Set From server side Validations:

    Kinspeed\Schools\Controller\Adminhtml\Types\Validate:

    /**
     * Check if required fields is not empty
     *
     * @param array $data
     */
    public function validateRequireEntries(array $data)
    {
        $requiredFields = [
            'identifier' => __('Types Identifier'),
        ];

        //...
