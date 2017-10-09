<?php

namespace Pmclain\OneClickCheckout\Test\Unit\Model\QuoteBuilder;

use Magento\Quote\Model\Quote;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use \PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pmclain\OneClickCheckout\Model\DefaultPaymentProvider;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\PaymentBuilder;
use Pmclain\OneClickCheckout\Model\VaultPool;

class PaymentBuilderTest extends TestCase
{
    /**
     * @var PaymentBuilder
     */
    private $paymentBuilder;

    /**
     * @var DefaultPaymentProvider|MockObject
     */
    private $defaultPaymentProviderMock;

    /**
     * @var PaymentTokenInterface|MockObject
     */
    private $paymentTokenMock;

    /**
     * @var Quote|MockObject
     */
    private $quoteMock;

    /**
     * @var Quote\Payment|MockObject
     */
    private $paymentMock;

    /**
     * @var Quote\Address|MockObject
     */
    private $addressMock;

    /**
     * @var VaultPool|MockObject
     */
    private $vaultPoolMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->defaultPaymentProviderMock = $this->getMockBuilder(DefaultPaymentProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentMock = $this->getMockBuilder(Quote\Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->vaultPoolMock = $this->getMockBuilder(VaultPool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentTokenMock = $this->getMockBuilder(PaymentTokenInterface::class)
            ->getMockForAbstractClass();

        $this->addressMock = $this->getMockBuilder(Quote\Address::class)
            ->disableOriginalConstructor()
            ->setMethods(['setPaymentMethod', 'setCollectShippingRates'])
            ->getMock();

        $this->defaultPaymentProviderMock->expects($this->once())
            ->method('getDefaultPayment')
            ->willReturn($this->paymentTokenMock);

        $this->quoteMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($this->paymentMock);

        $this->paymentMock->expects($this->once())
            ->method('setQuote')
            ->with($this->quoteMock);

        $this->paymentMock->expects($this->once())
            ->method('importData');

        $this->vaultPoolMock->expects($this->once())
            ->method('getPaymentDataArray')
            ->with($this->paymentTokenMock)
            ->willReturn([]);

        $this->addressMock->expects($this->once())
            ->method('setPaymentMethod');

        $this->paymentMock->expects($this->once())
            ->method('getMethod');

        $this->paymentBuilder = $objectManager->getObject(
            PaymentBuilder::class,
            [
                'defaultPaymentProvider' => $this->defaultPaymentProviderMock,
                'vaultPool' => $this->vaultPoolMock,
            ]
        );
    }

    public function testSetPaymentMethod()
    {
        $this->quoteMock->expects($this->once())
            ->method('isVirtual')
            ->willReturn(false);

        $this->quoteMock->expects($this->exactly(2))
            ->method('getShippingAddress')
            ->willReturn($this->addressMock);

        $this->addressMock->expects($this->once())
            ->method('setCollectShippingRates');

        $this->paymentBuilder->setPaymentMethod($this->quoteMock);
    }

    public function testSetPaymentMethodVirtualQuote()
    {
        $this->quoteMock->expects($this->once())
            ->method('isVirtual')
            ->willReturn(true);

        $this->quoteMock->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($this->addressMock);

        $this->paymentBuilder->setPaymentMethod($this->quoteMock);
    }
}
