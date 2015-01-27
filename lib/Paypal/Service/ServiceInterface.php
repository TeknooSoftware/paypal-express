<?php

namespace UniAlteri\Paypal\Express\Service;

use UniAlteri\Paypal\Express\Entity\PurchaseInterface;

/**
 * Interface ServiceInterface
 * Interface to define available service to use paypal express checkout in php platform
 * @package UniAlteri\Paypal\Express\Service
 */
interface ServiceInterface
{
    /**
     * Prepare a transaction via the Paypal API and get the token to identify
     * the transaction and the consumer on the paypal service
     * @param PurchaseInterface $purchase
     * @return TransactionResultInterface
     * @throws \RuntimeException if the purchase object is invalid
     * @throws \Exception
     */
    public function generateToken(PurchaseInterface $purchase);

    /**
     * Prepare a transaction via the Paypal API and get the url to redirect
     * the user to paypal service to process of the payment
     * @param PurchaseInterface $purchase
     * @return string
     */
    public function prepareTransaction(PurchaseInterface $purchase);

    /**
     * Get the transaction result from the Paypal API
     * @param string $token
     * @return TransactionResultInterface
     */
    public function getTransactionResult($token);

    /**
     * To confirm an active transaction on the Paypal API and unblock amounts
     * @param string $token
     * @param string $payerId
     * @param PurchaseInterface $purchase
     * @return TransactionResultInterface
     */
    public function confirmTransaction($token, $payerId, PurchaseInterface $purchase);
}