<?php
/**
 * ImageFactory
 *
 * @copyright Copyright © 2017 Kinspeed. All rights reserved.
 * @author    luke.paoloni@kinspeed.com
 */

namespace Kinspeed\Schools\Model\School\Attribute\Backend;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;

class ImageFactory
{
    const IMAGE_ATTRIBUTE_CODES = [
        Logo::ATTRIBUTE_CODE => Logo::class,
    ];

    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create corresponding class instance
     *
     * @param $imageAttributeCode
     * @param array $data
     * @return ObjectType
     */
    public function create(string $imageAttributeCode, array $data = [])
    {
        if (empty(self::IMAGE_ATTRIBUTE_CODES[$imageAttributeCode])) {
            throw new InvalidArgumentException(sprintf('"%s": isn\'t allowed', $imageAttributeCode));
        }

        $resultInstance = $this->objectManager->create(self::IMAGE_ATTRIBUTE_CODES[$imageAttributeCode], $data);
        if (!$resultInstance instanceof ImageAbstract) {
            throw new InvalidArgumentException(sprintf('%s isn\'t instance of %s',
                get_class($resultInstance),
                ImageAbstract::class
            ));
        }

        return $resultInstance;
    }
}