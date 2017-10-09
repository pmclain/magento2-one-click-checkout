<?php

namespace Pmclain\OneClickCheckout\Test\Unit\Model;

use Magento\Customer\Model\Session;
use Magento\Vault\Api\PaymentTokenRepositoryInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use \PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterface;
use Pmclain\OneClickCheckout\Api\DefaultPaymentRepositoryInterface;
use Pmclain\OneClickCheckout\Model\DefaultPaymentProvider;


class DefaultPaymentProviderTest extends TestCase
{
    /**
     * @var DefaultPaymentProvider
     */
    private $defaultPaymentProvider;

    /**
     * @var DefaultPaymentRepositoryInterface|MockObject
     */
    private $defaultPaymentRepositoryMock;

    /**
     * @var DefaultPaymentInterface|MockObject
     */
    private $defaultPaymentMock;

    /**
     * @var Session|MockObject
     */
    private $sessionMock;

    /**
     * @var PaymentTokenRepositoryInterface|MockObject
     */
    private $paymentTokenRepositoryMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->defaultPaymentRepositoryMock = $this->getMockBuilder(DefaultPaymentRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->paymentTokenRepositoryMock = $this->getMockBuilder(PaymentTokenRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->defaultPaymentMock = $this->getMockBuilder(DefaultPaymentInterface::class)
            ->getMockForAbstractClass();

        $this->defaultPaymentProvider = $objectManager->getObject(
            DefaultPaymentProvider::class,
            [
                'session' => $this->sessionMock,
                'defaultPaymentRepository' => $this->defaultPaymentRepositoryMock,
                'paymentTokenRepository' => $this->paymentTokenRepositoryMock,
            ]
        );
    }

    public function testGetDefaultPayment()
    {
        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->willReturn($this->defaultPaymentMock);

        $this->sessionMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn('1');

        $this->defaultPaymentMock->expects($this->once())
            ->method('getPaymentTokenId')
            ->willReturn('10');

        $this->paymentTokenRepositoryMock->expects($this->once())
            ->method('getById')
            ->willReturn(true);

        $this->defaultPaymentProvider->getDefaultPayment();
    }
}
