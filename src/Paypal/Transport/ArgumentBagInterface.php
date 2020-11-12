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

namespace Teknoo\Paypal\Express\Transport;

use Teknoo\Paypal\Express\Contract\PurchaseItemInterface;

/**
 * Interface to define arguments container for all request to the api.
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
interface ArgumentBagInterface
{
    /**
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
