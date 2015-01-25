<?php

namespace UniAlteri\Tests\Paypal\Entity;

use UniAlteri\Paypal\Express\Entity\ConsumerInterface;
use UniAlteri\Paypal\Express\Entity\PurchaseInterface;
use UniAlteri\Paypal\Express\Service\ExpressCheckout;
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
            ->willReturn('0102030405');

        return $consumer;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ConsumerInterface
     */
    protected function setAddressToConsumer()
    {
        $consumer = $this->setIdentityToConsumer();

        return $consumer;
    }

    public function testGenerateTokenWithoutAddress()
    {
        $user = new User();
        $user->setFirstName('prenom');
        $user->setLastName('nom');
        $user->setTel('789456123');

        $project = new Project();
        $project->setState(Project::STATE_ORDER);
        $project->updateState();
        $project->setUser($user);

        $invoice = $this->getMock('Areha\ProRt2012Bundle\Entity\Invoice', ['computeTTC', 'getUser'], [], '', false);
        $invoice->expects($this->any())->method('computeTTC')->willReturn(15000);
        $invoice->expects($this->any())->method('getUser')->willReturn($user);

        $requestMock = $this->getMock(
            '\Zeroem\CurlBundle\Curl\Request',
            array(),
            array(),
            '',
            false
        );

        $curlMock = $this->builRequestGeneratorMock();
        $curlMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($requestMock);

        $requestMock->expects($this->any())
            ->method('setMethod')
            ->with('POST');

        $exceptedBody = http_build_query(
            array(
                'PAYMENTREQUEST_0_AMT' => (15000/100),
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE', //Sale, Authorization, Order
                'RETURNURL' => 'returnUrl',
                'CANCELURL' => 'cancelUrl',
                'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
                'ADDROVERRIDE' => 1,
                'PAYMENTREQUEST_0_SHIPTONAME' => 'prenom nom',
                'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
                'METHOD' => 'SetExpressCheckout',
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
                        array(
                            'ACK' => true,
                            'TOKEN' => 'tokenFake'
                        )
                    )
                )
            );

        $this->assertEquals(
            'tokenFake',
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'paypalUrl')
                ->generateToken($project, $invoice, 'returnUrl', 'cancelUrl')
        );
    }

    public function testGenerateTokenAddress()
    {
        $user = new User();
        $user->setFirstName('prenom');
        $user->setLastName('nom');
        $user->setTel('789456123');
        $user->setAddress('adr1');
        $user->setZip('14000');
        $user->setCity('caen');

        $project = new Project();
        $project->setState(Project::STATE_ORDER);
        $project->updateState();
        $project->setUser($user);

        $invoice = $this->getMock('Areha\ProRt2012Bundle\Entity\Invoice', ['computeTTC', 'getUser'], [], '', false);
        $invoice->expects($this->any())->method('computeTTC')->willReturn(15000);
        $invoice->expects($this->any())->method('getUser')->willReturn($user);

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
                'PAYMENTREQUEST_0_AMT' => (15000/100),
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE', //Sale, Authorization, Order
                'RETURNURL' => 'returnUrl',
                'CANCELURL' => 'cancelUrl',
                'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
                'ADDROVERRIDE' => 1,
                'PAYMENTREQUEST_0_SHIPTONAME' => 'prenom nom',
                'PAYMENTREQUEST_0_SHIPTOSTREET' => 'adr1',
                'PAYMENTREQUEST_0_SHIPTOZIP' => 14000,
                'PAYMENTREQUEST_0_SHIPTOCITY' => 'caen',
                'PAYMENTREQUEST_0_SHIPTOSTATE' => '',
                'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'FR',
                'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
                'METHOD' => 'SetExpressCheckout',
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
                        array(
                            'ACK' => true,
                            'TOKEN' => 'tokenFake'
                        )
                    )
                )
            );

        $this->assertEquals(
            'tokenFake',
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'paypalUrl')
                ->generateToken($project, $invoice, 'returnUrl', 'cancelUrl')
        );
    }

    public function testGenerateTokenAddressNo()
    {
        $user = new User();
        $user->setFirstName('prenom');
        $user->setLastName('nom');
        $user->setTel('789456123');
        $user->setAddress('adr1');
        $user->setZip('14000');
        $user->setCity('caen');

        $project = new Project();
        $project->setState(Project::STATE_ORDER);
        $project->updateState();
        $project->setUser($user);

        $invoice = $this->getMock('Areha\ProRt2012Bundle\Entity\Invoice', ['computeTTC', 'getUser'], [], '', false);
        $invoice->expects($this->any())->method('computeTTC')->willReturn(15000);
        $invoice->expects($this->any())->method('getUser')->willReturn($user);

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
                'PAYMENTREQUEST_0_AMT' => (15000/100),
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE', //Sale, Authorization, Order
                'RETURNURL' => 'returnUrl',
                'CANCELURL' => 'cancelUrl',
                'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR', //USD, ...
                'ADDROVERRIDE' => 1,
                'PAYMENTREQUEST_0_SHIPTONAME' => 'prenom nom',
                'PAYMENTREQUEST_0_SHIPTOSTREET' => 'adr1',
                'PAYMENTREQUEST_0_SHIPTOZIP' => 14000,
                'PAYMENTREQUEST_0_SHIPTOCITY' => 'caen',
                'PAYMENTREQUEST_0_SHIPTOSTATE' => '',
                'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => 'FR',
                'PAYMENTREQUEST_0_SHIPTOPHONENUM' => '789456123',
                'METHOD' => 'SetExpressCheckout',
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
                        array(
                        )
                    )
                )
            );

        $this->assertFalse(
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'paypalUrl')
                ->generateToken($project, $invoice, 'returnUrl', 'cancelUrl')
        );
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