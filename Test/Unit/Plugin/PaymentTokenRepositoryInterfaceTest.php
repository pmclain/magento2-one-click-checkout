<?php

namespace Pmclain\OneClickCheckout\Test\Unit\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use \PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterface;
use Pmclain\OneClickCheckout\Api\DefaultPaymentRepositoryInterface;
use Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterfaceFactory;
use Pmclain\OneClickCheckout\Plugin\PaymentTokenRepositoryInterface;
use Magento\Vault\Model\PaymentTokenRepository;
use Psr\Log\LoggerInterface;

class PaymentTokenRepositoryInterfaceTest extends TestCase
{
    /**
     * @var PaymentTokenRepositoryInterface
     */
    private $paymentTokenRepositoryPlugin;

    /**
     * @var PaymentTokenRepository|MockObject
     */
    private $paymentTokenRepositoryMock;

    /**
     * @var PaymentTokenInterface|MockObject
     */
    private $paymentTokenMock;

    /**
     * @var DefaultPaymentRepositoryTest|MockObject
     */
    private $defaultPaymentRepositoryMock;

    /**
     * @var DefaultPaymentInterface|MockObject
     */
    private $defaultPaymentMock;

    /**
     * @var DefaultPaymentInterfaceFactory|MockObject
     */
    private $defaultPaymentFactoryMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->paymentTokenRepositoryMock = $this->getMockBuilder(PaymentTokenRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentTokenMock = $this->getMockBuilder(PaymentTokenInterface::class)
            ->getMockForAbstractClass();

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->getMockForAbstractClass();

        $this->defaultPaymentRepositoryMock = $this->getMockBuilder(DefaultPaymentRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->defaultPaymentFactoryMock = $this->getMockBuilder(DefaultPaymentInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->defaultPaymentMock = $this->getMockBuilder(DefaultPaymentInterface::class)
            ->getMockForAbstractClass();

        $this->paymentTokenRepositoryPlugin = $objectManager->getObject(
            PaymentTokenRepositoryInterface::class,
            [
                'logger' => $this->loggerMock,
                'defaultPaymentRepository' => $this->defaultPaymentRepositoryMock,
                'defaultPaymentFactory' => $this->defaultPaymentFactoryMock,
            ]
        );
    }

    public function testAfterSave()
    {
        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->willReturn($this->defaultPaymentMock);

        $this->paymentTokenMock->expects($this->once())
            ->method('getCustomerId');

        $this->defaultPaymentMock->expects($this->once())
            ->method('setPaymentTokenId');

        $this->paymentTokenMock->expects($this->once())
            ->method('getEntityId');

        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->defaultPaymentMock);

        $this->paymentTokenRepositoryPlugin->afterSave(
            $this->paymentTokenRepositoryMock,
            $this->paymentTokenMock
        );
    }

    public function testAfterSaveNewDefault()
    {
        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->willThrowException(new NoSuchEntityException());

        $this->paymentTokenMock->expects($this->exactly(2))
            ->method('getCustomerId');

        $this->defaultPaymentFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->defaultPaymentMock);

        $this->defaultPaymentMock->expects($this->once())
            ->method('setCustomerId');

        $this->defaultPaymentMock->expects($this->once())
            ->method('setPaymentTokenId');

        $this->paymentTokenMock->expects($this->once())
            ->method('getEntityId');

        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->defaultPaymentMock);

        $this->paymentTokenRepositoryPlugin->afterSave(
            $this->paymentTokenRepositoryMock,
            $this->paymentTokenMock
        );
    }

    public function testAfterSaveNewDefaultWithException()
    {
        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('getByCustomerId')
            ->willThrowException(new NoSuchEntityException());

        $this->paymentTokenMock->expects($this->exactly(2))
            ->method('getCustomerId');

        $this->defaultPaymentFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->defaultPaymentMock);

        $this->defaultPaymentMock->expects($this->once())
            ->method('setCustomerId');

        $this->defaultPaymentMock->expects($this->once())
            ->method('setPaymentTokenId');

        $this->paymentTokenMock->expects($this->once())
            ->method('getEntityId');

        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('save')
            ->willThrowException(new \Exception('Could not save.'));

        $this->loggerMock->expects($this->once())
            ->method('critical');

        $this->paymentTokenRepositoryPlugin->afterSave(
            $this->paymentTokenRepositoryMock,
            $this->paymentTokenMock
        );
    }

    public function testBeforeDelete()
    {
        $this->paymentTokenMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn('10');

        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('getByPaymentTokenId')
            ->willReturn($this->defaultPaymentMock);

        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($this->defaultPaymentMock);

        $this->paymentTokenRepositoryPlugin->beforeDelete(
            $this->paymentTokenRepositoryMock,
            $this->paymentTokenMock
        );
    }

    public function testBeforeDeleteNoDefault()
    {
        $this->paymentTokenMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn('10');

        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('getByPaymentTokenId')
            ->willThrowException(new NoSuchEntityException());

        $this->paymentTokenRepositoryPlugin->beforeDelete(
            $this->paymentTokenRepositoryMock,
            $this->paymentTokenMock
        );
    }

    public function testBeforeDeleteWithException()
    {
        $this->paymentTokenMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn('10');

        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('getByPaymentTokenId')
            ->willReturn($this->defaultPaymentMock);

        $this->defaultPaymentRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($this->defaultPaymentMock)
            ->willThrowException(new \Exception(''));

        $this->loggerMock->expects($this->once())
            ->method('critical');

        $this->paymentTokenRepositoryPlugin->beforeDelete(
            $this->paymentTokenRepositoryMock,
            $this->paymentTokenMock
        );
    }
}
