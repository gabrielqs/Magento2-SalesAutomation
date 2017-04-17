<?php

namespace Gabrielqs\SalesAutomation\Block\Adminhtml\Form\Field;

use \Magento\Framework\View\Element\Html\Select;
use \Magento\Framework\View\Element\Context;
use \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as OrderStatusCollectionFactory;

/**
 * Status select box renderer
 *
 * Class Status
 * @method Status setName(string $value)
 * @package Gabrielqs\SalesAutomation\Block\Adminhtml\Form\Field
 */
class Status extends Select
{
    /**
     * @var OrderStatusCollectionFactory
     */
    protected $_orderStatusCollectionFactory = null;

    /**
     * Constructor
     * @param Context $context
     * @param OrderStatusCollectionFactory $_orderStatusCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        OrderStatusCollectionFactory $_orderStatusCollectionFactory,
        array $data = []
    ) {
        $this->_orderStatusCollectionFactory = $_orderStatusCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Returns installment options for parent block to render
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $collection = $this->_orderStatusCollectionFactory->create();
            foreach ($collection as $status) {
                $this->addOption($status->getStatus(), $status->getLabel() . ' (' . $status->getStatus() . ')');
            }
        }
        return parent::_toHtml();
    }

    /**
     * Sets input name
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}