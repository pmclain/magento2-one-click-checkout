<?php

namespace Pmclain\OneClickCheckout\Plugin;

use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Session\SessionManagerInterface;

class Logout
{
    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetaDataFactory;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetaDataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @param \Magento\Customer\Controller\Account\Logout $subject
     * @param \Magento\Framework\Controller\Result\Redirect $result
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function afterExecute(
        \Magento\Customer\Controller\Account\Logout $subject,
        $result
    ) {
        $this->cookieManager->deleteCookie(
            'occ_status',
            $this->cookieMetaDataFactory->createCookieMetadata()
                ->setPath($this->sessionManager->getCookiePath())
                ->setDomain($this->sessionManager->getCookieDomain())
        );

        return $result;
    }
}
