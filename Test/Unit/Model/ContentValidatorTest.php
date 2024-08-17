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

namespace GustavoUlyssea\SpamFilter\Test\Unit\Model;

use GustavoUlyssea\SpamFilter\Model\ContentValidator;
use GustavoUlyssea\SpamFilter\Plugin\Customer\Address\Filter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\InvalidArgumentException;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContentValidatorTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|MockObject
     */
    private ScopeConfigInterface|MockObject $scopeConfigMock;

    private Order|MockObject $orderMock;

    /**
     * @var Filter
     */
    private ContentValidator $contentValidator;

    public function setup(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->addMethods(['getCustomer'])
            ->onlyMethods(['getShippingAddress', 'getBillingAddress', 'getData'])
            ->getMock();

        $this->contentValidator = new ContentValidator(
            $this->scopeConfigMock
        );
    }

    public function testValidateOrder(): void
    {
        $this->prepareGetForbiddenContent();
        $this->orderMock->expects(self::once())
            ->method('getData')
            ->willReturn(['abc']);
        $this->orderMock->expects(self::once())
            ->method('getCustomer')
            ->willReturn(null);
        $this->orderMock->expects(self::once())
            ->method('getShippingAddress')
            ->willReturn(null);
        $this->orderMock->expects(self::once())
            ->method('getBillingAddress')
            ->willReturn(null);
        $this->assertNull($this->contentValidator->validateOrder($this->orderMock));
    }

    public function testValidateData(): void
    {
        $this->prepareGetForbiddenContent();
        $this->assertNull($this->contentValidator->validateData(['abc']));
    }

    public function testValidateDataEntryInvalid(): void
    {
        $this->prepareGetForbiddenContent();
        $this->expectException(InvalidArgumentException::class);
        $this->contentValidator->validateDataEntry('123');
    }

    public function testValidateDataEntryValid(): void
    {
        $this->prepareGetForbiddenContent();
        $this->assertNull($this->contentValidator->validateDataEntry('abc'));
    }

    public function testGetForbiddenContent(): void
    {
        $this->prepareGetForbiddenContent();
        $this->assertEquals(['123'], $this->contentValidator->getForbiddenContent());
    }

    public function prepareGetForbiddenContent(): void
    {
        $this->scopeConfigMock->expects(self::once())
            ->method('getValue')
            ->willReturn('123');
    }

    public function testThrowExceptionInvalidData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->contentValidator->throwExceptionInvalidData();
    }
}
