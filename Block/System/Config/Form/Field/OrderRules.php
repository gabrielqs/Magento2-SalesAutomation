<?php

namespace Gabrielqs\SalesAutomation\Block\System\Config\Form\Field;

use \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use \Magento\Framework\DataObject;
use \Gabrielqs\SalesAutomation\Block\Adminhtml\Form\Field\PaymentMethods as PaymentMethodsRenderer;
use \Gabrielqs\SalesAutomation\Block\Adminhtml\Form\Field\Status as StatusRenderer;

/**
 * Responsible for creating a form field for the serialized arry option OrderRules
 *
 * Class InstallmentsWithNoInterest
 * @package Gabrielqs\SalesAutomation\Block\System\Config\Form\Field
 */
class OrderRules extends AbstractFieldArray
{

    /**
     * Renderer for the Payment Methods selectbox
     *
     * @var PaymentMethodsRenderer $_paymentMethodsRenderer
     */
    protected $_paymentMethodsRenderer = null;

    /**
     * Renderer for the Status selectbox
     *
     * @var StatusRenderer $_statusRenderer
     */
    protected $_statusRenderer = null;

    /**
     * Returns the Payment Methods selectbox renderer
     * @return PaymentMethodsRenderer
     */
    protected function _getPaymentMethodsRenderer()
    {
        if (!$this->_paymentMethodsRenderer) {
            $this->_paymentMethodsRenderer = $this->getLayout()->createBlock(
                'Gabrielqs\SalesAutomation\Block\Adminhtml\Form\Field\PaymentMethods',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_paymentMethodsRenderer;
    }

    /**
     * Returns the Payment Methods selectbox renderer
     * @return StatusRenderer
     */
    protected function _getStatusRenderer()
    {
        if (!$this->_statusRenderer) {
            $this->_statusRenderer = $this->getLayout()->createBlock(
                'Gabrielqs\SalesAutomation\Block\Adminhtml\Form\Field\Status',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_statusRenderer;
    }

    /**
     * Prepares line for rendering
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'payment_method',
            [
                'label' => __('Payment Method'),
                'class' => 'validate-no-empty',
                'renderer' => $this->_getPaymentMethodsRenderer(),
            ]
        );
        $this->addColumn(
            'status',
            [
                'label' => __('Status'),
                'class' => 'validate-no-empty',
                'renderer' => $this->_getStatusRenderer(),
            ]
        );
        $this->addColumn(
            'execute',
            [
                'label' => __('Action'),
                'class' => 'validate-no-empty',
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare serialized array row to be shown
     *
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow( DataObject $row )
    {
        $optionExtraAttr = [];

        $optionExtraAttr[
            'option_' . $this->_getPaymentMethodsRenderer()->calcOptionHash($row->getData('payment_method'))
        ] = 'selected="selected"';

        $optionExtraAttr[
            'option_' . $this->_getStatusRenderer()->calcOptionHash($row->getData('status'))
        ] = 'selected="selected"';

        $row->setData('option_extra_attrs', $optionExtraAttr);
    }
}