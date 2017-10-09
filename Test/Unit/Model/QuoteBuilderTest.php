<?php

namespace Pmclain\OneClickCheckout\Test\Unit\Model;

use Magento\Quote\Model\Quote;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use \PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pmclain\OneClickCheckout\Model\QuoteBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\Quote\Model\QuoteFactory;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\ItemBuilderFactory;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\ItemBuilder;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\AddressBuilder;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\ShippingMethodBuilder;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\PaymentBuilder;

class QuoteBuilderTest extends TestCase
{
    /**
     * @var QuoteBuilder
     */
    private $quoteBuilder;

    /**
     * @var QuoteFactory|MockObject
     */
    private $quoteFactoryMock;

    /**
     * @var Quote|MockObject
     */
    private $quoteMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var Store|MockObject
     */
    private $storeMock;

    /**
     * @var ItemBuilderFactory|MockObject
     */
    private $itemBuilderFactoryMock;

    /**
     * @var ItemBuilder|MockObject
     */
    private $itemBuilderMock;

    /**
     * @var AddressBuilder|MockObject
     */
    private $addressBuilderMock;

    /**
     * @var ShippingMethodBuilder|MockObject
     */
    private $shippingMethodBuilderMock;

    /**
     * @var PaymentBuilder|MockObject
     */
    private $paymentBuilderMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->quoteFactoryMock = $this->getMockBuilder(QuoteFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->setMethods(['setTotalsCollectedFlag', 'collectTotals', 'save', 'isVirtual', 'setCustomer', 'setShippingAddress', 'setBillingAddress'])
            ->getMock();

        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->getMockForAbstractClass();

        $this->storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemBuilderFactoryMock = $this->getMockBuilder(ItemBuilderFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemBuilderMock = $this->getMockBuilder(ItemBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->addressBuilderMock = $this->getMockBuilder(AddressBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->shippingMethodBuilderMock = $this->getMockBuilder(ShippingMethodBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentBuilderMock = $this->getMockBuilder(PaymentBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->quoteMock);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getId');

        $this->itemBuilderFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->itemBuilderMock);

        $this->addressBuilderMock->expects($this->once())
            ->method('getBillingAddress');

        $this->paymentBuilderMock->expects($this->once())
            ->method('setPaymentMethod')
            ->with($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('setTotalsCollectedFlag')
            ->with(false)
            ->willReturnSelf();

        $this->quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->quoteMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        $this->quoteBuilder = $objectManager->getObject(
            QuoteBuilder::class,
            [
                'quoteFactory' => $this->quoteFactoryMock,
                'storeManager' => $this->storeManagerMock,
                'itemBuilderFactory' => $this->itemBuilderFactoryMock,
                'addressBuilder' => $this->addressBuilderMock,
                'shippingMethodBuilder' => $this->shippingMethodBuilderMock,
                'paymentBuilder' => $this->paymentBuilderMock,
            ]
        );
    }

    public function testCreateQuote()
    {
        $this->quoteMock->expects($this->once())
            ->method('isVirtual')
            ->willReturn(false);

        $this->addressBuilderMock->expects($this->once())
            ->method('getShippingAddress');

        $this->shippingMethodBuilderMock->expects($this->once())
            ->method('setShippingMethod')
            ->with($this->quoteMock);

        $this->quoteBuilder->createQuote();
    }

    public function testCreateQuoteVirtual()
    {
        $this->quoteMock->expects($this->once())
            ->method('isVirtual')
            ->willReturn(true);

        $this->addressBuilderMock->expects($this->never())
            ->method('getShippingAddress');

        $this->shippingMethodBuilderMock->expects($this->never())
            ->method('setShippingMethod')
            ->with($this->quoteMock);

        $this->quoteBuilder->createQuote();
    }
}
