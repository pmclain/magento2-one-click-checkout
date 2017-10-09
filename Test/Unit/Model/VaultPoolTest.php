<?php

namespace Pmclain\OneClickCheckout\Test\Unit\Model;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use \PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pmclain\OneClickCheckout\Model\VaultPool;
use Pmclain\OneClickCheckout\Api\VaultSourceInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;

class VaultPoolTest extends TestCase
{
    /**
     * @var VaultPool
     */
    private $vaultPool;

    /**
     * @var VaultSourceInterface|MockObject
     */
    private $vaultSourceMock;

    /**
     * @var PaymentTokenInterface|MockObject
     */
    private $paymentTokenMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->paymentTokenMock = $this->getMockBuilder(PaymentTokenInterface::class)
            ->getMockForAbstractClass();

        $this->vaultSourceMock = $this->getMockBuilder(VaultSourceInterface::class)
            ->getMockForAbstractClass();

        $this->vaultPool = $objectManager->getObject(
            VaultPool::class,
            [
                'vaultSourceMap' => [
                    'testMethod' => $this->vaultSourceMock,
                ],
            ]
        );
    }

    public function testGetPaymentDataArray()
    {
        $this->paymentTokenMock->expects($this->once())
            ->method('getPaymentMethodCode')
            ->willReturn('testMethod');

        $this->vaultSourceMock->expects($this->once())
            ->method('getPaymentData')
            ->willReturn([]);

        $this->vaultPool->getPaymentDataArray($this->paymentTokenMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testGetPaymentDataArrayWithoutSource()
    {
        $this->paymentTokenMock->expects($this->once())
            ->method('getPaymentMethodCode')
            ->willReturn('someOtherMethod');

        $this->vaultPool->getPaymentDataArray($this->paymentTokenMock);
    }
}
