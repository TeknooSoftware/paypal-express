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

namespace Teknoo\Paypal\Express\Service;

/**
 * To manipulate errors from paypal.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class Error implements ErrorInterface
{
    public function __construct(
        private readonly int $code,
        private readonly string $shortMessage,
        private readonly string $longMessage,
        private readonly string $severity
    ) {
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
