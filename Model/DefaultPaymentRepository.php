<?php

namespace Pmclain\OneClickCheckout\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Pmclain\OneClickCheckout\Api\Data\DefaultPaymentSearchResultsInterfaceFactory;
use Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterface;
use Pmclain\OneClickCheckout\Api\DefaultPaymentRepositoryInterface;
use Pmclain\OneClickCheckout\Model\ResourceModel\DefaultPayment as DefaultPaymentResource;
use Pmclain\OneClickCheckout\Model\ResourceModel\DefaultPayment\CollectionFactory;

class DefaultPaymentRepository implements DefaultPaymentRepositoryInterface
{
    /**
     * @var DefaultPaymentResource
     */
    protected $resource;

    /**
     * @var DefaultPaymentFactory
     */
    protected $factory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var DefaultPaymentSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * DefaultPaymentRepository constructor.
     * @param DefaultPaymentResource $resource
     * @param DefaultPaymentFactory $factory
     * @param CollectionFactory $collectionFactory
     * @param DefaultPaymentSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        DefaultPaymentResource $resource,
        DefaultPaymentFactory $factory,
        CollectionFactory $collectionFactory,
        DefaultPaymentSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->factory = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @param DefaultPaymentInterface $defaultPayment
     * @return $this
     */
    public function save(DefaultPaymentInterface $defaultPayment)
    {
        $this->resource->save($defaultPayment);
        return $this;
    }

    /**
     * @param int $entityId
     * @return DefaultPayment
     * @throws NoSuchEntityException
     */
    public function getById($entityId)
    {
        $defaultPayment = $this->factory->create();
        $this->resource->load($defaultPayment, $entityId);
        if (!$defaultPayment->getId()) {
            throw new NoSuchEntityException(__('Default Payment method does not exist.'));
        }

        return $defaultPayment;
    }

    /**
     * @param int $customerId
     * @return DefaultPayment
     * @throws NoSuchEntityException
     */
    public function getByCustomerId($customerId)
    {
        $defaultPayment = $this->factory->create();
        $this->resource->load($defaultPayment, $customerId, 'customer_id');
        if (!$defaultPayment->getId()) {
            throw new NoSuchEntityException(__('Default Payment method does not exist.'));
        }

        return $defaultPayment;
    }

    /**
     * @param int $paymentTokenId
     * @return DefaultPayment
     * @throws NoSuchEntityException
     */
    public function getByPaymentTokenId($paymentTokenId)
    {
        $defaultPayment = $this->factory->create();
        $this->resource->load($defaultPayment, $paymentTokenId, 'payment_token_id');
        if (!$defaultPayment->getId()) {
            throw new NoSuchEntityException(__('Default Payment method does not exist.'));
        }

        return $defaultPayment;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Pmclain\OneClickCheckout\Api\Data\DefaultPaymentSearchResultsInterface
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $collection = $this->collectionFactory->create();

        foreach ((array)$searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }

        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                $this->getDirection($sortOrder->getDirection())
            );
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setCriteria($searchCriteria);

        $defaultPayments = [];
        foreach ($collection as $item) {
            $defaultPayments[] = $item;
        }

        $searchResults->setItems($defaultPayments);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @param DefaultPaymentInterface $defaultPayment
     * @return bool
     */
    public function delete(DefaultPaymentInterface $defaultPayment)
    {
        if ($this->resource->delete($defaultPayment)) {
            return true;
        }
        return false;
    }

    protected function addFilterGroupToCollection($group, $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($group->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $field = $filter->getField();
            $value = $filter->getValue();
            $fields[] = $field;
            $conditions[] = [$condition => $value];
        }
        $collection->addFieldToFilter($fields, $conditions);
    }
}
