<?php

/*
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
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\Paypal\Express\Transport;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use function parse_str;
use function urldecode;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class PsrTransport implements TransportInterface
{
    private ClientInterface $client;

    private UriFactoryInterface $uriFactory;

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    private string $apiEndPoint;

    private string $paypalVersion;

    private string $password;

    private string $user;

    private string $signature;

    private string $bNCode;

    public function __construct(
        ClientInterface $client,
        UriFactoryInterface $uriFactory,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        string $apiEndPoint,
        string $paypalVersion,
        string $user,
        string $password,
        string $signature,
        string $bNCode
    ) {
        $this->client = $client;
        $this->uriFactory = $uriFactory;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->apiEndPoint = $apiEndPoint;
        $this->paypalVersion = $paypalVersion;
        $this->password = $password;
        $this->user = $user;
        $this->signature = $signature;
        $this->bNCode = $bNCode;
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
        $stream = $this->streamFactory->createStream(\http_build_query($postFields));
        $request = $request->withBody($stream);

        //getting response from server
        $response = $this->client->sendRequest($request);

        //converting request response to an Associative Array
        $resultArray = array();
        parse_str(urldecode((string) $response->getBody()), $resultArray);

        return $resultArray;
    }
}
