<?php

namespace Pmclain\OneClickCheckout\Model;

use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\ItemBuilderFactory;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\ItemBuilder;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\AddressBuilder;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\ShippingMethodBuilder;
use Pmclain\OneClickCheckout\Model\QuoteBuilder\PaymentBuilder;

class QuoteBuilder
{
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ItemBuilderFactory
     */
    protected $itemBuilderFactory;

    /**
     * @var AddressBuilder
     */
    protected $addressBuilder;

    /**
     * @var ShippingMethodBuilder
     */
    protected $shippingMethodBuilder;

    /**
     * @var PaymentBuilder
     */
    protected $paymentBuilder;

    /**
     * QuoteBuilder constructor.
     * @param QuoteFactory $quoteFactory
     * @param StoreManagerInterface $storeManager
     * @param Session $session
     * @param ItemBuilderFactory $itemBuilderFactory
     * @param AddressBuilder $addressBuilder
     * @param ShippingMethodBuilder $shippingMethodBuilder
     * @param PaymentBuilder $paymentBuilder
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        StoreManagerInterface $storeManager,
        Session $session,
        ItemBuilderFactory $itemBuilderFactory,
        AddressBuilder $addressBuilder,
        ShippingMethodBuilder $shippingMethodBuilder,
        PaymentBuilder $paymentBuilder
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->storeManager = $storeManager;
        $this->session = $session;
        $this->itemBuilderFactory = $itemBuilderFactory;
        $this->addressBuilder = $addressBuilder;
        $this->shippingMethodBuilder = $shippingMethodBuilder;
        $this->paymentBuilder = $paymentBuilder;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function createQuote()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteFactory->create();
        $quote->setStoreId($this->storeManager->getStore()->getId());
        $quote->setCustomer($this->session->getCustomerDataObject());
        $quote->setCustomerIsGuest(0);

        /** @var ItemBuilder $itemBuilder */
        $itemBuilder = $this->itemBuilderFactory->create(['quote' => $quote]);
        $itemBuilder->addItems();

        if (!$quote->isVirtual()) {
            $quote->setShippingAddress($this->addressBuilder->getShippingAddress());
            $this->shippingMethodBuilder->setShippingMethod($quote);
        }

        $quote->setBillingAddress($this->addressBuilder->getBillingAddress());

        $this->paymentBuilder->setPaymentMethod($quote);

        $quote->setTotalsCollectedFlag(false)->collectTotals()->save();

        return $quote;
    }
}
