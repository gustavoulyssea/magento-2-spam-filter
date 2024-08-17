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

namespace GustavoUlyssea\SpamFilter\Test\Unit\Plugin\Order;

use GustavoUlyssea\SpamFilter\Model\ContentValidator;
use GustavoUlyssea\SpamFilter\Plugin\Order\Filter;
use Magento\Customer\Model\Data\Address;
use Magento\Customer\Model\ResourceModel\AddressRepository;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    /**
     * @var ContentValidator|MockObject
     */
    private ContentValidator|MockObject $contentValidatorMock;

    /**
     * @var Order|MockObject
     */
    private Address|MockObject $orderMock;

    /**
     * @var Filter
     */
    private Filter $filter;

    public function setup(): void
    {
        $this->contentValidatorMock = $this->createMock(ContentValidator::class);
        $this->orderMock = $this->createMock(Order::class);

        $this->filter = new Filter(
            $this->contentValidatorMock
        );
    }

    public function testBeforePlace(): void
    {
        $this->contentValidatorMock->expects(self::once())
            ->method('validateOrder');
        $this->assertNull($this->filter->beforePlace($this->orderMock));
    }
}
