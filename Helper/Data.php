<?php

namespace Gabrielqs\SalesAutomation\Helper;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Checkout\Model\Session as CheckoutSession;
use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;


class Data extends AbstractHelper
{
    /**
     * Store manager interface
     *
     * @var StoreManagerInterface $_storeManager
     */
    protected $_storeManager = null;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig = null;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Returns Sales Method System Config
     *
     * @param string $field
     * @param null $storeId
     * @return array|string
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->_storeManager->getStore(null);
        }
        $path = 'sales/automation/' . $field;
        return $this->_scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getOrderRules() {
        return $this->getConfigData('order_rules');
    }
}