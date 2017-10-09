<?php

namespace Pmclain\OneClickCheckout\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface DefaultPaymentSearchResultsInterface extends SearchResultsInterface
{
    public function getItems();

    public function setItems(array $items);
}
