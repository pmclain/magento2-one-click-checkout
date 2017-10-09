<?php

namespace Pmclain\OneClickCheckout\Model\QuoteBuilder;

use Pmclain\OneClickCheckout\Model\DefaultPaymentProvider;
use Pmclain\OneClickCheckout\Model\VaultPool;

class PaymentBuilder
{
    /**
     * @var DefaultPaymentProvider
     */
    protected $defaultPaymentProvider;

    /**
     * @var VaultPool
     */
    protected $vaultPool;

    /**
     * PaymentBuilder constructor.
     * @param DefaultPaymentProvider $defaultPaymentProvider
     * @param VaultPool $vaultPool
     */
    public function __construct(
        DefaultPaymentProvider $defaultPaymentProvider,
        VaultPool $vaultPool
    ) {
        $this->defaultPaymentProvider = $defaultPaymentProvider;
        $this->vaultPool = $vaultPool;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     */
    public function setPaymentMethod($quote)
    {
        $token = $this->defaultPaymentProvider->getDefaultPayment();

        $payment = $quote->getPayment();

        $payment->setQuote($quote);

        $payment->importData($this->vaultPool->getPaymentDataArray($token));

        if ($quote->isVirtual()) {
            $quote->getBillingAddress()->setPaymentMethod($payment->getMethod());
        } else {
            $quote->getShippingAddress()->setPaymentMethod($payment->getMethod());
            $quote->getShippingAddress()->setCollectShippingRates(true);
        }
    }
}
