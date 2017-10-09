<?php

namespace Pmclain\OneClickCheckout\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Pmclain\OneClickCheckout\Api\ButtonInterface;
use Magento\Customer\Model\Session;
use Pmclain\OneClickCheckout\Api\DefaultPaymentRepositoryInterface;

class Button implements ButtonInterface
{
    const CACHE_PREFIX = 'occ_available_customer_';

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var DefaultPaymentRepositoryInterface
     */
    protected $defaultPaymentRepository;

    /**
     * Button constructor.
     * @param Session $session
     * @param DefaultPaymentRepositoryInterface $defaultPaymentRepository
     */
    public function __construct(
        Session $session,
        DefaultPaymentRepositoryInterface $defaultPaymentRepository
    ) {
        $this->session = $session;
        $this->defaultPaymentRepository = $defaultPaymentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function canShow()
    {
        return $this->session->isLoggedIn() && $this->hasDefaultAddresses() && $this->hasDefaultPayment();
    }

    /**
     * @return bool
     */
    protected function hasDefaultAddresses()
    {
        $customer = $this->session->getCustomer();

        return $customer->getDefaultBillingAddress() && $customer->getDefaultShippingAddress();
    }

    /**
     * @return bool
     */
    protected function hasDefaultPayment()
    {
        try {
            $this->defaultPaymentRepository->getByCustomerId($this->session->getCustomerId());
            return true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
