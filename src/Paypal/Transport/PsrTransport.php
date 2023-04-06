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
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Paypal\Express\Transport;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use function http_build_query;
use function parse_str;
use function urldecode;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class PsrTransport implements TransportInterface
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly UriFactoryInterface $uriFactory,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly string $apiEndPoint,
        private readonly string $paypalVersion,
        private readonly string $user,
        private readonly string $password,
        private readonly string $signature,
        private readonly string $bNCode,
    ) {
    }

    public function call(string $methodName, ArgumentBagInterface $arguments): array
    {
        $uri = $this->uriFactory->createUri($this->apiEndPoint);
        $request = $this->requestFactory->createRequest('POST', $uri);

        $postFields = $arguments->toArray();
        $postFields['METHOD'] = $methodName;
        $postFields['VERSION'] = $this->paypalVersion;
        $postFields['PWD'] = $this->password;
        $postFields['USER'] = $this->user;
        $postFields['SIGNATURE'] = $this->signature;
        $postFields['BUTTONSOURCE'] = $this->bNCode;

        //setting the nvpreq as POST FIELD to curl
        $stream = $this->streamFactory->createStream(http_build_query($postFields));
        $request = $request->withBody($stream);

        //getting response from server
        $response = $this->client->sendRequest($request);

        //converting request response to an Associative Array
        $resultArray = [];
        parse_str(urldecode((string) $response->getBody()), $resultArray);

        return $resultArray;
    }
}
