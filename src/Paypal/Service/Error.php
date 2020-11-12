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
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\Paypal\Express\Service;

/**
 * To manipulate errors from paypal.
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Error implements ErrorInterface
{
    private int $code;

    private string $shortMessage;

    private string $longMessage;

    private string $severity;

    public function __construct(int $code, string $shortMessage, string $longMessage, string $severity)
    {
        $this->code = $code;
        $this->shortMessage = $shortMessage;
        $this->longMessage = $longMessage;
        $this->severity = $severity;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getShortMessage(): string
    {
        return $this->shortMessage;
    }

    public function getLongMessage(): string
    {
        return $this->longMessage;
    }

    public function getSeverity(): string
    {
        return $this->severity;
    }
}
