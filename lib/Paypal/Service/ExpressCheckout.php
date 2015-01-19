<?php

namespace UniAlteri\Paypal\Express\Service;

use UniAlteri\Paypal\Express\Entity\PurchaseInterface;
use UniAlteri\Paypal\Express\Transport\ArgumentBag;
use UniAlteri\Paypal\Express\Transport\TransportInterface;

/**
 * Class ExpressCheckout
 * Implementation of ServiceInterface to do transaction with paypal api
 * @package UniAlteri\Paypal\Express\Service
 */
class ExpressCheckout implements ServiceInterface
{
    /**
     * @var TransportInterface
     */
    protected $transport;

    /**
     * To initialize the service
     * @param TransportInterface $transport
     */
    public function __construct($transport)
    {
        $this->transport = $transport;
    }

    /**
     * Check the value of the currency attempted by the paypal api
     * @param string $currencyCode
     * @return string
     */
    protected function getValidCurrencyCode($currencyCode)
    {
        switch (strtoupper($currencyCode)) {
            case 'AUD':
            case 'BRL':
            case 'CAD':
            case 'CZK':
            case 'DKK':
            case 'EUR':
            case 'HKD':
            case 'HUF':
            case 'ILS':
            case 'JPY':
            case 'MYR':
            case 'MXN':
            case 'NOK':
            case 'NZD':
            case 'PHP':
            case 'PLN':
            case 'GBP':
            case 'RUB':
            case 'SGD':
            case 'SEK':
            case 'CHF':
            case 'TWD':
            case 'THB':
            case 'TRY':
            case 'USD':
                return $currencyCode;
                break;
            default:
                throw new \DomainException('Error, the payment action is not valid');
                break;
        }
    }

    /**
     * Check the value of the payment action attempted by the paypal api
     * @param string $paymentAction
     * @return string
     */
    protected function getValidPaymentAction($paymentAction)
    {
        switch (strtoupper($paymentAction)) {
            case 'SALE':
            case 'AUTHORIZATION':
            case 'ORDER':
                return $paymentAction;
                break;
            default:
                throw new \DomainException('Error, the payment action is not valid');
                break;
        }
    }

    /**
     * @param array|\ArrayAccess $result
     * @return TransactionResultInterface
     */
    protected function buildTransactionResultObjectt($result)
    {
        return new TransactionResult($result);
    }

    /**
     * Prepare a transaction via the Paypal API and get the token to identify
     * the transaction and the consumer on the paypal service
     * @param PurchaseInterface $purchase
     * @return string
     */
    public function generateToken(PurchaseInterface $purchase)
    {
        $user = $purchase->getConsumer();

        $requestParams = new ArgumentBag();

        // Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
        $requestParams->set('PAYMENTREQUEST_0_AMT', $purchase->getAmount());
        $requestParams->set('PAYMENTREQUEST_0_PAYMENTACTION', $this->getValidPaymentAction($purchase->getPaymentAction())); //Sale, Authorization, Order
        $requestParams->set('RETURNURL', $purchase->getReturnUrl());
        $requestParams->set('CANCELURL', $purchase->getCancelUrl());
        $requestParams->set('PAYMENTREQUEST_0_CURRENCYCODE', $this->getValidCurrencyCode($purchase->getCurrencyCode())); //EUR, USD, ...
        $requestParams->set('ADDROVERRIDE', 1);

        $name = $user->getConsumerName();
        if (!empty($name)) {
            $requestParams->set('PAYMENTREQUEST_0_SHIPTONAME', $name);
        }

        $address = $user->getShippingAddress();
        $zip = $user->getShippingZip();
        $city = $user->getShippingCity();
        if (!empty($address) && !empty($city) && !empty($zip)) {
            $requestParams->set('PAYMENTREQUEST_0_SHIPTOSTREET', $address);
            $requestParams->set('PAYMENTREQUEST_0_SHIPTOSTREET2', $user->getShippingExtraAddress());
            $requestParams->set('PAYMENTREQUEST_0_SHIPTOZIP', $zip);
            $requestParams->set('PAYMENTREQUEST_0_SHIPTOCITY', $city);
            $requestParams->set('PAYMENTREQUEST_0_SHIPTOSTATE', '');
            $requestParams->set('PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE', 'FR');
        }

        $requestParams->set('PAYMENTREQUEST_0_SHIPTOPHONENUM', $user->getPhone());

        return $this->buildTransactionResultObjectt($this->transport->call('SetExpressCheckout', $requestParams));
    }

    /**
     * Prepare a transaction via the Paypal API and get the url to redirect
     * the user to paypal service to process of the payment
     * @param PurchaseInterface $purchase
     * @return string
     */
    public function prepareTransaction(PurchaseInterface $purchase)
    {
        return str_replace('{token}', $this->generateToken($purchase), $this->transport->getPaypalUrl());
    }

    /**
     * Get the transaction result from the Paypal API
     * @param string $token
     * @return TransactionResultInterface
     */
    public function getTransactionResult($token)
    {
        $arguments = new ArgumentBag();
        $arguments->set('TOKEN', $token);
        return $this->buildTransactionResultObjectt($this->transport->call('GetExpressCheckoutDetails', $arguments));
    }

    /**
     * To confirm an active transaction on the Paypal API and unblock amounts
     * @param string $token
     * @param string $payerId
     * @param PurchaseInterface $purchase
     * @return $this
     */
    public function confirmTransaction($token, $payerId, PurchaseInterface $purchase)
    {
        $arguments = new ArgumentBag();
        $arguments->set('TOKEN', $token);
        $arguments->set('PAYERID', $payerId);
        $arguments->set('PAYMENTREQUEST_0_PAYMENTACTION', $this->getValidPaymentAction($purchase->getPaymentAction()));
        $arguments->set('PAYMENTREQUEST_0_AMT', $purchase->getAmount());
        $arguments->set('PAYMENTREQUEST_0_CURRENCYCODE', $this->getValidCurrencyCode($purchase->getCurrencyCode()));

        return $this->buildTransactionResultObjectt($this->transport->call('DoExpressCheckoutPayment', $arguments));
    }

    /**
     * To cancel an active transaction on the Paypal API
     * @param string $token
     * @return $this
     */
    public function cancelTransaction($token)
    {
    }

}