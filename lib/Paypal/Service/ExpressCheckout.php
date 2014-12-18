<?php

namespace UniAlteri\Paypal\Express\Service;

use UniAlteri\Paypal\Express\Entity\PurchaseInterface;
use UniAlteri\Paypal\Express\Transport\TransportInterface;

class ExpressCheckout implements ServiceInterface
{
    /**
     * @var TransportInterface
     */
    protected $transport;

    /**
     * @param TransportInterface $transport
     */
    public function __construct($transport)
    {
        $this->transport = $transport;
    }

    /**
     * Prepare a transaction via the Paypal API and get the token to identify
     * the transaction and the consumer on the paypal service
     * @param PurchaseInterface $purchase
     * @return string
     */
    public function generateToken(PurchaseInterface $purchase)
    {
        // TODO: Implement generateToken() method.
    }

    /**
     * Prepare a transaction via the Paypal API and get the url to redirect
     * the user to paypal service to process of the payment
     * @param PurchaseInterface $purchase
     * @return string
     */
    public function prepareTransaction(PurchaseInterface $purchase)
    {
        // TODO: Implement prepareTransaction() method.
    }

    /**
     * Get the transaction result from the Paypal API
     * @param string $token
     * @return TransactionResultInterface
     */
    public function getTransactionResult($token)
    {
        // TODO: Implement getTransactionResult() method.
    }

    /**
     * To confirm an active transaction on the Paypal API and unblock amounts
     * @param string $token
     * @return $this
     */
    public function confirmTransaction($token)
    {
        // TODO: Implement confirmTransaction() method.
    }

    /**
     * To cancel an active transaction on the Paypal API
     * @param string $token
     * @return $this
     */
    public function cancelTransaction($token)
    {
        // TODO: Implement cancelTransaction() method.
    }

}