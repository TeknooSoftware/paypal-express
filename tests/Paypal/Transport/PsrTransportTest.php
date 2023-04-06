<?php

/*
 * Paypal Express.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * it is available in LICENSE file at the root of this package
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
 *
 * @author      Richard Déloge <richard@teknoo.software>
 *
 *
 */

declare(strict_types=1);

namespace Teknoo\Tests\Paypal\Transport;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Teknoo\Paypal\Express\Transport\ArgumentBagInterface;
use Teknoo\Paypal\Express\Transport\PsrTransport;
use Teknoo\Paypal\Express\Transport\TransportInterface;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 *
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @covers \Teknoo\Paypal\Express\Transport\PsrTransport
 */
class PsrTransportTest extends TestCase
{
    private ?ClientInterface $client = null;

    private ?UriFactoryInterface $uriFactory = null;

    private ?RequestFactoryInterface $requestFactory = null;

    private ?StreamFactoryInterface $streamFactory = null;

    /**
     * @return MockObject|ClientInterface
     */
    private function getClientMock(): ClientInterface
    {
        if (!$this->client instanceof MockObject) {
            $this->client = $this->createMock(ClientInterface::class);
        }

        return $this->client;
    }

    /**
     * @return MockObject|UriFactoryInterface
     */
    private function getUriFactoryMock(): UriFactoryInterface
    {
        if (!$this->uriFactory instanceof MockObject) {
            $this->uriFactory = $this->createMock(UriFactoryInterface::class);
        }

        return $this->uriFactory;
    }

    /**
     * @return MockObject|RequestFactoryInterface
     */
    private function getRequestFactoryMock(): RequestFactoryInterface
    {
        if (!$this->requestFactory instanceof MockObject) {
            $this->requestFactory = $this->createMock(RequestFactoryInterface::class);
        }

        return $this->requestFactory;
    }

    /**
     * @return MockObject|StreamFactoryInterface
     */
    private function getStreamFactoryMock(): StreamFactoryInterface
    {
        if (!$this->streamFactory instanceof MockObject) {
            $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
        }

        return $this->streamFactory;
    }

    public function buildTransport(): PsrTransport
    {
        return new PsrTransport(
            $this->getClientMock(),
            $this->getUriFactoryMock(),
            $this->getRequestFactoryMock(),
            $this->getStreamFactoryMock(),
            'https://paypalUrl',
            '123',
            'password',
            'foo',
            'bar',
            'bnCode'
        );
    }

    public function testCall()
    {
        $uri = $this->createMock(UriInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $request->expects(self::any())->method('withBody')->willReturnSelf();
        $stream = $this->createMock(StreamInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $response->expects(self::any())->method('getBody')->willReturn(
            \urlencode('first=value&arr[]=foo+bar&arr[]=baz')
        );

        $this->getUriFactoryMock()
            ->expects(self::any())
            ->method('createUri')
            ->willReturn($uri);

        $this->getRequestFactoryMock()
            ->expects(self::any())
            ->method('createRequest')
            ->with('POST', $uri)
            ->willReturn($request);

        $this->getStreamFactoryMock()
            ->expects(self::any())
            ->method('createStream')
            ->willReturn($stream);

        $this->getClientMock()
            ->expects(self::any())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        self::assertEquals(
            [
                'first' => 'value',
                'arr' => [
                    'foo bar',
                    'baz'
                ]
            ],
            $this->buildTransport()->call(
                'foo',
                $this->createMock(ArgumentBagInterface::class)
            )
        );
    }
}
