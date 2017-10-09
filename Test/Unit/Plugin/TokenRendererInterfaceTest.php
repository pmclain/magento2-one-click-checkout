<?php

namespace Pmclain\OneClickCheckout\Test\Unit\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use \PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterface;
use Pmclain\OneClickCheckout\Api\DefaultPaymentRepositoryInterface;
use Pmclain\OneClickCheckout\Plugin\TokenRendererInterface;
use Magento\Vault\Block\TokenRendererInterface as Block;

class TokenRendererInterfaceTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var DefaultPaymentRepositoryInterface|MockObject
     */
    private $defaultPaymentRepositoryMock;

    /**
     * @var Block|MockObject
     */
    private $tokenRendererMock;

    /**
     * @var DefaultPaymentInterface|MockObject
     */
    private $defaultPaymentMock;

    /**
     * @var Session|MockObject
     */
    private $sessionMock;

    /**
     * @var PaymentTokenInterface|MockObject
     */
    private $paymentTokenMock;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->defaultPaymentRepositoryMock = $this->getMockBuilder(DefaultPaymentRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->tokenRendererMock = $this->getMockBuilder(Block::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->defaultPaymentMock = $this->getMockBuilder(DefaultPaymentInterface::class)
            ->getMockForAbstractClass();

        $this->paymentTokenMock = $this->getMockBuilder(PaymentTokenInterface::class)
            ->setMethods(['setData'])
            ->getMockForAbstractClass();

        $this->sessionMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn('10');
    }

    public function testAfterGetToken()
    {
        $tokenId = '17';

        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->willReturn($this->defaultPaymentMock);

        $this->defaultPaymentMock->expects($this->once())
            ->method('getPaymentTokenId')
            ->willReturn($tokenId);

        $this->paymentTokenMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn($tokenId);

        $this->paymentTokenMock->expects($this->once())
            ->method('setData')
            ->with(TokenRendererInterface::KEY_IS_DEFAULT, true);

        $tokenRenderPlugin = $this->objectManager->getObject(
            TokenRendererInterface::class,
            [
                'defaultPaymentRepository' => $this->defaultPaymentRepositoryMock,
                'session' => $this->sessionMock,
            ]
        );

        $tokenRenderPlugin->afterGetToken(
            $this->tokenRendererMock,
            $this->paymentTokenMock
        );
    }

    public function testAfterGetTokenNotDefault()
    {
        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->willThrowException(new NoSuchEntityException());

        $this->paymentTokenMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn('15');

        $this->paymentTokenMock->expects($this->once())
            ->method('setData')
            ->with(TokenRendererInterface::KEY_IS_DEFAULT, false);

        $tokenRenderPlugin = $this->objectManager->getObject(
            TokenRendererInterface::class,
            [
                'defaultPaymentRepository' => $this->defaultPaymentRepositoryMock,
                'session' => $this->sessionMock,
            ]
        );

        $tokenRenderPlugin->afterGetToken(
            $this->tokenRendererMock,
            $this->paymentTokenMock
        );

    }
}
