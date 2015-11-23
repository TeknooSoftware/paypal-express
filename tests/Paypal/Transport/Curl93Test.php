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
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @license     http://teknoo.software/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @version     0.8.3
 */

namespace Teknoo\tests\Paypal\Transport;

use Teknoo\Paypal\Express\Transport\ArgumentBag;
use Teknoo\Paypal\Express\Transport\Curl93;
use Teknoo\Curl\RequestGenerator;

/**
 * Class Curl93Test.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @license     http://teknoo.software/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Curl93Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RequestGenerator
     */
    protected $requestGeneratorService;

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestGenerator
     */
    protected function buildRequestGeneratorMock()
    {
        if (!$this->requestGeneratorService instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->requestGeneratorService = $this->getMock(
                'Teknoo\Curl\RequestGenerator',
                array(),
                array(),
                '',
                false
            );
        }

        return $this->requestGeneratorService;
    }

    /**
     * @param string|null           $userId
     * @param string|null           $password
     * @param string|null           $signature
     * @param string|null           $apiEndPoint
     * @param string|null           $paypalUrl
     * @param int|null              $paypalVersion
     * @param string|null           $bNCode
     * @param int|null              $apiTimeout
     * @param RequestGenerator|null $requestGenerator
     *
     * @return Curl93
     */
    public function buildService(
        $userId = null,
        $password = null,
        $signature = null,
        $apiEndPoint = null,
        $paypalUrl = null,
        $paypalVersion = 93,
        $bNCode = 'PP-ECWizard',
        $apiTimeout = 60,
        $requestGenerator = null
    ) {
        return new Curl93($userId, $password, $signature, $apiEndPoint, $paypalUrl, $paypalVersion, $bNCode, $apiTimeout, $requestGenerator);
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::__construct()
     */
    public function testConstructor()
    {
        $this->assertInstanceOf('Teknoo\Paypal\Express\Transport\Curl93', $this->buildService());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::getUserId()
     */
    public function testGetUserId()
    {
        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $this->assertEquals('uId', $service->getUserId());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::setUserId()
     */
    public function testSetUserId()
    {
        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $service->setUserId('newUID');
        $this->assertEquals('newUID', $service->getUserId());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::getPassword()
     */
    public function testGetPassword()
    {
        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $this->assertEquals('pwd', $service->getPassword());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::setPassword()
     */
    public function testSetPassword()
    {
        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $service->setPassword('newPwd');
        $this->assertEquals('newPwd', $service->getPassword());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::getSignature()
     */
    public function testGetSignature()
    {
        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $this->assertEquals('sgnt', $service->getSignature());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::setSignature()
     */
    public function testSetSignature()
    {
        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $service->setSignature('newSgnt');
        $this->assertEquals('newSgnt', $service->getSignature());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::getApiEndPoint()
     */
    public function testGetApiEndPoint()
    {
        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $this->assertEquals('https://teknoo.software', $service->getApiEndPoint());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::setApiEndPoint()
     */
    public function testSetApiEndPoint()
    {
        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $service->setApiEndPoint('https://teknoo.software/new');
        $this->assertEquals('https://teknoo.software/new', $service->getApiEndPoint());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::getPaypalUrl()
     */
    public function testGetPaypalUrl()
    {
        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $this->assertEquals('https://paypal.com', $service->getPaypalUrl());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::setPaypalUrl()
     */
    public function testSetPaypalUrl()
    {
        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $service->setPaypalUrl('https://paypal.com/new');
        $this->assertEquals('https://paypal.com/new', $service->getPaypalUrl());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::getPaypalApiVersion()
     */
    public function testGetPaypalApiVersion()
    {
        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $this->assertEquals(93, $service->getPaypalApiVersion());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::getApiTimeout()
     */
    public function testGetApiTimeout()
    {
        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $this->assertEquals(120, $service->getApiTimeout());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::setApiTimeout()
     */
    public function testSetApiTimeout()
    {
        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $service->setApiTimeout(230);
        $this->assertEquals(230, $service->getApiTimeout());
    }

    /**
     * @covers Teknoo\Paypal\Express\Transport\Curl93::call()
     */
    public function testCall()
    {
        $requestMock = $this->getMock(
            'Teknoo\Curl\Request',
            array(),
            array(),
            '',
            false
        );

        $curlMock = $this->buildRequestGeneratorMock();
        $curlMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($requestMock);

        $requestMock->expects($this->any())
            ->method('setMethod')
            ->with('POST');

        $exceptedBody = http_build_query(
            array(
                'bar' => 'foo',
                '123' => '456',
                'METHOD' => 'methodToCall',
                'VERSION' => 93,
                'PWD' => 'pwd',
                'USER' => 'uId',
                'SIGNATURE' => 'sgnt',
                'BUTTONSOURCE' => 'bnc',
            )
        );

        $requestMock->expects($this->any())
            ->method('setOption')
            ->withConsecutive(
                [CURLOPT_URL, 'https://teknoo.software'],
                [CURLOPT_VERBOSE, true],
                [CURLOPT_SSL_VERIFYPEER, false],
                [CURLOPT_SSL_VERIFYHOST, 0],
                [CURLOPT_RETURNTRANSFER, true],
                [CURLOPT_POST, true],
                [CURLOPT_TIMEOUT, 1200],
                [CURLOPT_CONNECTTIMEOUT, 120],
                [CURLOPT_POSTFIELDS, $exceptedBody]
            );

        $requestMock->expects($this->once())
            ->method('execute')
            ->willReturn(
                urlencode(
                    http_build_query(
                        array(
                            'foo' => 'bar',
                            'hello' => 'world',
                        )
                    )
                )
            );

        $arguments = new ArgumentBag(['bar' => 'foo', '123' => 456]);

        $service = $this->buildService('uId', 'pwd', 'sgnt', 'https://teknoo.software', 'https://paypal.com', 93, 'bnc', 120, $this->buildRequestGeneratorMock());
        $result = $service->call('methodToCall', $arguments);

        $this->assertInstanceOf('\ArrayObject', $result);
        $this->assertEquals(['foo' => 'bar', 'hello' => 'world'], $result->getArrayCopy());
    }
}
