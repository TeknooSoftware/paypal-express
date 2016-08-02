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
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 *
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @version     0.8.3
 */
namespace Teknoo\tests\Paypal\Service;

use Teknoo\Paypal\Express\Entity\ConsumerInterface;
use Teknoo\Paypal\Express\Entity\PurchaseInterface;
use Teknoo\Paypal\Express\Service\ExpressCheckout;
use Teknoo\Paypal\Express\Transport\ArgumentBag;
use Teknoo\Paypal\Express\Transport\TransportInterface;

/**
 * Class ExpressCheckoutTest.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 *
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class ExpressCheckoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TransportInterface
     */
    protected $transport;

    /**
     * @var ConsumerInterface
     */
    protected $consumer;

    /**
     * @var PurchaseInterface
     */
    protected $purchase;

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TransportInterface
     */
    protected function builTransportInterfaceMock()
    {
        if (!$this->transport instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->transport = $this->getMock(
                'Teknoo\Paypal\Express\Transport\TransportInterface',
                array(),
                array(),
                '',
                false
            );

            $this->transport->expects($this->any())
                ->method('getPaypalUrl')
                ->willReturn('http://paypalUrl/{token}');
        }

        return $this->transport;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ConsumerInterface
     */
    protected function buildConsumerInterfaceMock()
    {
        if (!$this->consumer instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->consumer = $this->getMock(
                'Teknoo\Paypal\Express\Entity\ConsumerInterface',
                array(),
                array(),
                '',
                false
            );
        }

        return $this->consumer;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PurchaseInterface
     */
    protected function buildPurchaseInterfaceMock()
    {
        if (!$this->purchase instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->purchase = $this->getMock(
                'Teknoo\Paypal\Express\Entity\PurchaseInterface',
                array(),
                array(),
                '',
                false
            );
        }

        return $this->purchase;
    }

    /**
     * @return ExpressCheckout
     */
    public function buildService()
    {
        return new ExpressCheckout(
            $this->builTransportInterfaceMock()
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ConsumerInterface
     */
    protected function setIdentityToConsumer()
    {
        $consumer = $this->buildConsumerInterfaceMock();

        $this->buildPurchaseInterfaceMock()->expects($this->any())
            ->method('getConsumer')
            ->willReturn($consumer);

        $consumer->expects($this->any())
            ->method('getConsumerName')
            ->willReturn('Roger Rabbit');

        $consumer->expects($this->any())
            ->method('getPhone')
            ->willReturn('789456123');

        return $consumer;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ConsumerInterface
     */
    protected function setAddressToConsumer()
    {
        $consumer = $this->setIdentityToConsumer();

        $consumer->expects($this->any())
            ->method('getShippingAddress')
            ->willReturn('adr1');

        $consumer->expects($this->any())
            ->method('getShippingZip')
            ->willReturn(14000);

        $consumer->expects($this->any())
            ->method('getShippingCity')
            ->willReturn('Caen');

        return $consumer;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PurchaseInterface
     */
    protected function setPurchase($currency = 'EUR', $operation = 'SALE')
    {
        $purchase = $this->buildPurchaseInterfaceMock();

        $purchase->expects($this->any())
            ->method('getAmount')
            ->willReturn(150.12);

        $purchase->expects($this->any())
            ->method('getPaymentAction')
            ->willReturn($operation);

        $purchase->expects($this->any())
            ->method('getReturnUrl')
            ->willReturn('http://teknoo.software');

        $purchase->expects($this->any())
            ->method('getCancelUrl')
            ->willReturn('http://teknoo.software/cancel');

        $purchase->expects($this->any())
            ->method('getCurrencyCode')
            ->willReturn($currency);

        return $purchase;
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::__construct()
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('Teknoo\Paypal\Express\Service\ExpressCheckout', $this->buildService());
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::generateToken()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::buildTransactionResultObject()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidPaymentAction()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidCurrencyCode()
     *
     * @throws \Exception
     */
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

        $this->builTransportInterfaceMock()
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody) {
                    $this->assertEquals('SetExpressCheckout', $name);
                    $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                    return array(
                        'ACK' => 'SUCCESS',
                        'TOKEN' => 'tokenFake',
                    );
                }
            );

        $this->setIdentityToConsumer();

        $purchase = $this->setPurchase();
        $purchase->expects($this->once())
            ->method('configureArgumentBag')
            ->with($this->callback(function ($arg) {return $arg instanceof ArgumentBag; }))
            ->willReturnSelf();

        $result = $this->buildService()
            ->generateToken($purchase);

        $this->assertInstanceOf('Teknoo\Paypal\Express\Service\TransactionResultInterface', $result);
        $this->assertTrue($result->isSuccessful());
        $this->assertEquals('tokenFake', $result->getTokenValue());
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::generateToken()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::buildTransactionResultObject()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidPaymentAction()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidCurrencyCode()
     *
     * @throws \Exception
     */
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

        $this->builTransportInterfaceMock()
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody) {
                    $this->assertEquals('SetExpressCheckout', $name);
                    $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                    return array(
                        'ACK' => 'SUCCESS',
                        'TOKEN' => 'tokenFake',
                    );
                }
            );

        $this->setAddressToConsumer();

        $purchase = $this->setPurchase();
        $purchase->expects($this->once())
            ->method('configureArgumentBag')
            ->with($this->callback(function ($arg) {return $arg instanceof ArgumentBag; }))
            ->willReturnSelf();

        $result = $this->buildService()
            ->generateToken($purchase);

        $this->assertInstanceOf('Teknoo\Paypal\Express\Service\TransactionResultInterface', $result);
        $this->assertTrue($result->isSuccessful());
        $this->assertEquals('tokenFake', $result->getTokenValue());
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::generateToken()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::buildTransactionResultObject()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidPaymentAction()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidCurrencyCode()
     *
     * @throws \Exception
     */
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

            $this->builTransportInterfaceMock()
                ->expects($this->any())
                ->method('call')
                ->willReturnCallback(
                    function ($name, $args) use (&$exceptedBody) {
                        $this->assertEquals('SetExpressCheckout', $name);
                        $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                        return array(
                            'ACK' => 'SUCCESS',
                            'TOKEN' => 'tokenFake',
                        );
                    }
                );

            $this->setAddressToConsumer();

            $purchase = $this->setPurchase($currency);
            $purchase->expects($this->once())
                ->method('configureArgumentBag')
                ->with($this->callback(function ($arg) {return $arg instanceof ArgumentBag; }))
                ->willReturnSelf();

            $result = $this->buildService()
                ->generateToken($purchase);

            $this->assertInstanceOf('Teknoo\Paypal\Express\Service\TransactionResultInterface', $result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEquals('tokenFake', $result->getTokenValue());
        }
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::generateToken()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::buildTransactionResultObject()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidPaymentAction()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidCurrencyCode()
     *
     * @throws \Exception
     */
    public function testGenerateTokenAddressBadCurrency()
    {
        $this->builTransportInterfaceMock()
            ->expects($this->never())
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

    /**
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::generateToken()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::buildTransactionResultObject()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidPaymentAction()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidCurrencyCode()
     *
     * @throws \Exception
     */
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

            $this->builTransportInterfaceMock()
                ->expects($this->any())
                ->method('call')
                ->willReturnCallback(
                    function ($name, $args) use (&$exceptedBody) {
                        $this->assertEquals('SetExpressCheckout', $name);
                        $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                        return array(
                            'ACK' => 'SUCCESS',
                            'TOKEN' => 'tokenFake',
                        );
                    }
                );

            $this->setAddressToConsumer();

            $purchase = $this->setPurchase('EUR', $operation);
            $purchase->expects($this->once())
                ->method('configureArgumentBag')
                ->with($this->callback(function ($arg) {return $arg instanceof ArgumentBag; }))
                ->willReturnSelf();

            $result = $this->buildService()
                ->generateToken($purchase);

            $this->assertInstanceOf('Teknoo\Paypal\Express\Service\TransactionResultInterface', $result);
            $this->assertTrue($result->isSuccessful());
            $this->assertEquals('tokenFake', $result->getTokenValue());
        }
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::generateToken()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::buildTransactionResultObject()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidPaymentAction()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidCurrencyCode()
     *
     * @throws \Exception
     */
    public function testGenerateTokenAddressBadOperation()
    {
        $this->builTransportInterfaceMock()
            ->expects($this->never())
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

    /**
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::generateToken()
     */
    public function testGenerateTokenBadConsumer()
    {
        $this->builTransportInterfaceMock()
            ->expects($this->never())
            ->method('call');

        try {
            $this->buildService()->generateToken($this->setPurchase());
        } catch (\RuntimeException $e) {
            return;
        } catch (\Exception $e) {
        }

        $this->fail('Error, If no consumer object are provided by the purchase object, the service must throw an exception');
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::generateToken()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::buildTransactionResultObject()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidPaymentAction()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getValidCurrencyCode()
     */
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

        $this->builTransportInterfaceMock()
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody) {
                    $this->assertEquals('SetExpressCheckout', $name);
                    $this->assertEquals(new ArgumentBag($exceptedBody), $args);

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
        $purchase->expects($this->once())
            ->method('configureArgumentBag')
            ->with($this->callback(function ($arg) {return $arg instanceof ArgumentBag; }))
            ->willReturnSelf();

        try {
            $this->buildService()->generateToken($purchase);
        } catch (\Exception $e) {
            $this->assertEquals('shortMessage : longMessage', $e->getMessage());

            return;
        }

        $this->fail('Error, on bad return must throws exception');
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::getTransactionResult()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::buildTransactionResultObject()
     */
    public function testGetTransactionResult()
    {
        $exceptedBody = array(
            'TOKEN' => 'fakeToken',
        );

        $this->builTransportInterfaceMock()
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody) {
                    $this->assertEquals('GetExpressCheckoutDetails', $name);
                    $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                    return array(
                        'ACK' => 'SUCCESS',
                        'PAYERID' => 'idFake',
                    );
                }
            );

        $this->setAddressToConsumer();
        $result = $this->buildService()
            ->getTransactionResult('fakeToken');

        $this->assertInstanceOf('Teknoo\Paypal\Express\Service\TransactionResultInterface', $result);
        $this->assertTrue($result->isSuccessful());
        $this->assertEquals('idFake', $result->getPayerIdValue());
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::confirmTransaction()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::buildTransactionResultObject()
     */
    public function testConfirmTransaction()
    {
        $exceptedBody = array(
            'TOKEN' => 'fakeToken',
            'PAYERID' => 'fakeId',
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE', //SALE, Authorization, Order
            'PAYMENTREQUEST_0_AMT' => 150.12,
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
        );

        $this->builTransportInterfaceMock()
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody) {
                    $this->assertEquals('DoExpressCheckoutPayment', $name);
                    $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                    return array(
                        'ACK' => 'SUCCESS',
                    );
                }
            );

        $this->setAddressToConsumer();
        $result = $this->buildService()
            ->confirmTransaction('fakeToken', 'fakeId', $this->setPurchase());

        $this->assertInstanceOf('Teknoo\Paypal\Express\Service\TransactionResultInterface', $result);
        $this->assertTrue($result->isSuccessful());
    }

    /**
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::prepareTransaction()
     * @covers Teknoo\Paypal\Express\Service\ExpressCheckout::buildTransactionResultObject()
     */
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

        $this->builTransportInterfaceMock()
            ->expects($this->once())
            ->method('call')
            ->willReturnCallback(
                function ($name, $args) use (&$exceptedBody) {
                    $this->assertEquals('SetExpressCheckout', $name);
                    $this->assertEquals(new ArgumentBag($exceptedBody), $args);

                    return array(
                        'ACK' => 'SUCCESS',
                        'TOKEN' => 'tokenFake',
                    );
                }
            );

        $this->setAddressToConsumer();
        $this->assertEquals(
            'http://paypalUrl/tokenFake',
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'http://paypalUrl/{token}/aaa')
                ->prepareTransaction($this->setPurchase())
        );
    }
}
