<?php

/**
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
 *
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 *
 */
namespace Teknoo\tests\Paypal\Service;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Teknoo\Paypal\Express\Contract\ConsumerInterface;
use Teknoo\Paypal\Express\Contract\ConsumerWithCountryInterface;
use Teknoo\Paypal\Express\Contract\PurchaseInterface;
use Teknoo\Paypal\Express\Service\ExpressCheckout;
use Teknoo\Paypal\Express\Service\TransactionResultInterface;
use Teknoo\Paypal\Express\Transport\ArgumentBag;
use Teknoo\Paypal\Express\Transport\TransportInterface;

/**
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 *
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\Paypal\Express\Service\ExpressCheckout
 */
class ExpressCheckoutTest extends TestCase
{
    private ?TransportInterface $transport = null;

    private ?ConsumerInterface $consumer = null;

    private ?PurchaseInterface $purchase = null;

    /**
     * @return MockObject|TransportInterface
     */
    private function getTransportMock(): TransportInterface
    {
        if (!$this->transport instanceof MockObject) {
            $this->transport = $this->createMock(TransportInterface::class);
        }

        return $this->transport;
    }

    /**
     * @return MockObject|ConsumerInterface
     */
    private function getConsumerMock(): ConsumerInterface
    {
        if (!$this->consumer instanceof MockObject) {
            $this->consumer = $this->createMock(ConsumerInterface::class);
        }

        return $this->consumer;
    }

    /**
     * @return MockObject|ConsumerWithCountryInterface
     */
    private function getConsumerWithCountryMock(): ConsumerWithCountryInterface
    {
        if (!$this->consumer instanceof MockObject) {
            $this->consumer = $this->createMock(ConsumerWithCountryInterface::class);
        }

        return $this->consumer;
    }

    /**
     * @return MockObject|PurchaseInterface
     */
    private function getPurchaseMock(): PurchaseInterface
    {
        if (!$this->purchase instanceof MockObject) {
            $this->purchase = $this->createMock(PurchaseInterface::class);
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
            $consumer = $this->getConsumerWithCountryMock();
        } else {
            $consumer = $this->getConsumerMock();
        }

        $this->getPurchaseMock()->expects(self::any())
            ->method('getConsumer')
            ->willReturn($consumer);

        $consumer->expects(self::any())
            ->method('getConsumerName')
            ->willReturn('Roger Rabbit');

        $consumer->expects(self::any())
            ->method('getPhone')
            ->willReturn('789456123');

        return $consumer;
    }

    private function setAddressToConsumer(bool $withCountry = false): ConsumerInterface
    {
        $consumer = $this->setIdentityToConsumer($withCountry);

        $consumer->expects(self::any())
            ->method('getShippingAddress')
            ->willReturn('adr1');

        $consumer->expects(self::any())
            ->method('getShippingZip')
            ->willReturn('14000');

        $consumer->expects(self::any())
            ->method('getShippingCity')
            ->willReturn('Caen');

        if (true === $withCountry) {
            $consumer->expects(self::any())
                ->method('getShippingCountryCode')
                ->willReturn('US');

            $consumer->expects(self::any())
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

        $purchase->expects(self::any())
            ->method('getAmount')
            ->willReturn(150.12);

        $purchase->expects(self::any())
            ->method('getPaymentAction')
            ->willReturn($operation);

        $purchase->expects(self::any())
            ->method('getReturnUrl')
            ->willReturn('http://teknoo.software');

        $purchase->expects(self::any())
            ->method('getCancelUrl')
            ->willReturn('http://teknoo.software/cancel');

        $purchase->expects(self::any())
            ->method('getCurrencyCode')
            ->willReturn($currency);

        return $purchase;
    }

    public function testConstruct()
    {
        self::assertInstanceOf(ExpressCheckout::class, $this->buildService());
    }

    public function testGenerateTokenWithoutAddress()
    {
        $exceptedBody = array(
            'PAYMENTREQUEST_0_AMT' => 150.12,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE', //SALE, Authorization, Order
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
            'ADDROVERRIDE' => 1,
            'PAYMENTREQUEST_0_SHIPTONAME' => 'Roger Rabbit',
            'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
            'RETURNURL' => 'http://teknoo.software',
            'CANCELURL' => 'http://teknoo.software/cancel',
        );

        $this->getTransportMock()
            ->expects(self::once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody) {
                    self::assertEquals('SetExpressCheckout', $name);
                    self::assertEquals(new ArgumentBag($exceptedBody), $args);

                    return array(
                        'ACK' => 'SUCCESS',
                        'TOKEN' => 'tokenFake',
                    );
                }
            );

        $this->setIdentityToConsumer();

        $purchase = $this->setPurchase();
        $purchase->expects(self::once())
            ->method('configureArgumentBag')
            ->with($this->callback(function ($arg) {
                return $arg instanceof ArgumentBag;
            }))
            ->willReturnSelf();

        $result = $this->buildService()
            ->generateToken($purchase);

        self::assertInstanceOf(TransactionResultInterface::class, $result);
        self::assertTrue($result->isSuccessful());
        self::assertEquals('tokenFake', $result->getTokenValue());
    }

    public function testGenerateTokenAddress()
    {
        $exceptedBody = array(
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
        );

        $this->getTransportMock()
            ->expects(self::once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody) {
                    self::assertEquals('SetExpressCheckout', $name);
                    self::assertEquals(new ArgumentBag($exceptedBody), $args);

                    return array(
                        'ACK' => 'SUCCESS',
                        'TOKEN' => 'tokenFake',
                    );
                }
            );

        $this->setAddressToConsumer();

        $purchase = $this->setPurchase();
        $purchase->expects(self::once())
            ->method('configureArgumentBag')
            ->with($this->callback(function ($arg) {
                return $arg instanceof ArgumentBag;
            }))
            ->willReturnSelf();

        $result = $this->buildService()
            ->generateToken($purchase);

        self::assertInstanceOf(TransactionResultInterface::class, $result);
        self::assertTrue($result->isSuccessful());
        self::assertEquals('tokenFake', $result->getTokenValue());
    }

    public function testGenerateTokenAddressWithCountry()
    {
        $exceptedBody = array(
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
        );

        $this->getTransportMock()
            ->expects(self::once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody) {
                    self::assertEquals('SetExpressCheckout', $name);
                    self::assertEquals(new ArgumentBag($exceptedBody), $args);

                    return array(
                        'ACK' => 'SUCCESS',
                        'TOKEN' => 'tokenFake',
                    );
                }
            );

        $this->setAddressToConsumer(true);

        $purchase = $this->setPurchase();
        $purchase->expects(self::once())
            ->method('configureArgumentBag')
            ->with($this->callback(function ($arg) {
                return $arg instanceof ArgumentBag;
            }))
            ->willReturnSelf();

        $result = $this->buildService()
            ->generateToken($purchase);

        self::assertInstanceOf(TransactionResultInterface::class, $result);
        self::assertTrue($result->isSuccessful());
        self::assertEquals('tokenFake', $result->getTokenValue());
    }

    public function testGenerateTokenAddressCurrency()
    {
        $currencies = ['AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK', 'NZD', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'TWD', 'THB', 'TRY', 'USD'];

        foreach ($currencies as $currency) {
            $this->purchase = null;
            $this->consumer = null;
            $this->transport = null;
            $exceptedBody = array(
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
            );

            $this->getTransportMock()
                ->expects(self::any())
                ->method('call')
                ->willReturnCallback(
                    function ($name, $args) use (&$exceptedBody) {
                        self::assertEquals('SetExpressCheckout', $name);
                        self::assertEquals(new ArgumentBag($exceptedBody), $args);

                        return array(
                            'ACK' => 'SUCCESS',
                            'TOKEN' => 'tokenFake',
                        );
                    }
                );

            $this->setAddressToConsumer();

            $purchase = $this->setPurchase($currency);
            $purchase->expects(self::once())
                ->method('configureArgumentBag')
                ->with($this->callback(function ($arg) {
                    return $arg instanceof ArgumentBag;
                }))
                ->willReturnSelf();

            $result = $this->buildService()
                ->generateToken($purchase);

            self::assertInstanceOf(TransactionResultInterface::class, $result);
            self::assertTrue($result->isSuccessful());
            self::assertEquals('tokenFake', $result->getTokenValue());
        }
    }

    public function testGenerateTokenAddressBadCurrency()
    {
        $this->getTransportMock()
            ->expects(self::never())
            ->method('call');

        $this->setAddressToConsumer();
        try {
            $this->buildService()->generateToken($this->setPurchase('BAD'));
        } catch (\DomainException $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the service must throws exception when the currency is not accepted');
    }

    public function testGenerateTokenAddressOperation()
    {
        $operations = ['SALE', 'AUTHORIZATION', 'ORDER'];

        foreach ($operations as $operation) {
            $this->purchase = null;
            $this->consumer = null;
            $this->transport = null;
            $exceptedBody = array(
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
            );

            $this->getTransportMock()
                ->expects(self::any())
                ->method('call')
                ->willReturnCallback(
                    function ($name, $args) use (&$exceptedBody) {
                        self::assertEquals('SetExpressCheckout', $name);
                        self::assertEquals(new ArgumentBag($exceptedBody), $args);

                        return array(
                            'ACK' => 'SUCCESS',
                            'TOKEN' => 'tokenFake',
                        );
                    }
                );

            $this->setAddressToConsumer();

            $purchase = $this->setPurchase('EUR', $operation);
            $purchase->expects(self::once())
                ->method('configureArgumentBag')
                ->with($this->callback(function ($arg) {
                    return $arg instanceof ArgumentBag;
                }))
                ->willReturnSelf();

            $result = $this->buildService()
                ->generateToken($purchase);

            self::assertInstanceOf(TransactionResultInterface::class, $result);
            self::assertTrue($result->isSuccessful());
            self::assertEquals('tokenFake', $result->getTokenValue());
        }
    }

    public function testGenerateTokenAddressBadOperation()
    {
        $this->getTransportMock()
            ->expects(self::never())
            ->method('call');

        $this->setAddressToConsumer();
        try {
            $this->buildService()->generateToken($this->setPurchase('EUR', 'BAD'));
        } catch (\DomainException $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, the service must throws exception when the currency is not accepted');
    }

    public function testGenerateTokenAddressFailure()
    {
        $exceptedBody = array(
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
        );

        $this->getTransportMock()
            ->expects(self::once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody) {
                    self::assertEquals('SetExpressCheckout', $name);
                    self::assertEquals(new ArgumentBag($exceptedBody), $args);

                    return array(
                        'ACK' => 'FAILURE',
                        'L_ERRORCODE0' => 'err1',
                        'L_SHORTMESSAGE0' => 'shortMessage',
                        'L_LONGMESSAGE0' => 'longMessage',
                        'L_SEVERITYCODE0' => 'severity',
                    );
                }
            );

        $this->setAddressToConsumer();

        $purchase = $this->setPurchase();
        $purchase->expects(self::once())
            ->method('configureArgumentBag')
            ->with($this->callback(function ($arg) {
                return $arg instanceof ArgumentBag;
            }))
            ->willReturnSelf();

        try {
            $this->buildService()->generateToken($purchase);
        } catch (\Exception $e) {
            self::assertEquals('shortMessage : longMessage', $e->getMessage());

            return;
        }

        $this->fail('Error, on bad return must throws exception');
    }

    public function testGetTransactionResult()
    {
        $exceptedBody = array(
            'TOKEN' => 'fakeToken',
        );

        $this->getTransportMock()
            ->expects(self::once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody) {
                    self::assertEquals('GetExpressCheckoutDetails', $name);
                    self::assertEquals(new ArgumentBag($exceptedBody), $args);

                    return array(
                        'ACK' => 'SUCCESS',
                        'PAYERID' => 'idFake',
                    );
                }
            );

        $this->setAddressToConsumer();
        $result = $this->buildService()
            ->getTransactionResult('fakeToken');

        self::assertInstanceOf(TransactionResultInterface::class, $result);
        self::assertTrue($result->isSuccessful());
        self::assertEquals('idFake', $result->getPayerIdValue());
    }

    public function testConfirmTransaction()
    {
        $exceptedBody = array(
            'TOKEN' => 'fakeToken',
            'PAYERID' => 'fakeId',
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE', //SALE, Authorization, Order
            'PAYMENTREQUEST_0_AMT' => 150.12,
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
        );

        $this->getTransportMock()
            ->expects(self::once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody) {
                    self::assertEquals('DoExpressCheckoutPayment', $name);
                    self::assertEquals(new ArgumentBag($exceptedBody), $args);

                    return array(
                        'ACK' => 'SUCCESS',
                    );
                }
            );

        $this->setAddressToConsumer();
        $result = $this->buildService()
            ->confirmTransaction('fakeToken', 'fakeId', $this->setPurchase());

        self::assertInstanceOf(TransactionResultInterface::class, $result);
        self::assertTrue($result->isSuccessful());
    }

    public function testPrepareTransaction()
    {
        $exceptedBody = array(
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
        );

        $this->getTransportMock()
            ->expects(self::once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody) {
                    self::assertEquals('SetExpressCheckout', $name);
                    self::assertEquals(new ArgumentBag($exceptedBody), $args);

                    return array(
                        'ACK' => 'SUCCESS',
                        'TOKEN' => 'tokenFake',
                    );
                }
            );

        $this->setAddressToConsumer();
        self::assertEquals(
            'http://paypalUrl/tokenFake',
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'http://paypalUrl/{token}/aaa')
                ->prepareTransaction($this->setPurchase(), 'http://paypalUrl/{token}')
        );
    }
}
