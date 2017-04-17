<?php

namespace Gabrielqs\SalesAutomation\Model;

use \Magento\Sales\Model\Service\InvoiceService;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use \Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use \Magento\Framework\DB\Transaction;
use \Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use \Magento\Sales\Model\Order\Invoice;
use \Gabrielqs\SalesAutomation\Helper\Data as SalesAutomationHelper;

class OrderProcessor
{
    /**
     * Constant for cancel action
     */
    const ACTION_CANCEL = 'cancel';

    /**
     * Constant for invoice action
     */
    const ACTION_INVOICE = 'invoice';

    /**
     * DB Transaction
     * @var Transaction
     */
    protected $_dbTransaction = null;

    /**
     * Invoice Sender
     * @var InvoiceSender
     */
    protected $_invoiceSender = null;

    /**
     * Invoice Service
     * @var InvoiceService
     */
    protected $_invoiceService = null;

    /**
     * Order Collection Factory
     * @var OrderCollectionFactory
     */
    protected $_orderCollectionFactory = null;

    /**
     * Sales Automation Helper
     * @var SalesAutomationHelper
     */
    protected $_salesAutomationHelper = null;

    /**
     * Order Auto Manage Plugin constructor
     * @param Transaction $dbTransaction
     * @param InvoiceService $invoiceService
     * @param InvoiceSender $invoiceSender
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param SalesAutomationHelper $salesAutomationHelper
     */
    public function __construct (
        Transaction $dbTransaction,
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        OrderCollectionFactory $orderCollectionFactory,
        SalesAutomationHelper $salesAutomationHelper
    ) {
        $this->_dbTransaction = $dbTransaction;
        $this->_invoiceService = $invoiceService;
        $this->_invoiceSender = $invoiceSender;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_salesAutomationHelper = $salesAutomationHelper;
    }

    /**
     * Cancels all orders from an collection
     * @param OrderCollection $orderCollection
     * @return void
     */
    protected function _cancelCollection(OrderCollection $orderCollection)
    {
        foreach ($orderCollection as $order) {
            if ($order->canCancel()) {
                $order
                    ->cancel()
                    ->save();
            }
        }
    }

    /**
     * Auto manages order after place. Creates invoice or cancel.
     * @return void
     */
    public function processRules()
    {
        foreach (unserialize($this->_salesAutomationHelper->getOrderRules()) as $orderRule) {
            /** @var OrderCollection $orderCollection */
            $orderCollection = $this->_orderCollectionFactory->create();
            $orderCollection
                ->join(
                    ['payment' =>  $orderCollection->getResource()->getTable('sales_order_payment')],
                    'payment.parent_id = main_table.entity_id',
                    ['*']
                )
                ->addFieldToFilter('status', ['eq' => $orderRule['status']])
                ->addFieldToFilter('method', ['eq' => $orderRule['payment_method']]);

            if ($orderCollection->count()) {
                switch ($orderRule['execute']) {
                    case self::ACTION_INVOICE:
                        $this->_invoiceCollection($orderCollection);
                        break;
                    case self::ACTION_CANCEL:
                        $this->_cancelCollection($orderCollection);
                        break;
                    default;
                        break;
                }
            }
        }
    }

    /**
     * Creates invoices for all orders in a collection
     * @param OrderCollection $orderCollection
     * @return void
     */
    protected function _invoiceCollection(OrderCollection $orderCollection)
    {
        foreach ($orderCollection as $order) {
            if ($order->canInvoice()) {
                /* @var Invoice $invoice */
                $invoice = $this->_invoiceService->prepareInvoice($order);

                $invoice
                    ->register()
                    ->capture()
                    ->save();

                /*$this->_dbTransaction
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();*/

                $this->_invoiceSender->send($invoice);

                $message = __('Notified customer about invoice #%1.', $invoice->getId());
                $order
                    ->addStatusHistoryComment($message)
                    ->setIsCustomerNotified(true)
                    ->save();
            }
        }
    }
}