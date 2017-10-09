<?php

namespace Pmclain\OneClickCheckout\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use \PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\ShippingMethodBuilder;

class ShippingMethodBuilderTest extends TestCase
{
    /**
     * @var ShippingMethodBuilder
     */
    private $shippingMethodBuilder;

    /**
     * @var Quote|MockObject
     */
    private $quoteMock;

    /**
     * @var Quote\Address|MockObject
     */
    private $addressMock;

    /**
     * @var Quote\Address\Rate|MockObject
     */
    private $rateMock;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $configMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->getMockForAbstractClass();

        $this->quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->addressMock = $this->getMockBuilder(Quote\Address::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->rateMock = $this->getMockBuilder(Quote\Address\Rate::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCode'])
            ->getMock();

        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($this->addressMock);

        $this->addressMock->expects($this->once())
            ->method('getGroupedAllShippingRates')
            ->willReturn([
                'testCarrier' => [
                    $this->rateMock,
                ]
            ]);

        $this->shippingMethodBuilder = $objectManager->getObject(
            ShippingMethodBuilder::class,
            [
                'config' => $this->configMock,
            ]
        );
    }

    public function testSetShippingMethod()
    {
        $this->configMock->expects($this->once())
            ->method('getValue')
            ->willReturn('testCarrier_method');

        $this->rateMock->expects($this->exactly(2))
            ->method('getCode')
            ->willReturn('testCarrier_method');

        $this->shippingMethodBuilder->setShippingMethod($this->quoteMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NotFoundException
     */
    public function testSetShippingMethodNotAvailable()
    {
        $this->configMock->expects($this->once())
            ->method('getValue')
            ->willReturn('testCarrier_method');

        $this->rateMock->expects($this->once())
            ->method('getCode')
            ->willReturn('someOther_method');

        $this->shippingMethodBuilder->setShippingMethod($this->quoteMock);
    }
}
