<?php

namespace Pmclain\OneClickCheckout\Model\QuoteBuilder;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\ScopeInterface;

class ShippingMethodBuilder
{
    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * ShippingMethodBuilder constructor.
     * @param ScopeConfigInterface $config
     */
    public function __construct(
        ScopeConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @throws NotFoundException
     */
    public function setShippingMethod($quote)
    {
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true);
        $shippingAddress->collectShippingRates();
        $rates = $shippingAddress->getGroupedAllShippingRates();

        $configMethod = $this->config->getValue('checkout/one_click_checkout/shipping', ScopeInterface::SCOPE_STORE);
        $method = false;

        foreach ($rates as $carrier) {
            /** @var \Magento\Quote\Model\Quote\Address\Rate $method */
            foreach ($carrier as $carrierMethod) {
                if ($carrierMethod->getCode() === $configMethod) {
                    $method = $carrierMethod;
                }
            }
        }

        if (!$method) {
            throw new NotFoundException(__('The configured shipping method was not availabe.'));
        }

        $shippingAddress->setShippingAmount($method->getPrice());
        $shippingAddress->setBaseShippingAmount($method->getPrice());
        $shippingAddress->setShippingMethod($method->getCode());
        $shippingAddress->setShippingDescription($method->getMethodTitle());
    }
}
