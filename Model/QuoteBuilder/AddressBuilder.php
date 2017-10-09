<?php

namespace Pmclain\OneClickCheckout\Model\QuoteBuilder;

use Magento\Customer\Model\Session;
use Magento\Quote\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\AddressRepositoryInterface;

class AddressBuilder
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var AddressInterfaceFactory
     */
    protected $addressFactory;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * AddressBuilder constructor.
     * @param Session $session
     * @param AddressInterfaceFactory $addressFactory
     * @param AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        Session $session,
        AddressInterfaceFactory $addressFactory,
        AddressRepositoryInterface $addressRepository
    ) {
        $this->session = $session;
        $this->addressFactory = $addressFactory;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getShippingAddress()
    {
        /** @var \Magento\Quote\Model\Quote\Address $quoteAddress */
        $quoteAddress = $this->addressFactory->create();

        $customerAddressId = $this->session->getCustomerDataObject()->getDefaultShipping();
        $customerAddress = $this->addressRepository->getById($customerAddressId);

        $quoteAddress->importCustomerAddressData($customerAddress);

        return $quoteAddress;
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getBillingAddress()
    {
        /** @var \Magento\Quote\Model\Quote\Address $quoteAddress */
        $quoteAddress = $this->addressFactory->create();

        $customerAddressId = $this->session->getCustomerDataObject()->getDefaultBilling();
        $customerAddress = $this->addressRepository->getById($customerAddressId);

        $quoteAddress->importCustomerAddressData($customerAddress);

        return $quoteAddress;
    }
}
