<?php

namespace Pmclain\OneClickCheckout\Model;

use Magento\Framework\Exception\InputException;

class VaultPool
{
    /**
     * @var \Pmclain\OneClickCheckout\Api\VaultSourceInterface[]
     */
    protected $vaultSourceMap;

    /**
     * VaultPool constructor.
     * @param array $vaultSourceMap
     */
    public function __construct(
        array $vaultSourceMap = []
    ) {
        $this->vaultSourceMap = $vaultSourceMap;
    }

    /**
     * @param \Magento\Vault\Api\Data\PaymentTokenInterface $token
     *
     * @throws InputException
     * @return array
     */
    public function getPaymentDataArray($token)
    {
        $methodCode = $token->getPaymentMethodCode();

        if (!isset($this->vaultSourceMap[$methodCode])) {
            throw new InputException(__('VaultSource for %1 does not exist.', $methodCode));
        }

        $defaultData = [
            'checks' => [
                \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_CHECKOUT,
                \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY,
                \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY,
                \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
            ],
        ];

        $vaultSourceData = $this->vaultSourceMap[$methodCode]->getPaymentData($token);

        return array_merge($defaultData, $vaultSourceData);
    }
}
