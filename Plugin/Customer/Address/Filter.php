<?php

declare(strict_types=1);

namespace GustavoUlyssea\SpamFilter\Plugin\Customer\Address;

use GustavoUlyssea\SpamFilter\Model\ContentValidator;
use Magento\Customer\Model\Data\Address;
use Magento\Customer\Model\ResourceModel\AddressRepository;
use Magento\Framework\Exception\InvalidArgumentException;

class Filter
{
    /**
     * @var ContentValidator
     */
    protected $contentValidator;

    /**
     * @param ContentValidator $contentValidator
     */
    public function __construct(
        ContentValidator $contentValidator
    ) {
        $this->contentValidator = $contentValidator;
    }

    /**
     * Validate address
     *
     * @param AddressRepository $subject
     * @param Address $address
     * @return array
     * @throws InvalidArgumentException
     */
    public function beforeSave(
        AddressRepository $subject,
        Address $address
    ): array {
        $this->contentValidator->validateData($address->__toArray());
        return [$address];
    }
}
