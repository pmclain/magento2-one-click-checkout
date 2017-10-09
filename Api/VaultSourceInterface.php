<?php

namespace Pmclain\OneClickCheckout\Api;

interface VaultSourceInterface
{
    /**
     * @param \Magento\Vault\Api\Data\PaymentTokenInterface $token
     * @return array
     */
    public function getPaymentData($token);
}
