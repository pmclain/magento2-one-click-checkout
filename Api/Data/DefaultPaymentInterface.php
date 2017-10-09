<?php

namespace Pmclain\OneClickCheckout\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface DefaultPaymentInterface
{
    public function getId();

    public function getPaymentTokenId();

    public function getCustomerId();

    public function setId($id);

    public function setPaymentTokenId($paymentTokenId);

    public function setCustomerId($customerId);
}
