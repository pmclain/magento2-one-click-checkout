<?php

namespace Pmclain\OneClickCheckout\Model\ResourceModel\DefaultPayment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @codeCoverageIgnore
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(
            \Pmclain\OneClickCheckout\Model\DefaultPayment::class,
            \Pmclain\OneClickCheckout\Model\ResourceModel\DefaultPayment::class
        );
    }
}
