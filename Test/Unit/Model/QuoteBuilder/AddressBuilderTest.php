<?php

namespace Pmclain\OneClickCheckout\Test\Unit\Model\QuoteBuilder;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use \PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\AddressBuilder;
use Magento\Customer\Model\Session;
use Magento\Quote\Api\Data\AddressInterfaceFactory;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class AddressBuilderTest extends TestCase
{
    /**
     * @var AddressBuilder
     */
    private $addressBuilder;

    /**
     * @var Session|MockObject
     */
    private $sessionMock;

    /**
     * @var AddressInterfaceFactory|MockObject
     */
    private $addressFactoryMock;

    /**
     * @var AddressInterface|MockObject
     */
    private $addressMock;

    /**
     * @var CustomerInterface|MockObject
     */
    private $customerMock;

    /**
     * @var AddressRepositoryInterface|MockObject
     */
    private $addressRepositoryMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->addressFactoryMock = $this->getMockBuilder(AddressInterfaceFactory::class)
            ->setMethods(['create'])
            ->getMockForAbstractClass();

        $this->addressMock = $this->getMockBuilder(AddressInterface::class)
            ->setMethods(['importCustomerAddressData'])
            ->getMockForAbstractClass();

        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerMock = $this->getMockBuilder(CustomerInterface::class)
            ->getMockForAbstractClass();

        $this->addressRepositoryMock = $this->getMockBuilder(AddressRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->addressFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->addressMock);

        $this->sessionMock->expects($this->once())
            ->method('getCustomerDataObject')
            ->willReturn($this->customerMock);

        $this->addressRepositoryMock->expects($this->once())
            ->method('getById');

        $this->addressMock->expects($this->once())
            ->method('importCustomerAddressData');

        $this->addressBuilder = $objectManager->getObject(
            AddressBuilder::class,
            [
                'session' => $this->sessionMock,
                'addressFactory' => $this->addressFactoryMock,
                'addressRepository' => $this->addressRepositoryMock,
            ]
        );
    }

    public function testGetShippingAddress()
    {
        $this->customerMock->expects($this->once())
            ->method('getDefaultShipping');

        $this->addressBuilder->getShippingAddress();
    }

    public function testGetBillingAddress()
    {
        $this->customerMock->expects($this->once())
            ->method('getDefaultBilling');

        $this->addressBuilder->getBillingAddress();
    }
}
