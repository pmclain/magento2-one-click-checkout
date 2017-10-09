<?php

namespace Pmclain\OneClickCheckout\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Vault\Api\PaymentTokenManagementInterface;
use Pmclain\OneClickCheckout\Api\DefaultPaymentRepositoryInterface;
use Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterfaceFactory;

class SetDefault extends Action
{
    const ADMIN_RESOURCE = 'Magento_Checkout::checkout';

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var PaymentTokenManagementInterface
     */
    protected $paymentTokenManagement;

    /**
     * @var DefaultPaymentRepositoryInterface
     */
    protected $defaultPaymentRepository;

    /**
     * @var DefaultPaymentInterfaceFactory
     */
    protected $defaultPaymentFactory;

    /**
     * SetDefault constructor.
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param CollectionFactory $customerCollectionFactory
     * @param PaymentTokenManagementInterface $paymentTokenManagement
     * @param DefaultPaymentRepositoryInterface $defaultPaymentRepository
     * @param DefaultPaymentInterfaceFactory $defaultPaymentFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CollectionFactory $customerCollectionFactory,
        PaymentTokenManagementInterface $paymentTokenManagement,
        DefaultPaymentRepositoryInterface $defaultPaymentRepository,
        DefaultPaymentInterfaceFactory $defaultPaymentFactory
    ) {
        parent::__construct($context);
        $this->jsonResultFactory = $jsonFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->paymentTokenManagement = $paymentTokenManagement;
        $this->defaultPaymentRepository = $defaultPaymentRepository;
        $this->defaultPaymentFactory = $defaultPaymentFactory;
    }

    public function execute()
    {
        $customerCollection = $this->customerCollectionFactory->create();
        $customerIds = $customerCollection->getAllIds();

        foreach ($customerIds as $customerId) {
            try {
                $this->defaultPaymentRepository->getByCustomerId($customerId);
            } catch (NoSuchEntityException $e) {
                $this->setDefaultPayment($customerId);
            }
        }

        return $this->jsonResultFactory->create()->setData(['success' => true]);
    }

    private function setDefaultPayment($customerId)
    {
        $customerTokens = $this->paymentTokenManagement->getVisibleAvailableTokens($customerId);

        if (!$customerTokens) {
            return;
        }

        $customerTokens = array_reverse($customerTokens);

        /** @var \Magento\Vault\Api\Data\PaymentTokenInterface $customerToken */
        $customerToken = current($customerTokens);

        $defaultPayment = $this->defaultPaymentFactory->create();
        $defaultPayment->setPaymentTokenId($customerToken->getEntityId());
        $defaultPayment->setCustomerId($customerToken->getCustomerId());
        $this->defaultPaymentRepository->save($defaultPayment);
    }
}
