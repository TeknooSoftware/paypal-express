<?php

namespace UniAlteri\Tests\Paypal\Entity;

use UniAlteri\Paypal\Express\Entity\ConsumerInterface;
use UniAlteri\Paypal\Express\Entity\PurchaseInterface;
use UniAlteri\Paypal\Express\Service\ExpressCheckout;
use UniAlteri\Paypal\Express\Transport\ArgumentBag;
use UniAlteri\Paypal\Express\Transport\TransportInterface;

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
                'UniAlteri\Paypal\Express\Transport\TransportInterface',
                array(),
                array(),
                '',
                false
            );
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
                'UniAlteri\Paypal\Express\Entity\ConsumerInterface',
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
                'UniAlteri\Paypal\Express\Entity\PurchaseInterface',
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
    protected function setPurchase()
    {
        $purchase =$this->buildPurchaseInterfaceMock();

        $purchase->expects($this->any())
            ->method('getAmount')
            ->willReturn(150.12);

        $purchase->expects($this->any())
            ->method('getPaymentAction')
            ->willReturn('Sale');

        $purchase->expects($this->any())
            ->method('getReturnUrl')
            ->willReturn('http://teknoo.it');

        $purchase->expects($this->any())
            ->method('getCancelUrl')
            ->willReturn('http://teknoo.it/cancel');

        $purchase->expects($this->any())
            ->method('getCurrencyCode')
            ->willReturn('EUR');

        return $purchase;
    }

    public function testGenerateTokenWithoutAddress()
    {
        $exceptedBody = array(
            'PAYMENTREQUEST_0_AMT' => 150.12,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale', //Sale, Authorization, Order
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
            'ADDROVERRIDE' => 1,
            'PAYMENTREQUEST_0_SHIPTONAME' => 'Roger Rabbit',
            'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
            'RETURNURL' => 'http://teknoo.it',
            'CANCELURL' => 'http://teknoo.it/cancel'
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
                        'TOKEN' => 'tokenFake'
                    );
                }
            );

        $this->setIdentityToConsumer();
        $result = $this->buildService()
            ->generateToken($this->setPurchase());

        $this->assertTrue($result->isSuccessful());
        $this->assertEquals('tokenFake', $result->getTokenValue());
    }

    public function testGenerateTokenAddress()
    {
        $exceptedBody = array(
            'PAYMENTREQUEST_0_AMT' => 150.12,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale', //Sale, Authorization, Order
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
            'ADDROVERRIDE' => 1,
            'PAYMENTREQUEST_0_SHIPTONAME' => 'Roger Rabbit',
            'PAYMENTREQUEST_0_SHIPTOSTREET' => 'adr1',
            'PAYMENTREQUEST_0_SHIPTOZIP' => 14000,
            'PAYMENTREQUEST_0_SHIPTOCITY' => 'Caen',
            'PAYMENTREQUEST_0_SHIPTOSTATE' => '',
            'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'FR',
            'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
            'RETURNURL' => 'http://teknoo.it',
            'CANCELURL' => 'http://teknoo.it/cancel',
            'PAYMENTREQUEST_0_SHIPTOSTREET2' => null
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
                        'TOKEN' => 'tokenFake'
                    );
                }
            );

        $this->setAddressToConsumer();
        $result = $this->buildService()
            ->generateToken($this->setPurchase());

        $this->assertTrue($result->isSuccessful());
        $this->assertEquals('tokenFake', $result->getTokenValue());
    }

    public function testGenerateTokenAddressFailure()
    {
        $exceptedBody = array(
            'PAYMENTREQUEST_0_AMT' => 150.12,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale', //Sale, Authorization, Order
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
            'ADDROVERRIDE' => 1,
            'PAYMENTREQUEST_0_SHIPTONAME' => 'Roger Rabbit',
            'PAYMENTREQUEST_0_SHIPTOSTREET' => 'adr1',
            'PAYMENTREQUEST_0_SHIPTOZIP' => 14000,
            'PAYMENTREQUEST_0_SHIPTOCITY' => 'Caen',
            'PAYMENTREQUEST_0_SHIPTOSTATE' => '',
            'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'FR',
            'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
            'RETURNURL' => 'http://teknoo.it',
            'CANCELURL' => 'http://teknoo.it/cancel',
            'PAYMENTREQUEST_0_SHIPTOSTREET2' => null
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
                        'L_SEVERITYCODE0' => 'severity'
                    );
                }
            );

        $this->setAddressToConsumer();
        try {
            $this->buildService()->generateToken($this->setPurchase());
        } catch (\Exception $e) {
            $this->assertEquals('shortMessage : longMessage', $e->getMessage());
            return;
        }

        $this->fail('Error, on bad return must throws exception');
    }

    public function testGetTransactionResult()
    {
        $requestMock = $this->getMock(
            '\Zeroem\CurlBundle\Curl\Request',
            array(),
            array(),
            '',
            false
        );

        $curlMock = $this->builRequestGeneratorMock();
        $curlMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

        $requestMock->expects($this->once())
            ->method('setMethod')
            ->with('POST');

        $exceptedBody = http_build_query(
            array(
                'TOKEN' => '789456123',
                'METHOD' => 'GetExpressCheckoutDetails',
                'VERSION' => 93,
                'PWD' => 'pwd',
                'USER' => '123',
                'SIGNATURE' => 'azer',
                'BUTTONSOURCE' => 'PP-ECWizard'
            )
        );

        $requestMock->expects($this->any())
            ->method('setOption')
            ->withConsecutive(
                [CURLOPT_URL,'endPointFake'],
                [CURLOPT_VERBOSE,true],
                [CURLOPT_SSL_VERIFYPEER,false],
                [CURLOPT_SSL_VERIFYHOST,0],
                [CURLOPT_RETURNTRANSFER,true],
                [CURLOPT_POST,true],
                [CURLOPT_TIMEOUT,60*10],
                [CURLOPT_CONNECTTIMEOUT,60],
                [CURLOPT_POSTFIELDS,$exceptedBody]
            );

        $requestMock->expects($this->once())
            ->method('execute')
            ->willReturn(
                urlencode(
                    http_build_query(
                        array('foo'=>'bar')
                    )
                )
            );

        $this->assertEquals(
            array('foo'=>'bar'),
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'paypalUrl')
                ->getTransactionResult('789456123')
        );
    }

    public function testConfirmTransaction()
    {
        $invoice = $this->getMock('Areha\ProRt2012Bundle\Entity\Invoice', ['computeTTC', 'getUser'], [], '', false);
        $invoice->expects($this->any())->method('computeTTC')->willReturn(15000);

        $requestMock = $this->getMock(
            '\Zeroem\CurlBundle\Curl\Request',
            array(),
            array(),
            '',
            false
        );

        $curlMock = $this->builRequestGeneratorMock();
        $curlMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

        $requestMock->expects($this->once())
            ->method('setMethod')
            ->with('POST');

        $exceptedBody = http_build_query(
            array(
                'TOKEN' => '789456123',
                'PAYERID' => 'aaaa',
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE',
                'PAYMENTREQUEST_0_AMT' => (15000/100),
                'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR',
                'METHOD' => 'DoExpressCheckoutPayment',
                'VERSION' => 93,
                'PWD' => 'pwd',
                'USER' => '123',
                'SIGNATURE' => 'azer',
                'BUTTONSOURCE' => 'PP-ECWizard'
            )
        );

        $requestMock->expects($this->any())
            ->method('setOption')
            ->withConsecutive(
                [CURLOPT_URL,'endPointFake'],
                [CURLOPT_VERBOSE,true],
                [CURLOPT_SSL_VERIFYPEER,false],
                [CURLOPT_SSL_VERIFYHOST,0],
                [CURLOPT_RETURNTRANSFER,true],
                [CURLOPT_POST,true],
                [CURLOPT_TIMEOUT,60*10],
                [CURLOPT_CONNECTTIMEOUT,60],
                [CURLOPT_POSTFIELDS,$exceptedBody]
            );

        $requestMock->expects($this->once())
            ->method('execute')
            ->willReturn(
                urlencode(
                    http_build_query(
                        array('foo'=>'bar')
                    )
                )
            );

        $this->assertEquals(
            array('foo'=>'bar'),
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'paypalUrl')
                ->confirmTransaction('789456123', 'aaaa', $invoice)
        );
    }

    public function testPrepareTransaction()
    {
        $this->assertEquals(
            'http://paypalUrl/789456123/aaa',
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'http://paypalUrl/{token}/aaa')
                ->prepareTransaction('789456123')
        );
    }
}