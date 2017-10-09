<?php

namespace Pmclain\OneClickCheckout\Test\Unit\Model;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use \PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Pmclain\OneClickCheckout\Api\DefaultPaymentRepositoryInterface;
use Pmclain\OneClickCheckout\Model\Button;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Customer;


class ButtonTest extends TestCase
{
    /**
     * @var Button
     */
    private $button;

    /**
     * @var Session|MockObject
     */
    private $sessionMock;

    /**
     * @var Customer|MockObject
     */
    private $customerMock;

    /**
     * @var DefaultPaymentRepositoryInterface|MockObject
     */
    private $defaultPaymentRepositoryMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerMock = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->defaultPaymentRepositoryMock = $this->getMockBuilder(DefaultPaymentRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->button = $objectManager->getObject(
            Button::class,
            [
                'session' => $this->sessionMock,
                'defaultPaymentRepository' => $this->defaultPaymentRepositoryMock,
            ]
        );
    }

    public function testCanShowLoggedOut()
    {
        $this->sessionMock->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(false);

        $this->assertFalse($this->button->canShow());
    }

    public function testCanShowNoAddress()
    {
        $this->sessionMock->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(true);

        $this->sessionMock->expects($this->any())
            ->method('getCustomer')
            ->willReturn($this->customerMock);

        $this->assertFalse($this->button->canShow());
    }

    public function testCanShowNoDefault()
    {
        $this->sessionMock->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(true);

        $this->sessionMock->expects($this->any())
            ->method('getCustomer')
            ->willReturn($this->customerMock);

        $this->customerMock->expects($this->once())
            ->method('getDefaultBillingAddress')
            ->willReturn(true);

        $this->customerMock->expects($this->once())
            ->method('getDefaultShippingAddress')
            ->willReturn(true);

        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->willThrowException(new NoSuchEntityException());

        $this->assertFalse($this->button->canShow());
    }

    public function testCanShow()
    {
        $this->sessionMock->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(true);

        $this->sessionMock->expects($this->any())
            ->method('getCustomer')
            ->willReturn($this->customerMock);

        $this->customerMock->expects($this->once())
            ->method('getDefaultBillingAddress')
            ->willReturn(true);

        $this->customerMock->expects($this->once())
            ->method('getDefaultShippingAddress')
            ->willReturn(true);

        $this->assertTrue($this->button->canShow());
    }
}
