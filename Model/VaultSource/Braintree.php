<?php

namespace Pmclain\OneClickCheckout\Model\VaultSource;

use Pmclain\OneClickCheckout\Api\VaultSourceInterface;
use Magento\Braintree\Model\Ui\ConfigProvider;
use Magento\Braintree\Model\Adapter\BraintreeAdapter;
use Magento\Braintree\Observer\DataAssignObserver;
use Magento\Vault\Api\Data\PaymentTokenInterface;

class Braintree implements VaultSourceInterface
{
    /**
     * @var BraintreeAdapter
     */
    protected $adapter;

    /**
     * Braintree constructor.
     * @param BraintreeAdapter $adapter
     */
    public function __construct(
        BraintreeAdapter $adapter
    ) {
        $this->adapter = $adapter;
    }

    /**
     * @param PaymentTokenInterface $token
     * @return array
     */
    public function getPaymentData($token)
    {
        $nonceObject = $this->adapter->createNonce($token->getGatewayToken());
        $nonce = $nonceObject->paymentMethodNonce->nonce;

        return [
            'additional_data' => [
                DataAssignObserver::PAYMENT_METHOD_NONCE => $nonce,
                PaymentTokenInterface::PUBLIC_HASH => $token->getPublicHash(),
            ],
            'method' => ConfigProvider::CC_VAULT_CODE,
        ];
    }
}
