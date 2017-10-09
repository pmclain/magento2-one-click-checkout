<?php

namespace Pmclain\OneClickCheckout\Model\Config\Source;

class Shipping implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shipConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Shipping constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config $shipConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shipConfig
    ) {
        $this->shipConfig = $shipConfig;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getShippingMethods();
    }

    /**
     * @return array
     */
    private function getShippingMethods()
    {
        $activeCarriers = $this->shipConfig->getActiveCarriers();
        $methods = [];

        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                $carrierTitle = $this->scopeConfig->getValue(
                    'carriers/' . $carrierCode . '/title',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );

                foreach ($carrierMethods as $methodCode => $method) {
                    $code = $carrierCode.'_'.$methodCode;
                    if ($method) {
                        $methods[] = ['value' => $code, 'label' => $carrierTitle . ' : ' . $method];
                    }
                }
            }
        }

        return $methods;
    }
}
