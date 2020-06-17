<?php

/*
 * Paypal Express.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\Paypal\Express\Service;

use Teknoo\Paypal\Express\Contract\ConsumerWithCountryInterface;
use Teknoo\Paypal\Express\Contract\PurchaseInterface;
use Teknoo\Paypal\Express\Transport\ArgumentBag;
use Teknoo\Paypal\Express\Transport\TransportInterface;

/**
 * Implementation of ServiceInterface to do transaction with paypal api.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class ExpressCheckout implements ServiceInterface
{
    private TransportInterface $transport;

    private string $paypalUrl;

    private string $jokerInUrlValue;

    public function __construct(TransportInterface $transport, string $paypalUrl, string $jokerInUrlValue = '{token}')
    {
        $this->transport = $transport;
        $this->paypalUrl = $paypalUrl;
        $this->jokerInUrlValue = $jokerInUrlValue;
    }

    private function getValidCurrencyCode(string $currencyCode): string
    {
        switch (\strtoupper($currencyCode)) {
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
                return \strtoupper($currencyCode);
            default:
                throw new \DomainException('Error, the payment action is not valid');
        }
    }

    private function getValidPaymentAction(string $paymentAction): string
    {
        switch (\strtoupper($paymentAction)) {
            case 'SALE':
            case 'AUTHORIZATION':
            case 'ORDER':
                return \strtoupper($paymentAction);
            default:
                throw new \DomainException('Error, the payment action is not valid');
        }
    }

    /**
     * @param array<string, mixed> $result
     */
    private function buildTransactionResultObject(array $result): TransactionResultInterface
    {
        return new TransactionResult($result);
    }

    /**
     * @throws \RuntimeException if the purchase object is invalid
     * @throws \Exception
     */
    public function generateToken(PurchaseInterface $purchase): TransactionResultInterface
    {
        $user = $purchase->getConsumer();

        $requestParams = new ArgumentBag();

        // Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
        $requestParams->set('PAYMENTREQUEST_0_AMT', $purchase->getAmount());

        $requestParams->set(
            'PAYMENTREQUEST_0_PAYMENTACTION',
            $this->getValidPaymentAction($purchase->getPaymentAction())
        ); //Sale, Authorization, Order

        $requestParams->set('RETURNURL', $purchase->getReturnUrl());
        $requestParams->set('CANCELURL', $purchase->getCancelUrl());

        $requestParams->set(
            'PAYMENTREQUEST_0_CURRENCYCODE',
            $this->getValidCurrencyCode($purchase->getCurrencyCode())
        ); //EUR, USD, ...

        $requestParams->set('ADDROVERRIDE', 1);

        $name = $user->getConsumerName();
        if (!empty($name)) {
            $requestParams->set('PAYMENTREQUEST_0_SHIPTONAME', $name);
        }

        $address = $user->getShippingAddress();
        $zip = $user->getShippingZip();
        $city = $user->getShippingCity();

        $state = '';
        $countryCode = '';
        if ($user instanceof ConsumerWithCountryInterface) {
            $state = (string)$user->getShippingState();
            $countryCode = (string)$user->getShippingCountryCode();
        }

        if (!empty($address) && !empty($city) && !empty($zip)) {
            $requestParams->set('PAYMENTREQUEST_0_SHIPTOSTREET', $address);
            $requestParams->set('PAYMENTREQUEST_0_SHIPTOSTREET2', $user->getShippingExtraAddress());
            $requestParams->set('PAYMENTREQUEST_0_SHIPTOZIP', $zip);
            $requestParams->set('PAYMENTREQUEST_0_SHIPTOCITY', $city);
            $requestParams->set('PAYMENTREQUEST_0_SHIPTOSTATE', $state);
            $requestParams->set('PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE', $countryCode);
        }

        $requestParams->set('PAYMENTREQUEST_0_SHIPTOPHONENUM', $user->getPhone());

        $purchase->configureArgumentBag($requestParams);

        $result = $this->buildTransactionResultObject(
            $this->transport->call('SetExpressCheckout', $requestParams)
        );

        if (!$result->isSuccessful()) {
            $errors = $result->getErrors();
            $error = $errors[0];
            throw new \RuntimeException(
                $error->getShortMessage() . ' : ' . $error->getLongMessage(),
                $error->getCode()
            );
        }

        return $result;
    }

    /**
     * Prepare a transaction via the Paypal API and get the url to redirect
     * the user to paypal service to process of the payment.
     */
    public function prepareTransaction(
        PurchaseInterface $purchase
    ): string {
        return \str_replace(
            $this->jokerInUrlValue,
            $this->generateToken($purchase)->getTokenValue(),
            $this->paypalUrl
        );
    }

    /**
     * Get the transaction result from the Paypal API.
     */
    public function getTransactionResult(string $token): TransactionResultInterface
    {
        $arguments = new ArgumentBag();
        $arguments->set('TOKEN', $token);

        return $this->buildTransactionResultObject(
            $this->transport->call('GetExpressCheckoutDetails', $arguments)
        );
    }

    /**
     * To confirm an active transaction on the Paypal API and unblock amounts.
     */
    public function confirmTransaction(
        string $token,
        string $payerId,
        PurchaseInterface $purchase
    ): TransactionResultInterface {
        $arguments = new ArgumentBag();
        $arguments->set('TOKEN', $token);
        $arguments->set('PAYERID', $payerId);
        $arguments->set('PAYMENTREQUEST_0_PAYMENTACTION', $this->getValidPaymentAction($purchase->getPaymentAction()));
        $arguments->set('PAYMENTREQUEST_0_AMT', $purchase->getAmount());
        $arguments->set('PAYMENTREQUEST_0_CURRENCYCODE', $this->getValidCurrencyCode($purchase->getCurrencyCode()));

        return $this->buildTransactionResultObject(
            $this->transport->call('DoExpressCheckoutPayment', $arguments)
        );
    }
}
