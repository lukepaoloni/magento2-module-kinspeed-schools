<?php
/**
 * Logo
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */

namespace Kinspeed\Schools\Model\School\Attribute\Backend;

class Logo extends ImageAbstract
{
    /**
     * @var string
     */
    const ATTRIBUTE_CODE = 'logo';

    protected function subdirName(): string
{
    return self::ATTRIBUTE_CODE;
}

    protected function attributeCode(): string
{
    return self::ATTRIBUTE_CODE;
}

}
