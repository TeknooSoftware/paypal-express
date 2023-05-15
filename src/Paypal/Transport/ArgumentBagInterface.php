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
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Paypal\Express\Transport;

use Teknoo\Paypal\Express\Contracts\PurchaseItemInterface;

/**
 * Interface to define arguments container for all request to the api.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
interface ArgumentBagInterface
{
    /*
     * Reset this bag.
     */
    public function reset(): ArgumentBagInterface;

    /**
     * Define an argument in the bag.
     *
     * @param mixed $value
     *
     * @throws \InvalidArgumentException when $name is not a string
     */
    public function set(string $name, $value): ArgumentBagInterface;

    /**
     * Return an argument defined in the bag.
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException when $name is not a string
     */
    public function get(string $name);

    /**
     * Return the list of argument as an array object.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    public function addItem(PurchaseItemInterface $purchaseItem): ArgumentBagInterface;
}
