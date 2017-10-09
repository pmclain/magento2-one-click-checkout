<?php

namespace Pmclain\OneClickCheckout\Controller\OneClick;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Pmclain\OneClickCheckout\Model\QuoteBuilderFactory;
use Pmclain\OneClickCheckout\Model\QuoteBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class PlaceOrder extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Data
     */
    protected $priceHelper;

    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var QuoteBuilderFactory
     */
    protected $quoteBuilderFactory;

    /**
     * PlaceOrder constructor.
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param CartManagementInterface $cartManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param Data $priceHelper
     * @param ScopeConfigInterface $config
     * @param QuoteBuilderFactory $quoteBuilderFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CartManagementInterface $cartManagement,
        OrderRepositoryInterface $orderRepository,
        Data $priceHelper,
        ScopeConfigInterface $config,
        QuoteBuilderFactory $quoteBuilderFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $jsonFactory;
        $this->cartManagement = $cartManagement;
        $this->orderRepository = $orderRepository;
        $this->priceHelper = $priceHelper;
        $this->config = $config;
        $this->quoteBuilderFactory = $quoteBuilderFactory;
    }

    public function execute()
    {
        if (!$this->config->getValue('checkout/one_click_checkout/enabled', ScopeInterface::SCOPE_STORE)) {
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }

        $resultJson = $this->resultJsonFactory->create();

        /** @var QuoteBuilder $quoteBuilder */
        $quoteBuilder = $this->quoteBuilderFactory->create();

        try {
            $quote = $quoteBuilder->createQuote();
        } catch (LocalizedException $e) {
            return $resultJson->setData([
                'status' => 'error',
                'message' => __($e->getMessage()),
            ]);
        } catch (\Exception $e) {
            return $resultJson->setData([
                'status' => 'error',
                'message' => __('An error occurred on the server. Please try again.'),
            ]);
        }

        try {
            $orderId = $this->cartManagement->placeOrder($quote->getId());
            $order = $this->orderRepository->get($orderId);

            $result = [
                'status' => 'success',
                'incrementId' => $order->getIncrementId(),
                'url' => $this->_url->getUrl('sales/order/view', ['order_id' => $orderId]),
                'totals' => [
                    'subtotal' => $this->priceHelper->currency($order->getSubtotal(), true, false),
                    'discount' => [
                        'raw' => $order->getDiscountAmount(),
                        'formatted' => $this->priceHelper->currency($order->getDiscountAmount(), true, false),
                    ],
                    'shipping' => [
                        'raw' => $order->getShippingAmount(),
                        'formatted' => $this->priceHelper->currency($order->getShippingAmount(), true, false),
                    ],
                    'tax' => [
                        'raw' => $order->getTaxAmount(),
                        'formatted' => $this->priceHelper->currency($order->getTaxAmount(), true, false),
                    ],
                    'grandTotal' => $this->priceHelper->currency($order->getGrandTotal(), true, false),
                ],
            ];
        } catch (\Exception $e) {
            $quote->setIsActive(false)->save();
            $result = [
                'status' => 'error',
                'message' => __('An error occurred on the server. Please try again.'),
            ];
        }

        return $resultJson->setData($result);
    }
}
