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
 * to richarddeloge@gmail . com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo . software/paypal Project website
 *
 * @license     http://teknoo . software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail . com>
 */

declare(strict_types=1);

namespace Teknoo\Paypal\Express\Service;

/**
 * Class to manipulate result.
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail . com)
 *
 * @link        http://teknoo . software/paypal Project website
 *
 * @license     http://teknoo . software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail . com>
 */
class TransactionResult implements TransactionResultInterface
{
    /**
     * @var array<string, string>
     */
    private array $values;

    /**
     * @param array<string, string> $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function getAckValue(): string
    {
        if (isset($this->values['ACK'])) {
            return $this->values['ACK'];
        }

        throw new \RuntimeException('Error, the ACK value is not available in the response');
    }

    public function isSuccessful(): bool
    {
        $ack = \strtoupper($this->getAckValue());
        return ('SUCCESS' === $ack || 'SUCCESSWITHWARNING' === $ack);
    }

    public function getTokenValue(): string
    {
        if (isset($this->values['TOKEN'])) {
            return $this->values['TOKEN'];
        }

        throw new \RuntimeException('Error, the TOKEN value is not available in the response');
    }

    public function getPayerIdValue(): string
    {
        if (isset($this->values['PAYERID'])) {
            return $this->values['PAYERID'];
        }

        throw new \RuntimeException('Error, the PAYERID value is not available in the response');
    }

    public function getTimestampValue(): string
    {
        if (isset($this->values['TIMESTAMP'])) {
            return $this->values['TIMESTAMP'];
        }

        throw new \RuntimeException('Error, the TIMESTAMP value is not available in the response');
    }

    public function getCorrelationIdValue(): string
    {
        if (isset($this->values['CORRELATIONID'])) {
            return $this->values['CORRELATIONID'];
        }

        throw new \RuntimeException('Error, the CORRELATIONID value is not available in the response');
    }

    public function getVersionValue(): string
    {
        if (isset($this->values['VERSION'])) {
            return $this->values['VERSION'];
        }

        throw new \RuntimeException('Error, the VERSION value is not available in the response');
    }

    public function getBuildValue(): string
    {
        if (isset($this->values['BUILD'])) {
            return $this->values['BUILD'];
        }

        throw new \RuntimeException('Error, the BUILD value is not available in the response');
    }

    /**
     * @return array<ErrorInterface>
     */
    public function getErrors(): array
    {
        $errorList = [];

        $lineIndex = 0;
        while (isset($this->values['L_ERRORCODE' . $lineIndex])) {
            $errorList[] = new Error(
                (int) $this->values['L_ERRORCODE' . $lineIndex],
                $this->values['L_SHORTMESSAGE' . $lineIndex],
                $this->values['L_LONGMESSAGE' . $lineIndex],
                $this->values['L_SEVERITYCODE' . $lineIndex]
            );

            ++$lineIndex;
        }

        return $errorList;
    }

    /**
     * Return raw value from the request .
     *
     * @return array<string, mixed>
     */
    public function getRawValues(): array
    {
        return $this->values;
    }
}
