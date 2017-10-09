<?php

namespace Pmclain\OneClickCheckout\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Pmclain\OneClickCheckout\Api\DefaultPaymentRepositoryInterface;
use Magento\Customer\Model\Session;

class TokenRendererInterface
{
    const KEY_IS_DEFAULT = 'is_default';

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var DefaultPaymentRepositoryInterface
     */
    protected $defaultPaymentRepository;

    /**
     * @var string|int
     */
    protected $defaultPaymentId = '';

    /**
     * TokenRendererInterface constructor.
     * @param DefaultPaymentRepositoryInterface $defaultPaymentRepository
     * @param Session $session
     */
    public function __construct(
        DefaultPaymentRepositoryInterface $defaultPaymentRepository,
        Session $session
    ) {
        $this->defaultPaymentRepository = $defaultPaymentRepository;
        $this->session = $session;
        $this->init();
    }

    /**
     * @param \Magento\Vault\Block\TokenRendererInterface $subject
     * @param \Magento\Vault\Api\Data\PaymentTokenInterface|null $result
     * @return \Magento\Vault\Api\Data\PaymentTokenInterface|null $result
     */
    public function afterGetToken(
        \Magento\Vault\Block\TokenRendererInterface $subject,
        $result
    ) {
        if (!$result) {
            return $result;
        }

        $isDefault = false;
        if ($result->getEntityId() === $this->defaultPaymentId) {
            $isDefault = true;
        }

        $result->setData(self::KEY_IS_DEFAULT, $isDefault);

        return $result;
    }

    private function init()
    {
        $customerId = $this->session->getCustomerId();
        try {
            $defaultPayment = $this->defaultPaymentRepository->getByCustomerId($customerId);
            $this->defaultPaymentId = $defaultPayment->getPaymentTokenId();
        } catch (NoSuchEntityException $e) {
            $this->defaultPaymentId = false;
        }
    }
}
