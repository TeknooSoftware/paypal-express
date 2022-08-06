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
 *
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @version     0.8.3
 */
namespace Acme\demo;

use Teknoo\Paypal\Express\Contract\ConsumerInterface;
use Teknoo\Paypal\Express\Contract\PurchaseInterface;
use Teknoo\Paypal\Express\Contract\PurchaseItemInterface;
use Teknoo\Paypal\Express\Transport\ArgumentBag;
use Teknoo\Paypal\Express\Transport\ArgumentBagInterface;

/**
 * Class Purchase
 * Demo business class representing a purchase.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/paypal-express Project website
 *
 * @license     http://teknoo.software/paypal/license/mit         MIT License
 *
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Purchase implements PurchaseInterface
{
    protected string $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getAmount(): float
    {
        return 314.15;
    }

    public function getPaymentAction(): string
    {
        return 'SALE';
    }

    public function getReturnUrl(): string
    {
        return $this->baseUrl.'?method=return';
    }

    public function getCancelUrl(): string
    {
        return $this->baseUrl.'?method=cancel';
    }

    public function getCurrencyCode(): string
    {
        return 'EUR';
    }

    public function getConsumer(): ConsumerInterface
    {
        return new Consumer();
    }

    public function configureArgumentBag(ArgumentBagInterface $argumentBag): PurchaseInterface
    {
        $argumentBag->addItem(
            new class implements PurchaseItemInterface {
                public function getName(): string
                {
                    return 'Acme Item';
                }

                public function getDescription(): string
                {
                    return 'Acme Item Desc';
                }

                public function getAmount(): float
                {
                    return 123.0;
                }

                public function getQantity(): int
                {
                    return 1;
                }

                public function getReference(): string
                {
                    return 'foo';
                }

                public function getRequestUrl(): string
                {
                    return '';
                }

                public function getItemCategory(): string
                {
                    return 'Digital';
                }

            }
        );

        $argumentBag->set('PAYMENTREQUEST_0_ITEMAMT', '123.00');
        $argumentBag->set('PAYMENTREQUEST_0_AMT', '123.00');
        $argumentBag->set('PAYMENTREQUEST_0_INVNUM', 'B'.\date('Ymd'));
        $argumentBag->set('PAYMENTREQUEST_0_NOTETEXT', 'Acme Purchase');
        $argumentBag->set('PAYMENTREQUEST_0_DESC', 'Acme Purchase');
        $argumentBag->set('PAYMENTREQUEST_0_CUSTOM', 'Acme Purchase');

        return $this;
    }
}
