<?php

namespace UniAlteri\Tests\Paypal\Entity;

use UniAlteri\Paypal\Express\Service\ExpressCheckout;
use UniAlteri\Paypal\Express\Transport\TransportInterface;

class ExpressCheckoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TransportInterface
     */
    protected $transport;

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
     * @param string $userId
     * @param string $password
     * @param string $signature
     * @param boolean $sandbox
     * @param boolean $apiEndPoint
     * @param boolean $paypalUrl
     * @return ExpressCheckout
     */
    public function buildService($userId, $password, $signature, $sandbox, $apiEndPoint, $paypalUrl)
    {
        return new ExpressCheckout(
            $this->builTransportInterfaceMock()
        );
    }

    public function testGetTokenWithoutAddress()
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
                ->getToken($project, $invoice, 'returnUrl', 'cancelUrl')
        );
    }

    public function testGetTokenAddress()
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
                ->getToken($project, $invoice, 'returnUrl', 'cancelUrl')
        );
    }

    public function testGetTokenAddressNo()
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
                ->getToken($project, $invoice, 'returnUrl', 'cancelUrl')
        );
    }

    public function testGetShippingResult()
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
                ->getShippingResult('789456123')
        );
    }

    public function testConfirmPayment()
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
                ->confirmPayment('789456123', 'aaaa', $invoice)
        );
    }

    public function testCheckAndValidatePaymentNoAck()
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

        try {
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'paypalUrl')
                ->checkAndValidatePayment('789456123', $invoice);
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, if no ack, exception must be throw');
    }

    public function testCheckAndValidatePaymentNoSuccess()
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
                        array('ACK'=>'No')
                    )
                )
            );

        $this->assertFalse(
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'paypalUrl')
                ->checkAndValidatePayment('789456123', $invoice)
        );
    }

    public function testCheckAndValidatePaymentNoConfirmNoWarningException()
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
                'METHOD' => 'GetExpressCheckoutDetails',
                'VERSION' => 93,
                'PWD' => 'pwd',
                'USER' => '123',
                'SIGNATURE' => 'azer',
                'BUTTONSOURCE' => 'PP-ECWizard'
            )
        );

        $exceptedBody2 = http_build_query(
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
                [CURLOPT_POSTFIELDS,$exceptedBody],
                [CURLOPT_URL,'endPointFake'],
                [CURLOPT_VERBOSE,true],
                [CURLOPT_SSL_VERIFYPEER,false],
                [CURLOPT_SSL_VERIFYHOST,0],
                [CURLOPT_RETURNTRANSFER,true],
                [CURLOPT_POST,true],
                [CURLOPT_TIMEOUT,60*10],
                [CURLOPT_CONNECTTIMEOUT,60],
                [CURLOPT_POSTFIELDS,$exceptedBody2]
            );

        $counter = 0;
        $requestMock->expects($this->once())
            ->method('execute')
            ->willReturn(
                function () use (&$counter) {
                    if (0 == $counter++) {
                        return urlencode(
                            http_build_query(
                                array('ACK' => 'SUCCESS', 'PAYERID' => 'aaaa')
                            )
                        );
                    } else {
                        return urlencode(
                            http_build_query(
                                array('foo'=>'bar')
                            )
                        );
                    }
                }
            );

        try {
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'paypalUrl')
                ->checkAndValidatePayment('789456123', $invoice);
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, if no ack, exception must be throw');
    }

    public function testCheckAndValidatePaymentConfirmNoWarning()
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
        $curlMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($requestMock);

        $requestMock->expects($this->any())
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

        $exceptedBody2 = http_build_query(
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
                [CURLOPT_POSTFIELDS,$exceptedBody],
                [CURLOPT_URL,'endPointFake'],
                [CURLOPT_VERBOSE,true],
                [CURLOPT_SSL_VERIFYPEER,false],
                [CURLOPT_SSL_VERIFYHOST,0],
                [CURLOPT_RETURNTRANSFER,true],
                [CURLOPT_POST,true],
                [CURLOPT_TIMEOUT,60*10],
                [CURLOPT_CONNECTTIMEOUT,60],
                [CURLOPT_POSTFIELDS,$exceptedBody2]
            );

        $counter = 0;
        $requestMock->expects($this->any())
            ->method('execute')
            ->willReturnCallback(
                function () use (&$counter) {
                    if (0 == $counter++) {
                        return urlencode(
                            http_build_query(
                                array('ACK' => 'SUCCESS', 'PAYERID' => 'aaaa')
                            )
                        );
                    } else {
                        return urlencode(
                            http_build_query(
                                array('ACK'=>'SUCCESS')
                            )
                        );
                    }
                }
            );

        $this->assertTrue(
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'paypalUrl')
                ->checkAndValidatePayment('789456123', $invoice)
        );
    }

    public function testCheckAndValidatePaymentConfirmNoWarningWarning()
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
        $curlMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($requestMock);

        $requestMock->expects($this->any())
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

        $exceptedBody2 = http_build_query(
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
                [CURLOPT_POSTFIELDS,$exceptedBody],
                [CURLOPT_URL,'endPointFake'],
                [CURLOPT_VERBOSE,true],
                [CURLOPT_SSL_VERIFYPEER,false],
                [CURLOPT_SSL_VERIFYHOST,0],
                [CURLOPT_RETURNTRANSFER,true],
                [CURLOPT_POST,true],
                [CURLOPT_TIMEOUT,60*10],
                [CURLOPT_CONNECTTIMEOUT,60],
                [CURLOPT_POSTFIELDS,$exceptedBody2]
            );

        $counter = 0;
        $requestMock->expects($this->any())
            ->method('execute')
            ->willReturnCallback(
                function () use (&$counter) {
                    if (0 == $counter++) {
                        return urlencode(
                            http_build_query(
                                array('ACK' => 'SUCCESS', 'PAYERID' => 'aaaa')
                            )
                        );
                    } else {
                        return urlencode(
                            http_build_query(
                                array('ACK'=>'SUCCESSWITHWARNING')
                            )
                        );
                    }
                }
            );

        $this->assertTrue(
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'paypalUrl')
                ->checkAndValidatePayment('789456123', $invoice)
        );
    }

    public function testCheckAndValidatePaymentConfirmWarningWarning()
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
        $curlMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($requestMock);

        $requestMock->expects($this->any())
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

        $exceptedBody2 = http_build_query(
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
                [CURLOPT_POSTFIELDS,$exceptedBody],
                [CURLOPT_URL,'endPointFake'],
                [CURLOPT_VERBOSE,true],
                [CURLOPT_SSL_VERIFYPEER,false],
                [CURLOPT_SSL_VERIFYHOST,0],
                [CURLOPT_RETURNTRANSFER,true],
                [CURLOPT_POST,true],
                [CURLOPT_TIMEOUT,60*10],
                [CURLOPT_CONNECTTIMEOUT,60],
                [CURLOPT_POSTFIELDS,$exceptedBody2]
            );

        $counter = 0;
        $requestMock->expects($this->any())
            ->method('execute')
            ->willReturnCallback(
                function () use (&$counter) {
                    if (0 == $counter++) {
                        return urlencode(
                            http_build_query(
                                array('ACK' => 'SUCCESSWITHWARNING', 'PAYERID' => 'aaaa')
                            )
                        );
                    } else {
                        return urlencode(
                            http_build_query(
                                array('ACK'=>'SUCCESSWITHWARNING')
                            )
                        );
                    }
                }
            );

        $this->assertTrue(
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'paypalUrl')
                ->checkAndValidatePayment('789456123', $invoice)
        );
    }

    public function testCheckAndValidatePaymentConfirmWarningNo()
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
        $curlMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($requestMock);

        $requestMock->expects($this->any())
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

        $exceptedBody2 = http_build_query(
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
                [CURLOPT_POSTFIELDS,$exceptedBody],
                [CURLOPT_URL,'endPointFake'],
                [CURLOPT_VERBOSE,true],
                [CURLOPT_SSL_VERIFYPEER,false],
                [CURLOPT_SSL_VERIFYHOST,0],
                [CURLOPT_RETURNTRANSFER,true],
                [CURLOPT_POST,true],
                [CURLOPT_TIMEOUT,60*10],
                [CURLOPT_CONNECTTIMEOUT,60],
                [CURLOPT_POSTFIELDS,$exceptedBody2]
            );

        $counter = 0;
        $requestMock->expects($this->any())
            ->method('execute')
            ->willReturnCallback(
                function () use (&$counter) {
                    if (0 == $counter++) {
                        return urlencode(
                            http_build_query(
                                array('ACK' => 'SUCCESSWITHWARNING', 'PAYERID' => 'aaaa')
                            )
                        );
                    } else {
                        return urlencode(
                            http_build_query(
                                array('ACK'=>'NO')
                            )
                        );
                    }
                }
            );

        $this->assertFalse(
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'paypalUrl')
                ->checkAndValidatePayment('789456123', $invoice)
        );
    }

    public function testCheckAndValidatePaymentConfirmWarningException()
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
        $curlMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($requestMock);

        $requestMock->expects($this->any())
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

        $exceptedBody2 = http_build_query(
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
                [CURLOPT_POSTFIELDS,$exceptedBody],
                [CURLOPT_URL,'endPointFake'],
                [CURLOPT_VERBOSE,true],
                [CURLOPT_SSL_VERIFYPEER,false],
                [CURLOPT_SSL_VERIFYHOST,0],
                [CURLOPT_RETURNTRANSFER,true],
                [CURLOPT_POST,true],
                [CURLOPT_TIMEOUT,60*10],
                [CURLOPT_CONNECTTIMEOUT,60],
                [CURLOPT_POSTFIELDS,$exceptedBody2]
            );

        $counter = 0;
        $requestMock->expects($this->any())
            ->method('execute')
            ->willReturnCallback(
                function () use (&$counter) {
                    if (0 == $counter++) {
                        return urlencode(
                            http_build_query(
                                array('ACK' => 'SUCCESSWITHWARNING', 'PAYERID' => 'aaaa')
                            )
                        );
                    } else {
                        return urlencode(
                            http_build_query(
                                array('foo'=>'bar')
                            )
                        );
                    }
                }
            );

        try {
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'paypalUrl')
                ->checkAndValidatePayment('789456123', $invoice);
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, exception not throwed');
    }

    public function testGeneratePaypalUrl()
    {
        $this->assertEquals(
            'http://paypalUrl/789456123/aaa',
            $this->buildService('123', 'pwd', 'azer', false, 'endPointFake', 'http://paypalUrl/{token}/aaa')
                ->generatePaypalUrl('789456123')
        );
    }
}