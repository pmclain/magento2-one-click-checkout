<?php

namespace Pmclain\OneClickCheckout\Api;

interface DefaultPaymentRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Pmclain\OneClickCheckout\Api\Data\DefaultPaymentSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $entityId
     * @return \Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterface
     */
    public function getById($entityId);

    /**
     * @param int $customerId
     * @return \Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterface
     */
    public function getByCustomerId($customerId);

    /**
     * @param int $paymentTokenId
     * @return \Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterface
     */
    public function getByPaymentTokenId($paymentTokenId);

    /**
     * @param \Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterface $defaultPayment
     * @return bool
     */
    public function delete(Data\DefaultPaymentInterface $defaultPayment);

    /**
     * @param \Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterface $defaultPayment
     * @return \Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterface
     */
    public function save(Data\DefaultPaymentInterface $defaultPayment);
}
