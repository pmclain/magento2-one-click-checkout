<?php

namespace Pmclain\OneClickCheckout\Model;

use Pmclain\OneClickCheckout\Api\DefaultPaymentRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Vault\Api\PaymentTokenRepositoryInterface;

class DefaultPaymentProvider
{
    /**
     * @var DefaultPaymentRepositoryInterface
     */
    protected $defaultPaymentRepository;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PaymentTokenRepositoryInterface
     */
    protected $paymentTokenRepository;

    /**
     * DefaultPaymentProvider constructor.
     * @param DefaultPaymentRepositoryInterface $defaultPaymentRepository
     * @param Session $session
     * @param PaymentTokenRepositoryInterface $paymentTokenRepository
     */
    public function __construct(
        DefaultPaymentRepositoryInterface $defaultPaymentRepository,
        Session $session,
        PaymentTokenRepositoryInterface $paymentTokenRepository
    ) {
        $this->defaultPaymentRepository = $defaultPaymentRepository;
        $this->session = $session;
        $this->paymentTokenRepository = $paymentTokenRepository;
    }

    /**
     * @return \Magento\Vault\Api\Data\PaymentTokenInterface
     */
    public function getDefaultPayment()
    {
        $defaultPayment = $this->defaultPaymentRepository->getByCustomerId($this->session->getCustomerId());

        return $this->paymentTokenRepository->getById($defaultPayment->getPaymentTokenId());
    }
}
