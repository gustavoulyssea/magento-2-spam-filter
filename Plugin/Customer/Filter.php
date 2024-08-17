<?php
/**
 * @author Gustavo Ulyssea - gustavo.ulyssea@gmail.com
 * @copyright Copyright (c) 2024 - 2024 GumNet (https://gum.net.br)
 * @package GustavoUlyssea SpamFilter
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY GUM Net (https://gum.net.br). AND CONTRIBUTORS
 * ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
 * TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE FOUNDATION OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

declare(strict_types=1);

namespace GustavoUlyssea\SpamFilter\Plugin\Customer;

use GustavoUlyssea\SpamFilter\Model\ContentValidator;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Data\Customer as CustomerData;
use Magento\Customer\Model\Customer;
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
     * Validate customer
     *
     * @param CustomerRepositoryInterface $subject
     * @param Customer|CustomerData $customer
     * @param null|string $passwordHash
     * @return array
     * @throws InvalidArgumentException
     */
    public function beforeSave(
        CustomerRepositoryInterface $subject,
        $customer,
        $passwordHash = null
    ): array {
        if (get_class($customer) == CustomerData::class) {
            $this->contentValidator->validateData($customer->__toArray());
        } elseif (get_class($customer) == Customer::class) {
            $this->contentValidator->validateData($customer->getData());
        }
        return [$customer, $passwordHash];
    }
}
