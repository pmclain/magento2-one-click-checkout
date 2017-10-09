<?php

namespace Pmclain\OneClickCheckout\Test\Unit\Model;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use \PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Pmclain\OneClickCheckout\Model\DefaultPaymentRepository;
use Pmclain\OneClickCheckout\Model\ResourceModel\DefaultPayment as Resource;
use Pmclain\OneClickCheckout\Model\DefaultPayment;
use Pmclain\OneClickCheckout\Model\DefaultPaymentFactory;

class DefaultPaymentRepositoryTest extends TestCase
{
    /**
     * @var DefaultPaymentRepository
     */
    private $defaultPaymentRepository;

    /**
     * @var Resource|MockObject
     */
    private $resourceMock;

    /**
     * @var DefaultPayment|MockObject
     */
    private $defaultPaymentMock;

    /**
     * @var DefaultPaymentFactory|MockObject
     */
    private $factoryMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resourceMock = $this->getMockBuilder(Resource::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->defaultPaymentMock = $this->getMockBuilder(DefaultPayment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factoryMock = $this->getMockBuilder(DefaultPaymentFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->defaultPaymentRepository = $objectManager->getObject(
            DefaultPaymentRepository::class,
            [
                'resource' => $this->resourceMock,
                'factory' => $this->factoryMock,
            ]
        );
    }

    public function testSave()
    {
        $this->defaultPaymentRepository->save($this->defaultPaymentMock);
    }

    public function testGetById()
    {
        $this->factoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->defaultPaymentMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->willReturn($this->defaultPaymentMock);

        $this->defaultPaymentMock->expects($this->once())
            ->method('getId')
            ->willReturn('200');

        $this->defaultPaymentRepository->getById('200');
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByIdException()
    {
        $this->factoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->defaultPaymentMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->willReturn($this->defaultPaymentMock);

        $this->defaultPaymentRepository->getById(101);
    }

    public function testGetByCustomerId()
    {
        $this->factoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->defaultPaymentMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->willReturn($this->defaultPaymentMock);

        $this->defaultPaymentMock->expects($this->once())
            ->method('getId')
            ->willReturn('200');

        $this->defaultPaymentRepository->getByCustomerId('200');
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByCustomerIdException()
    {
        $this->factoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->defaultPaymentMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->willReturn($this->defaultPaymentMock);

        $this->defaultPaymentRepository->getByCustomerId(101);
    }

    public function testGetByPaymentTokenId()
    {
        $this->factoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->defaultPaymentMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->willReturn($this->defaultPaymentMock);

        $this->defaultPaymentMock->expects($this->once())
            ->method('getId')
            ->willReturn('200');

        $this->defaultPaymentRepository->getByPaymentTokenId('200');
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetByPaymentTokenIdException()
    {
        $this->factoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->defaultPaymentMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->willReturn($this->defaultPaymentMock);

        $this->defaultPaymentRepository->getByPaymentTokenId(101);
    }

    public function testDelete()
    {
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $this->defaultPaymentRepository->delete($this->defaultPaymentMock);
    }
}
