<?php

namespace Pmclain\OneClickCheckout\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Pmclain\OneClickCheckout\Api\DefaultPaymentRepositoryInterface;
use Pmclain\OneClickCheckout\Api\Data\DefaultPaymentInterfaceFactory;
use Psr\Log\LoggerInterface;

class PaymentTokenRepositoryInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DefaultPaymentRepositoryInterface
     */
    protected $defaultPaymentRepository;

    /**
     * @var DefaultPaymentInterfaceFactory
     */
    protected $defaultPaymentFactory;

    /**
     * PaymentTokenRepositoryInterface constructor.
     * @param LoggerInterface $logger
     * @param DefaultPaymentRepositoryInterface $defaultPaymentRepository
     * @param DefaultPaymentInterfaceFactory $defaultPaymentFactory
     */
    public function __construct(
        LoggerInterface $logger,
        DefaultPaymentRepositoryInterface $defaultPaymentRepository,
        DefaultPaymentInterfaceFactory $defaultPaymentFactory
    ) {
        $this->logger = $logger;
        $this->defaultPaymentRepository = $defaultPaymentRepository;
        $this->defaultPaymentFactory = $defaultPaymentFactory;
    }

    /**
     * @param \Magento\Vault\Api\PaymentTokenRepositoryInterface $subject
     * @param \Magento\Vault\Api\Data\PaymentTokenInterface $result
     * @return \Magento\Vault\Api\Data\PaymentTokenInterface
     */
    public function afterSave(
        \Magento\Vault\Api\PaymentTokenRepositoryInterface $subject,
        \Magento\Vault\Api\Data\PaymentTokenInterface $result
    ) {
        $this->setDefaultPayment($result);

        return $result;
    }

    /**
     * @param \Magento\Vault\Api\PaymentTokenRepositoryInterface $subject
     * @param \Magento\Vault\Api\Data\PaymentTokenInterface $token
     * @return array
     */
    public function beforeDelete(
        \Magento\Vault\Api\PaymentTokenRepositoryInterface $subject,
        \Magento\Vault\Api\Data\PaymentTokenInterface $token
    ) {
        $this->deleteDefault($token->getEntityId());

        return [$token];
    }

    /**
     * @param int|string $tokenId
     */
    private function deleteDefault($tokenId)
    {
        try {
            $defaultPayment = $this->defaultPaymentRepository->getByPaymentTokenId($tokenId);
            $this->defaultPaymentRepository->delete($defaultPayment);
        } catch (NoSuchEntityException $e) {
            return;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param \Magento\Vault\Api\Data\PaymentTokenInterface $token
     */
    private function setDefaultPayment($token)
    {
        try {
            $defaultPayment = $this->defaultPaymentRepository->getByCustomerId($token->getCustomerId());
            $defaultPayment->setPaymentTokenId($token->getEntityId());
            $this->defaultPaymentRepository->save($defaultPayment);
        } catch (NoSuchEntityException $e) {
            $defaultPayment = $this->defaultPaymentFactory->create();
            $defaultPayment->setCustomerId($token->getCustomerId());
            $defaultPayment->setPaymentTokenId($token->getEntityId());
            $this->defaultPaymentRepository->save($defaultPayment);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
