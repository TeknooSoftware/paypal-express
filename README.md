Teknoo Software - Paypal Express library
===================================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/ad93fc55-8404-417c-b2bc-87342262d8a3/mini.png)](https://insight.sensiolabs.com/projects/ad93fc55-8404-417c-b2bc-87342262d8a3) [![Build Status](https://travis-ci.org/TeknooSoftware/paypal-express.svg?branch=master)](https://travis-ci.org/TeknooSoftware/paypal-express)

This library allows you to integrate quickly and easily the service "Paypal Express Checkout" in your website.

Simple example
--------------

    //Request generator to communicate with paypal via curl
    $requestGenerator = new Teknoo\Curl\RequestGenerator();

    //Transport object to communicate with curl
    $transport = new Teknoo\Paypal\Express\Transport\Curl93(
        'User Id from Paypal',
        'Password from Paypal',
        'Signature from Paypal',
        'https://api-3t.sandbox.paypal.com/nvp',
        'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token={token}',
        93,
        'PP-ECWizard',
        60,
        $requestGenerator
    );
    
    //Api client
    $service = new ExpressCheckout($transport);
    
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

Installation & Requirements
---------------------------
To install this library with composer, run this command :

    composer require teknoo/paypal-exprss

This library requires :

    * PHP 5.4+
    * Teknoo Software Curl Request library

Example
-------
An example of using this library is available in the folder : [Demo](demo/index.php).

API Documentation
-----------------
Generated documentation from the library with PhpDocumentor : [Open](https://cdn.rawgit.com/TeknooSoftware/paypal-express/master/docs/api/index.html).

Documentation and how-to
------------------------
Documentation to explain how this library works and how use it : [Behavior](docs/documentation.md).

Credits
-------
Richard Déloge - <richarddeloge@gmail.com> - Lead developer.
Teknoo Software - <http://teknoo.software>

About Teknoo Software
---------------------
**Teknoo Software** is a PHP software editor, founded by Richard Déloge. 
Teknoo Software's DNA is simple : Provide to our partners and to the community a set of high quality services or software,
 sharing knowledge and skills.$
 
License
-------
States is licensed under the MIT and GPL3+ Licenses - see the licenses folder for details

Contribute :)
-------------
You are welcome to contribute to this project. [Fork it on Github](CONTRIBUTING.md)


