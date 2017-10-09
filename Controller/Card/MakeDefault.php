<?php

namespace Pmclain\OneClickCheckout\Controller\Card;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Vault\Api\PaymentTokenManagementInterface;
use Magento\Customer\Model\Session;
use Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterface;
use Pmclain\OneClickCheckout\Api\DefaultPaymentRepositoryInterface;
use Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterfaceFactory;

class MakeDefault extends Action
{
    /**
     * @var PaymentTokenManagementInterface
     */
    protected $paymentTokenManagement;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var DefaultPaymentRepositoryInterface
     */
    protected $defaultPaymentRepository;

    /**
     * @var DefaultPaymentInterfaceFactory
     */
    protected $defaultPaymentFactory;

    /**
     * MakeDefault constructor.
     * @param Context $context
     * @param PaymentTokenManagementInterface $paymentTokenManagement
     * @param Session $session
     * @param DefaultPaymentRepositoryInterface $defaultPaymentRepository
     * @param DefaultPaymentInterfaceFactory $defaultPaymentFactory
     */
    public function __construct(
        Context $context,
        PaymentTokenManagementInterface $paymentTokenManagement,
        Session $session,
        DefaultPaymentRepositoryInterface $defaultPaymentRepository,
        DefaultPaymentInterfaceFactory $defaultPaymentFactory
    ) {
        parent::__construct($context);
        $this->paymentTokenManagement = $paymentTokenManagement;
        $this->session = $session;
        $this->defaultPaymentRepository = $defaultPaymentRepository;
        $this->defaultPaymentFactory = $defaultPaymentFactory;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        if (!$this->getRequest()->isPost()) {
            return $resultRedirect;
        }

        $token = $this->paymentTokenManagement->getByPublicHash(
            $this->getRequest()->getParam('public_hash'),
            $this->session->getCustomerId()
        );

        if (!$token) {
            $this->messageManager->addErrorMessage(
                __('The selected stored payment method could not be found.')
            );
            return $resultRedirect;
        }

        $this->setDefaultPaymentMethod($token);

        $this->messageManager->addSuccessMessage(
            __('Your default payment method has been updated.')
        );

        return $resultRedirect;
    }

    /**
     * @param \Magento\Vault\Api\Data\PaymentTokenInterface $token
     */
    private function setDefaultPaymentMethod($token)
    {
        try {
            $defaultPayment = $this->defaultPaymentRepository->getByCustomerId($this->session->getCustomerId());
            $defaultPayment->setPaymentTokenId($token->getEntityId());
            $this->defaultPaymentRepository->save($defaultPayment);
        } catch (NoSuchEntityException $e) {
            $defaultPayment = $this->defaultPaymentFactory->create();
            $defaultPayment->setCustomerId($this->session->getCustomerId());
            $defaultPayment->setPaymentTokenId($token->getEntityId());
            $this->defaultPaymentRepository->save($defaultPayment);
        }
    }
}
