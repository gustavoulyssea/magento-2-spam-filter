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

namespace GustavoUlyssea\SpamFilter\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\InvalidArgumentException;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

class ContentValidator
{
    public const CONFIG_PATH = 'spam_filter/security/forbidden_strings';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var array
     */
    protected $forbiddenContent = [];

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Validate order
     *
     * @param Order $order
     * @return void
     * @throws InvalidArgumentException
     */
    public function validateOrder(Order $order): void
    {
        $this->validateData($order->getData());

        if ($customer = $order->getCustomer()) {
            $this->validateData($customer->getData());
        }
        if ($address = $order->getShippingAddress()) {
            $this->validateData($address->getData());
        }

        if ($address = $order->getBillingAddress()) {
            $this->validateData($address->getData());
        }
    }

    /**
     * Validate entity (extends AbstractModel)
     *
     * @param array $data
     * @return void
     * @throws InvalidArgumentException
     */
    public function validateData(array $data): void
    {
        foreach ($data as $entry) {
            $this->validateDataEntry($entry);
        }
    }

    /**
     * Validate data
     *
     * @param mixed $data
     * @return void
     * @throws InvalidArgumentException
     */
    public function validateDataEntry($data): void
    {
        if (is_string($data)) {
            foreach ($this->getForbiddenContent() as $forbiddenContent) {
                if (strlen($forbiddenContent) && stristr($data, $forbiddenContent)) {
                    $this->throwExceptionInvalidData();
                }
            }
        }
    }

    /**
     * Get forbidden content
     *
     * @return array
     */
    public function getForbiddenContent(): array
    {
        if (count($this->forbiddenContent)) {
            return $this->forbiddenContent;
        }
        if (!$forbiddenContent = $this->scopeConfig->getValue(self::CONFIG_PATH, ScopeInterface::SCOPE_STORE)) {
            $this->forbiddenContent[] = '';
            return $this->forbiddenContent;
        }
        $this->forbiddenContent = explode(',', $forbiddenContent);
        return $this->forbiddenContent;
    }

    /**
     * Throw invalid exception
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function throwExceptionInvalidData(): void
    {
        throw new InvalidArgumentException(__('Invalid data provided.'));
    }
}
