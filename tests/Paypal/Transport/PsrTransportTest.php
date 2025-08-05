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

namespace Teknoo\Tests\Paypal\Transport;

use PHPUnit\Framework\Attributes\CoversClass;
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
#[CoversClass(PsrTransport::class)]
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

    public function testCall(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $request->method('withBody')->willReturnSelf();
        $stream = $this->createMock(StreamInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $stream = new class implements StreamInterface {
            public function __toString(): string
            {
                return \urlencode('first=value&arr[]=foo+bar&arr[]=baz');
            }

            public function close(): void
            {
                // TODO: Implement close() method.
            }

            public function detach(): void
            {
                // TODO: Implement detach() method.
            }

            public function getSize(): ?int
            {
                // TODO: Implement getSize() method.
            }

            public function tell(): int
            {
                // TODO: Implement tell() method.
            }

            public function eof(): bool
            {
                // TODO: Implement eof() method.
            }

            public function isSeekable(): bool
            {
                // TODO: Implement isSeekable() method.
            }

            public function seek(int $offset, int $whence = SEEK_SET): void
            {
                // TODO: Implement seek() method.
            }

            public function rewind(): void
            {
                // TODO: Implement rewind() method.
            }

            public function isWritable(): bool
            {
                return false;
            }

            public function write(string $string): int
            {
                // TODO: Implement write() method.
            }

            public function isReadable(): bool
            {
                return true;
            }

            public function read(int $length): string
            {
                return \urlencode('first=value&arr[]=foo+bar&arr[]=baz');
            }

            public function getContents(): string
            {
                return \urlencode('first=value&arr[]=foo+bar&arr[]=baz');
            }

            public function getMetadata(?string $key = null): void
            {
                // TODO: Implement getMetadata() method.
            }
        };

        $response->method('getBody')->willReturn(
            $stream,
        );

        $this->getUriFactoryMock()
            ->method('createUri')
            ->willReturn($uri);

        $this->getRequestFactoryMock()
            ->method('createRequest')
            ->with('POST', $uri)
            ->willReturn($request);

        $this->getStreamFactoryMock()
            ->method('createStream')
            ->willReturn($stream);

        $this->getClientMock()
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $this->assertEquals([
            'first' => 'value',
            'arr' => [
                'foo bar',
                'baz'
            ]
        ], $this->buildTransport()->call(
            'foo',
            $this->createMock(ArgumentBagInterface::class)
        ));
    }
}
