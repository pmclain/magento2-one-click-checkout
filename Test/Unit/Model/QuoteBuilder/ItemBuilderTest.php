<?php

namespace Pmclain\OneClickCheckout\Test\Unit\Model;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart\RequestInfoFilterInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Locale\Resolver;
use Magento\Quote\Model\Quote;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use \PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\ItemBuilder;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\Data\StoreInterface;

class ItemBuilderTest extends TestCase
{
    /**
     * @var ItemBuilder
     */
    private $itemBuilder;

    /**
     * @var Http|MockObject
     */
    private $requestMock;

    /**
     * @var ResolverInterface|MockObject
     */
    private $resolverMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var StoreInterface|MockObject
     */
    private $storeMock;

    /**
     * @var ProductRepositoryInterface|MockObject
     */
    private $productRepositoryMock;

    /**
     * @var Product|MockObject
     */
    private $productMock;

    /**
     * @var RequestInfoFilterInterface|MockObject
     */
    private $requestInfoFilterMock;

    /**
     * @var Quote|MockObject
     */
    private $quoteMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resolverMock = $this->getMockBuilder(ResolverInterface::class)
            ->getMockForAbstractClass();

        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->getMockForAbstractClass();

        $this->storeMock = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();

        $this->productRepositoryMock = $this->getMockBuilder(ProductRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestInfoFilterMock = $this->getMockBuilder(RequestInfoFilterInterface::class)
            ->getMockForAbstractClass();

        $this->quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resolverMock->expects($this->once())
            ->method('getLocale')
            ->willReturn(Resolver::DEFAULT_LOCALE);

        $this->requestMock->expects($this->once())
            ->method('getParams')
            ->willReturn([
                'qty' => '10',
            ]);

        $this->itemBuilder = $objectManager->getObject(
            ItemBuilder::class,
            [
                'request' => $this->requestMock,
                'quote' => $this->quoteMock,
                'resolver' => $this->resolverMock,
                'productRepository' => $this->productRepositoryMock,
                'storeManager' => $this->storeManagerMock,
                'requestInfoFilter' => $this->requestInfoFilterMock,
            ]
        );
    }

    public function testAddItems()
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('product')
            ->willReturn('210');

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getId');

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->willReturn($this->productMock);

        $this->requestInfoFilterMock->expects($this->once())
            ->method('filter');

        $this->itemBuilder->addItems();
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testAddItemNoProductParam()
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('product')
            ->willReturn(null);

        $this->itemBuilder->addItems();
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testAddItemNoProduct()
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('product')
            ->willReturn('210');

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getId');

        $this->productRepositoryMock->expects($this->once())
            ->method('getById')
            ->willThrowException(new NoSuchEntityException());

        $this->itemBuilder->addItems();
    }
}
