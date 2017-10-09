<?php

namespace Pmclain\OneClickCheckout\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterface;

/**
 * @codeCoverageIgnore
 */
class DefaultPayment extends AbstractModel implements IdentityInterface, DefaultPaymentInterface
{
    const CACHE_TAG = 'default_payment';

    const KEY_ID = 'id';
    const KEY_CUSTOMER_ID = 'customer_id';
    const KEY_PAYMENT_TOKEN_ID = 'payment_token_id';

    protected $_cacheTag = self::CACHE_TAG;

    protected function _construct()
    {
        $this->_init(\Pmclain\OneClickCheckout\Model\ResourceModel\DefaultPayment::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId()
    {
        return $this->getData(self::KEY_ID);
    }

    public function getCustomerId()
    {
        return $this->getData(self::KEY_CUSTOMER_ID);
    }

    public function getPaymentTokenId()
    {
        return $this->getData(self::KEY_PAYMENT_TOKEN_ID);
    }

    public function setId($id)
    {
        return $this->setData(self::KEY_ID, $id);
    }

    public function setCustomerId($customerId)
    {
        return $this->setData(self::KEY_CUSTOMER_ID, $customerId);
    }

    public function setPaymentTokenId($paymentTokenId)
    {
        return $this->setData(self::KEY_PAYMENT_TOKEN_ID, $paymentTokenId);
    }
}
