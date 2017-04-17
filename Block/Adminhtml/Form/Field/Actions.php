<?php

namespace Gabrielqs\SalesAutomation\Block\Adminhtml\Form\Field;

use \Magento\Framework\View\Element\Html\Select;

/**
 * Actions select box renderer
 *
 * Class InstallmentsWithNoInterest
 * @package Gabrielqs\SalesAutomation\Block\Adminhtml\Form\Field
 */
class Actions extends Select
{
    /**
     * Returns installment options for parent block to render
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $options = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
            foreach ($options as $option) {
                $this->addOption($option, $option);
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