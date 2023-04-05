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
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Paypal\Express\Service;

use DomainException;
use Teknoo\Paypal\Express\Contracts\ConsumerWithCountryInterface;
use Teknoo\Paypal\Express\Contracts\PurchaseInterface;
use Teknoo\Paypal\Express\Transport\ArgumentBag;
use Teknoo\Paypal\Express\Transport\Exception\ErrorInRequestException;
use Teknoo\Paypal\Express\Transport\TransportInterface;

use function str_replace;
use function strtoupper;

/**
 * Implementation of ServiceInterface to do transaction with paypal api.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class ExpressCheckout implements ServiceInterface
{
    public function __construct(
        private readonly TransportInterface $transport,
        private readonly string $paypalUrl,
        private readonly string $jokerInUrlValue = '{token}',
        private readonly string $defaultCountry = 'FR'
    ) {
    }

    private function getValidCurrencyCode(string $currencyCode): string
    {
        return match (strtoupper($currencyCode)) {
            'AUD',
            'BRL',
            'CAD',
            'CZK',
            'DKK',
            'EUR',
            'HKD',
            'HUF',
            'ILS',
            'JPY',
            'MYR',
            'MXN',
            'NOK',
            'NZD',
            'PHP',
            'PLN',
            'GBP',
            'RUB',
            'SGD',
            'SEK',
            'CHF',
            'TWD',
            'THB',
            'TRY',
            'USD' => strtoupper($currencyCode),
            default => throw new DomainException('Error, the payment action is not valid'),
        };
    }

    private function getValidPaymentAction(string $paymentAction): string
    {
        return match (strtoupper($paymentAction)) {
            'SALE', 'AUTHORIZATION', 'ORDER' => strtoupper($paymentAction),
            default => throw new DomainException('Error, the payment action is not valid'),
        };
    }

    /**
     * @param array<string, string> $result
     */
    private function buildTransactionResultObject(array $result): TransactionResultInterface
    {
        return new TransactionResult($result);
    }

    /**
     * @throws ErrorInRequestException if the purchase object is invalid
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
        $countryCode = $this->defaultCountry;
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
            throw new ErrorInRequestException(
                $error->getShortMessage() . ' : ' . $error->getLongMessage(),
                $error->getCode()
            );
        }

        return $result;
    }

    /*
     * Prepare a transaction via the Paypal API and get the url to redirect
     * the user to paypal service to process of the payment.
     */
    public function prepareTransaction(
        PurchaseInterface $purchase
    ): string {
        return str_replace(
            $this->jokerInUrlValue,
            $this->generateToken($purchase)->getTokenValue(),
            $this->paypalUrl
        );
    }

    /*
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

    /*
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
