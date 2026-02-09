<?php

/*
 * Paypal Express.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 *
 * @author      Richard Déloge <richard@teknoo.software>
 *
 *
 */

declare(strict_types=1);

namespace Teknoo\Tests\Paypal\Service;

use DomainException;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Teknoo\Paypal\Express\Contracts\ConsumerInterface;
use Teknoo\Paypal\Express\Contracts\ConsumerWithCountryInterface;
use Teknoo\Paypal\Express\Contracts\PurchaseInterface;
use Teknoo\Paypal\Express\Service\ExpressCheckout;
use Teknoo\Paypal\Express\Service\TransactionResultInterface;
use Teknoo\Paypal\Express\Transport\ArgumentBag;
use Teknoo\Paypal\Express\Transport\TransportInterface;

use function is_object;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 *
 * @author      Richard Déloge <richard@teknoo.software>
 *
 */
#[CoversClass(ExpressCheckout::class)]
class ExpressCheckoutTest extends TestCase
{
    private ?MockObject $transport = null;

    private ?Stub $consumer = null;

    private ?Stub $purchase = null;

    /**
     * @return MockObject|TransportInterface
     */
    private function getTransportMock(): TransportInterface
    {
        if (!is_object($this->transport)) {
            $this->transport = $this->createMock(TransportInterface::class);
        }

        return $this->transport;
    }

    private function getConsumerStub(): ConsumerInterface&Stub
    {
        if (!is_object($this->consumer)) {
            $this->consumer = $this->createStub(ConsumerInterface::class);
        }

        return $this->consumer;
    }

    private function getConsumerWithCountryStub(): ConsumerWithCountryInterface&Stub
    {
        if (!is_object($this->consumer)) {
            $this->consumer = $this->createStub(ConsumerWithCountryInterface::class);
        }

        return $this->consumer;
    }

    /**
     * @return PurchaseInterface
     */
    private function getPurchaseMock(): PurchaseInterface
    {
        if (!is_object($this->purchase)) {
            // Use a stub to avoid PHPUnit notice when no explicit expectations are set
            $this->purchase = $this->createStub(PurchaseInterface::class);
        }

        return $this->purchase;
    }

    public function buildService(): ExpressCheckout
    {
        return new ExpressCheckout(
            $this->getTransportMock(),
            'http://paypalUrl/{token}',
            '{token}'
        );
    }

    private function setIdentityToConsumer(bool $withCountry = false): ConsumerInterface
    {
        if (true === $withCountry) {
            $consumer = $this->getConsumerWithCountryStub();
        } else {
            $consumer = $this->getConsumerStub();
        }

        $this->getPurchaseMock()
            ->method('getConsumer')
            ->willReturn($consumer);

        $consumer
            ->method('getConsumerName')
            ->willReturn('Roger Rabbit');

        $consumer
            ->method('getPhone')
            ->willReturn('789456123');

        return $consumer;
    }

    private function setAddressToConsumer(bool $withCountry = false): ConsumerInterface
    {
        $consumer = $this->setIdentityToConsumer($withCountry);

        $consumer
            ->method('getShippingAddress')
            ->willReturn('adr1');

        $consumer
            ->method('getShippingZip')
            ->willReturn('14000');

        $consumer
            ->method('getShippingCity')
            ->willReturn('Caen');

        if (true === $withCountry) {
            $consumer
                ->method('getShippingCountryCode')
                ->willReturn('US');

            $consumer
                ->method('getShippingState')
                ->willReturn('Washington');
        }

        return $consumer;
    }

    /**
     * @return MockObject|PurchaseInterface
     */
    private function setPurchase(string $currency = 'EUR', string $operation = 'SALE'): PurchaseInterface
    {
        $purchase = $this->getPurchaseMock();

        $purchase
            ->method('getAmount')
            ->willReturn(150.12);

        $purchase
            ->method('getPaymentAction')
            ->willReturn($operation);

        $purchase
            ->method('getReturnUrl')
            ->willReturn('http://teknoo.software');

        $purchase
            ->method('getCancelUrl')
            ->willReturn('http://teknoo.software/cancel');

        $purchase
            ->method('getCurrencyCode')
            ->willReturn($currency);

        return $purchase;
    }

    public function testConstruct(): void
    {
        // Ensure the transport mock has an explicit expectation to avoid PHPUnit notice
        $this->getTransportMock()
            ->expects($this->never())
            ->method('call');
        $this->assertInstanceOf(ExpressCheckout::class, $this->buildService());
    }

    public function testGenerateTokenWithoutAddress(): void
    {
        $exceptedBody = [
            'PAYMENTREQUEST_0_AMT' => 150.12,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE', //SALE, Authorization, Order
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
            'ADDROVERRIDE' => 1,
            'PAYMENTREQUEST_0_SHIPTONAME' => 'Roger Rabbit',
            'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
            'RETURNURL' => 'http://teknoo.software',
            'CANCELURL' => 'http://teknoo.software/cancel',
        ];

        $this->getTransportMock()
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody): array {
                    $this->assertEquals('SetExpressCheckout', $name);
                    $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                    return [
                        'ACK' => 'SUCCESS',
                        'TOKEN' => 'tokenFake',
                    ];
                }
            );

        $this->setIdentityToConsumer();

        $purchase = $this->setPurchase();
        $purchase
            ->method('configureArgumentBag')
            ->willReturnSelf();

        $result = $this->buildService()
            ->generateToken($purchase);

        $this->assertInstanceOf(TransactionResultInterface::class, $result);
        $this->assertTrue($result->isSuccessful());
        $this->assertEquals('tokenFake', $result->getTokenValue());
    }

    public function testGenerateTokenAddress(): void
    {
        $exceptedBody = [
            'PAYMENTREQUEST_0_AMT' => 150.12,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE', //SALE, Authorization, Order
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
            'ADDROVERRIDE' => 1,
            'PAYMENTREQUEST_0_SHIPTONAME' => 'Roger Rabbit',
            'PAYMENTREQUEST_0_SHIPTOSTREET' => 'adr1',
            'PAYMENTREQUEST_0_SHIPTOZIP' => 14000,
            'PAYMENTREQUEST_0_SHIPTOCITY' => 'Caen',
            'PAYMENTREQUEST_0_SHIPTOSTATE' => '',
            'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'FR',
            'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
            'RETURNURL' => 'http://teknoo.software',
            'CANCELURL' => 'http://teknoo.software/cancel',
            'PAYMENTREQUEST_0_SHIPTOSTREET2' => null,
        ];

        $this->getTransportMock()
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody): array {
                    $this->assertEquals('SetExpressCheckout', $name);
                    $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                    return [
                        'ACK' => 'SUCCESS',
                        'TOKEN' => 'tokenFake',
                    ];
                }
            );

        $this->setAddressToConsumer();

        $purchase = $this->setPurchase();
        $purchase
            ->method('configureArgumentBag')
            ->willReturnSelf();

        $result = $this->buildService()
            ->generateToken($purchase);

        $this->assertInstanceOf(TransactionResultInterface::class, $result);
        $this->assertTrue($result->isSuccessful());
        $this->assertEquals('tokenFake', $result->getTokenValue());
    }

    public function testGenerateTokenAddressWithCountry(): void
    {
        $exceptedBody = [
            'PAYMENTREQUEST_0_AMT' => 150.12,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE', //SALE, Authorization, Order
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
            'ADDROVERRIDE' => 1,
            'PAYMENTREQUEST_0_SHIPTONAME' => 'Roger Rabbit',
            'PAYMENTREQUEST_0_SHIPTOSTREET' => 'adr1',
            'PAYMENTREQUEST_0_SHIPTOZIP' => 14000,
            'PAYMENTREQUEST_0_SHIPTOCITY' => 'Caen',
            'PAYMENTREQUEST_0_SHIPTOSTATE' => 'Washington',
            'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'US',
            'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
            'RETURNURL' => 'http://teknoo.software',
            'CANCELURL' => 'http://teknoo.software/cancel',
            'PAYMENTREQUEST_0_SHIPTOSTREET2' => null,
        ];

        $this->getTransportMock()
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody): array {
                    $this->assertEquals('SetExpressCheckout', $name);
                    $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                    return [
                        'ACK' => 'SUCCESS',
                        'TOKEN' => 'tokenFake',
                    ];
                }
            );

        $this->setAddressToConsumer(true);

        $purchase = $this->setPurchase();
        $purchase
            ->method('configureArgumentBag')
            ->willReturnSelf();

        $result = $this->buildService()
            ->generateToken($purchase);

        $this->assertInstanceOf(TransactionResultInterface::class, $result);
        $this->assertTrue($result->isSuccessful());
        $this->assertEquals('tokenFake', $result->getTokenValue());
    }

    public function testGenerateTokenAddressCurrency(): void
    {
        $currencies = ['AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK',
            'NZD', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'TWD', 'THB', 'TRY', 'USD'];

        foreach ($currencies as $currency) {
            $this->purchase = null;
            $this->consumer = null;
            $this->transport = null;
            $exceptedBody = [
                'PAYMENTREQUEST_0_AMT' => 150.12,
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE', //SALE, Authorization, Order
                'PAYMENTREQUEST_0_CURRENCYCODE' => $currency,
                'ADDROVERRIDE' => 1,
                'PAYMENTREQUEST_0_SHIPTONAME' => 'Roger Rabbit',
                'PAYMENTREQUEST_0_SHIPTOSTREET' => 'adr1',
                'PAYMENTREQUEST_0_SHIPTOZIP' => 14000,
                'PAYMENTREQUEST_0_SHIPTOCITY' => 'Caen',
                'PAYMENTREQUEST_0_SHIPTOSTATE' => '',
                'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'FR',
                'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
                'RETURNURL' => 'http://teknoo.software',
                'CANCELURL' => 'http://teknoo.software/cancel',
                'PAYMENTREQUEST_0_SHIPTOSTREET2' => null,
            ];

            $this->getTransportMock()
                ->expects($this->once())
                ->method('call')
                ->willReturnCallback(
                    function ($name, $args) use (&$exceptedBody): array {
                        $this->assertEquals('SetExpressCheckout', $name);
                        $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                        return [
                            'ACK' => 'SUCCESS',
                            'TOKEN' => 'tokenFake',
                        ];
                    }
                );

            $this->setAddressToConsumer();

            $purchase = $this->setPurchase($currency);
            $purchase
                ->method('configureArgumentBag')
                ->willReturnSelf();

            $result = $this->buildService()
                ->generateToken($purchase);

            $this->assertInstanceOf(TransactionResultInterface::class, $result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEquals('tokenFake', $result->getTokenValue());
        }
    }

    public function testGenerateTokenAddressBadCurrency(): void
    {
        $this->getTransportMock()
            ->expects($this->never())
            ->method('call');

        $this->setAddressToConsumer();
        try {
            $this->buildService()->generateToken($this->setPurchase('BAD'));
        } catch (DomainException) {
            return;
        } catch (Exception) {
        }

        $this->fail('Error, the service must throws exception when the currency is not accepted');
    }

    public function testGenerateTokenAddressOperation(): void
    {
        $operations = ['SALE', 'AUTHORIZATION', 'ORDER'];

        foreach ($operations as $operation) {
            $this->purchase = null;
            $this->consumer = null;
            $this->transport = null;
            $exceptedBody = [
                'PAYMENTREQUEST_0_AMT' => 150.12,
                'PAYMENTREQUEST_0_PAYMENTACTION' => $operation, //SALE, Authorization, Order
                'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',
                'ADDROVERRIDE' => 1,
                'PAYMENTREQUEST_0_SHIPTONAME' => 'Roger Rabbit',
                'PAYMENTREQUEST_0_SHIPTOSTREET' => 'adr1',
                'PAYMENTREQUEST_0_SHIPTOZIP' => 14000,
                'PAYMENTREQUEST_0_SHIPTOCITY' => 'Caen',
                'PAYMENTREQUEST_0_SHIPTOSTATE' => '',
                'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'FR',
                'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
                'RETURNURL' => 'http://teknoo.software',
                'CANCELURL' => 'http://teknoo.software/cancel',
                'PAYMENTREQUEST_0_SHIPTOSTREET2' => null,
            ];

            $this->getTransportMock()
                ->expects($this->once())
                ->method('call')
                ->willReturnCallback(
                    function ($name, $args) use (&$exceptedBody): array {
                        $this->assertEquals('SetExpressCheckout', $name);
                        $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                        return [
                            'ACK' => 'SUCCESS',
                            'TOKEN' => 'tokenFake',
                        ];
                    }
                );

            $this->setAddressToConsumer();

            $purchase = $this->setPurchase('EUR', $operation);
            $purchase
                ->method('configureArgumentBag')
                ->willReturnSelf();

            $result = $this->buildService()
                ->generateToken($purchase);

            $this->assertInstanceOf(TransactionResultInterface::class, $result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEquals('tokenFake', $result->getTokenValue());
        }
    }

    public function testGenerateTokenAddressBadOperation(): void
    {
        $this->getTransportMock()
            ->expects($this->never())
            ->method('call');

        $this->setAddressToConsumer();
        try {
            $this->buildService()->generateToken($this->setPurchase('EUR', 'BAD'));
        } catch (DomainException) {
            return;
        } catch (Exception) {
        }

        $this->fail('Error, the service must throws exception when the currency is not accepted');
    }

    public function testGenerateTokenAddressFailure(): void
    {
        $exceptedBody = [
            'PAYMENTREQUEST_0_AMT' => 150.12,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE', //SALE, Authorization, Order
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
            'ADDROVERRIDE' => 1,
            'PAYMENTREQUEST_0_SHIPTONAME' => 'Roger Rabbit',
            'PAYMENTREQUEST_0_SHIPTOSTREET' => 'adr1',
            'PAYMENTREQUEST_0_SHIPTOZIP' => 14000,
            'PAYMENTREQUEST_0_SHIPTOCITY' => 'Caen',
            'PAYMENTREQUEST_0_SHIPTOSTATE' => '',
            'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'FR',
            'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
            'RETURNURL' => 'http://teknoo.software',
            'CANCELURL' => 'http://teknoo.software/cancel',
            'PAYMENTREQUEST_0_SHIPTOSTREET2' => null,
        ];

        $this->getTransportMock()
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody): array {
                    $this->assertEquals('SetExpressCheckout', $name);
                    $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                    return [
                        'ACK' => 'FAILURE',
                        'L_ERRORCODE0' => 'err1',
                        'L_SHORTMESSAGE0' => 'shortMessage',
                        'L_LONGMESSAGE0' => 'longMessage',
                        'L_SEVERITYCODE0' => 'severity',
                    ];
                }
            );

        $this->setAddressToConsumer();

        $purchase = $this->setPurchase();
        $purchase
            ->method('configureArgumentBag')
            ->willReturnSelf();

        try {
            $this->buildService()->generateToken($purchase);
        } catch (Exception $e) {
            $this->assertEquals('shortMessage : longMessage', $e->getMessage());

            return;
        }

        $this->fail('Error, on bad return must throws exception');
    }

    public function testGetTransactionResult(): void
    {
        $exceptedBody = [
            'TOKEN' => 'fakeToken',
        ];

        $this->getTransportMock()
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody): array {
                    $this->assertEquals('GetExpressCheckoutDetails', $name);
                    $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                    return [
                        'ACK' => 'SUCCESS',
                        'PAYERID' => 'idFake',
                    ];
                }
            );

        $this->setAddressToConsumer();
        $result = $this->buildService()
            ->getTransactionResult('fakeToken');

        $this->assertInstanceOf(TransactionResultInterface::class, $result);
        $this->assertTrue($result->isSuccessful());
        $this->assertEquals('idFake', $result->getPayerIdValue());
    }

    public function testConfirmTransaction(): void
    {
        $exceptedBody = [
            'TOKEN' => 'fakeToken',
            'PAYERID' => 'fakeId',
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE', //SALE, Authorization, Order
            'PAYMENTREQUEST_0_AMT' => 150.12,
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
        ];

        $this->getTransportMock()
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody): array {
                    $this->assertEquals('DoExpressCheckoutPayment', $name);
                    $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                    return [
                        'ACK' => 'SUCCESS',
                    ];
                }
            );

        $this->setAddressToConsumer();
        $result = $this->buildService()
            ->confirmTransaction('fakeToken', 'fakeId', $this->setPurchase());

        $this->assertInstanceOf(TransactionResultInterface::class, $result);
        $this->assertTrue($result->isSuccessful());
    }

    public function testPrepareTransaction(): void
    {
        $exceptedBody = [
            'PAYMENTREQUEST_0_AMT' => 150.12,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE', //SALE, Authorization, Order
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
            'ADDROVERRIDE' => 1,
            'PAYMENTREQUEST_0_SHIPTONAME' => 'Roger Rabbit',
            'PAYMENTREQUEST_0_SHIPTOSTREET' => 'adr1',
            'PAYMENTREQUEST_0_SHIPTOZIP' => 14000,
            'PAYMENTREQUEST_0_SHIPTOCITY' => 'Caen',
            'PAYMENTREQUEST_0_SHIPTOSTATE' => '',
            'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'FR',
            'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
            'RETURNURL' => 'http://teknoo.software',
            'CANCELURL' => 'http://teknoo.software/cancel',
            'PAYMENTREQUEST_0_SHIPTOSTREET2' => null,
        ];

        $this->getTransportMock()
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody): array {
                    $this->assertEquals('SetExpressCheckout', $name);
                    $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                    return [
                        'ACK' => 'SUCCESS',
                        'TOKEN' => 'tokenFake',
                    ];
                }
            );

        $this->setAddressToConsumer();
        $this->assertEquals('http://paypalUrl/tokenFake', $this->buildService()
            ->prepareTransaction($this->setPurchase()));
    }
}
