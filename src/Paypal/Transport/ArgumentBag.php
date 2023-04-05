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

use Teknoo\Paypal\Express\Contracts\PurchaseItemInterface;
use Teknoo\Paypal\Express\Service\Exception\MissingParameterException;

/**
 * Class to allow developer to pass arguments for request.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class ArgumentBag implements ArgumentBagInterface
{
    private int $purchaseItemCounter = 0;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private array $parameters = []
    ) {
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

        throw new MissingParameterException("Error, the required parameter $name is not defined");
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
