<?php

namespace Gabrielqs\SalesAutomation\Block\Adminhtml\Form\Field;

use \Magento\Framework\View\Element\Html\Select;
use \Magento\Framework\View\Element\Context;
use \Magento\Payment\Helper\Data as PaymentHelper;

/**
 * PaymentMethods select box renderer
 *
 * Class PaymentMethods
 * @package Gabrielqs\SalesAutomation\Block\Adminhtml\Form\Field
 */
class PaymentMethods extends Select
{
    /**
     * Payment Helper
     * @var PaymentHelper
     */
    protected $_paymentHelper = null;

    /**
     * Constructor
     * @param Context $context
     * @param PaymentHelper $_paymentHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        PaymentHelper $_paymentHelper,
        array $data = []
    ) {
        $this->_paymentHelper = $_paymentHelper;
        parent::__construct($context, $data);
    }

    /**
     * Returns installment options for parent block to render
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $options = $this->_paymentHelper->getPaymentMethodList();
            foreach ($options as $code => $title) {
                $this->addOption($code, $title . ' (' . $code . ')');
            }
        }
        return parent::_toHtml();
    }

    /**
     * Sets input name
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}