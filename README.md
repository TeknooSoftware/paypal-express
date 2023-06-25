Teknoo Software - Paypal Express library
===================================

This library allows you to integrate quickly and easily the service "Paypal Express Checkout", using NVP, in your website.

This library is deprecated, please consider the official [Paypal PHP SDK](https://paypal.github.io/PayPal-PHP-SDK/).

Quick Example
-------------
    <?php
    
    declare(strict_types=1);
    
    require_once 'vendor/autoload.php';
    
    use Http\Discovery\HttpClientDiscovery;
    use Http\Discovery\Psr17FactoryDiscovery;
    use Teknoo\Paypal\Express\Service\ExpressCheckout;
    use Teknoo\Paypal\Express\Transport\PsrTransport;
    
    //Initialize Paypal library
    
    //Transport object to communicate with curl
    $transport = new PsrTransport(
        HttpClientDiscovery::find(),
        Psr17FactoryDiscovery::findUrlFactory(),
        Psr17FactoryDiscovery::findRequestFactory(),
        Psr17FactoryDiscovery::findStreamFactory(),
        'https://api-3t.sandbox.paypal.com/nvp',
        '93',
        'user id',
        'password',
        'paypal signature',
        'PP-ECWizard'
    );
    
    //Paypal service
    $service = new ExpressCheckout(
        $transport,
        'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token={token}'
    );
    
    //Prepare demo purchase
    $purchase = new class implementing Teknoo\Paypal\Express\Entity\PurchaseInterface {
    // ...
    };
    
    //In your html, purchase is an custom object implementing the interface PurchaseInterface
    <a href="<?php echo $service->prepareTransaction($purchase); ?>">Process to checkout to paypal</a>
    
    //On the result page
    $result = $service->getTransactionResult($_GET['token']);
    if ($result->isSuccessful()) {
        /* ... */
    } else {
        $errors = $result->getErrors();
    }

Support this project
---------------------
This project is free and will remain free. It is fully supported by the activities of the EIRL.
If you like it and help me maintain it and evolve it, don't hesitate to support me on
[Patreon](https://patreon.com/teknoo_software) or [Github](https://github.com/sponsors/TeknooSoftware).

Thanks :) Richard.

Credits
-------
EIRL Richard Déloge - <https://deloge.io> - Lead developer.
SASU Teknoo Software - <https://teknoo.software>

About Teknoo Software
---------------------
**Teknoo Software** is a PHP software editor, founded by Richard Déloge, as part of EIRL Richard Déloge.
Teknoo Software's goals : Provide to our partners and to the community a set of high quality services or software,
sharing knowledge and skills.

License
-------
This library is licensed under the MIT License - see the licenses folder for details.

Installation & Requirements
---------------------------
To install this library with composer, run this command :

    composer require teknoo/paypal-exprss

This library requires :

    * PHP 8.1+
    * A psr/http-message implementation (PSR 7)
    * A psr/http-factory implementation (PSR 17)

Example
-------
An example of using this library is available in the folder : [Demo](demo/index.php).

Contribute :)
-------------
You are welcome to contribute to this project. [Fork it on Github](CONTRIBUTING.md)


