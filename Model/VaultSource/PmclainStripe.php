<?php

namespace Pmclain\OneClickCheckout\Model\VaultSource;

use Pmclain\OneClickCheckout\Api\VaultSourceInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;

class PmclainStripe implements VaultSourceInterface
{
    /**
     * @param PaymentTokenInterface $token
     * @return array
     */
    public function getPaymentData($token)
    {
        return [
            'additional_data' => [
                PaymentTokenInterface::PUBLIC_HASH => $token->getPublicHash(),
            ],
            'method' => 'pmclain_stripe_vault',
        ];
    }
}
