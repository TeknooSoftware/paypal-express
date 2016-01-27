<?php

/**
 * Paypal Express.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @license     http://teknoo.software/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @version     0.8.3
 */

namespace Teknoo\Paypal\Express\Transport;

use Teknoo\Paypal\Express\Entity\PurchaseItemInterface;

/**
 * Class ArgumentBag
 * Class to allow developer to pass arguments for request.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/paypal Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 * @license     http://teknoo.software/paypal/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class ArgumentBag implements ArgumentBagInterface
{
    /**
     * @var \ArrayObject
     */
    protected $parameters;

    /**
     * To count all purchase item added with addItem()
     * @var int
     */
    protected $purchaseItemCounter=0;

    /**
     * To initialize this bag.
     */
    public function __construct($parameters = null)
    {
        $this->reset();
        if (!empty($parameters)) {
            $this->parameters->exchangeArray($parameters);
        }
    }

    /**
     * Reset this bag.
     *
     * @return $this
     */
    public function reset()
    {
        $this->parameters = new \ArrayObject();

        return $this;
    }

    /**
     * Define an argument in the bag.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     *
     * @throws \InvalidArgumentException when $name is not a string
     */
    public function set($name, $value)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('The name is not a string');
        }

        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * Return an argument defined in the bag.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException when $name is not a string
     */
    public function get($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('The name is not a string');
        }

        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }

        throw new \RuntimeException(sprintf('Error, the required parameter %s is not defined', $name));
    }

    /**
     * Return the list of argument as an array object.
     *
     * @return \ArrayAccess|\Countable
     */
    public function toArray()
    {
        return $this->parameters;
    }

    /**
     * To increase the purchase coutner for the next add
     * @return self
     */
    private function increasePurchaseItemCounter()
    {
        $this->purchaseItemCounter++;

        return $this;
    }

    /**
     * @param PurchaseItemInterface $purchaseItem
     * @return self
     */
    public function addItem(PurchaseItemInterface $purchaseItem)
    {
        $purchaseItemCounterValue = $this->purchaseItemCounter;

        $this->set('L_PAYMENTREQUEST_0_NAME'.$purchaseItemCounterValue, $purchaseItem->getPaymentRequestName());
        $this->set('L_PAYMENTREQUEST_0_DESC'.$purchaseItemCounterValue, $purchaseItem->getPaymentRequestDesc());
        $this->set('L_PAYMENTREQUEST_0_AMT'.$purchaseItemCounterValue, $purchaseItem->getPaymentRequestAmount());
        $this->set('L_PAYMENTREQUEST_0_NUMBER'.$purchaseItemCounterValue, $purchaseItem->getPaymentRequestNumber());
        $this->set('L_PAYMENTREQUEST_0_ITEMURL'.$purchaseItemCounterValue, $purchaseItem->getPaymentRequestUrl());

        $this->increasePurchaseItemCounter();

        return $this;
    }
}
