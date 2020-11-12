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
 * Class to allow developer to pass arguments for request.
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
class ArgumentBag implements ArgumentBagInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $parameters = [];

    private int $purchaseItemCounter = 0;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function reset(): ArgumentBagInterface
    {
        $this->parameters = [];

        return $this;
    }

    /**
     * @param mixed $value
     * throws \InvalidArgumentException when $name is not a string
     */
    public function set(string $name, $value): ArgumentBagInterface
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * @throws \InvalidArgumentException when $name is not a string
     * @return mixed
     */
    public function get(string $name)
    {
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }

        throw new \RuntimeException("Error, the required parameter $name is not defined");
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->parameters;
    }

    private function increasePurchaseItemCounter(): ArgumentBagInterface
    {
        ++$this->purchaseItemCounter;

        return $this;
    }

    public function addItem(PurchaseItemInterface $purchaseItem): ArgumentBagInterface
    {
        $purchaseItemCounter = $this->purchaseItemCounter;

        $this->set('L_PAYMENTREQUEST_0_NAME' . $purchaseItemCounter, $purchaseItem->getName());
        $this->set('L_PAYMENTREQUEST_0_DESC' . $purchaseItemCounter, $purchaseItem->getDescription());
        $this->set(
            'L_PAYMENTREQUEST_0_AMT' . $purchaseItemCounter,
            \number_format($purchaseItem->getAmount(), 2, '.', ',')
        );
        $this->set(
            'L_PAYMENTREQUEST_0_QTY' . $purchaseItemCounter,
            \number_format($purchaseItem->getQantity(), 2, '.', ', ')
        );
        $this->set('L_PAYMENTREQUEST_0_NUMBER' . $purchaseItemCounter, $purchaseItem->getReference());
        $this->set('L_PAYMENTREQUEST_0_ITEMURL' . $purchaseItemCounter, $purchaseItem->getRequestUrl());
        $this->set(
            'L_PAYMENTREQUEST_0_ITEMCATEGORY' . $purchaseItemCounter,
            $purchaseItem->getItemCategory()
        );

        $this->increasePurchaseItemCounter();

        return $this;
    }
}
