<?php

namespace Pmclain\OneClickCheckout\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @codeCoverageIgnore
 */
class DefaultPayment extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('default_payment', 'id');
    }
}
