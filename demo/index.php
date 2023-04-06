<?php

declare(strict_types=1);

/**
 * Paypal Express.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * that are bundled with this package in the folder licences
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
 *
 * @author      Richard Déloge <richard@teknoo.software>
 */
namespace Acme\Demo;

use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Teknoo\Paypal\Express\Service\ExpressCheckout;
use Teknoo\Paypal\Express\Transport\PsrTransport;

//Initialize composer
require_once '../vendor/autoload.php';
require_once 'Consumer.php';
require_once 'Purchase.php';

try {

    //Initialize Paypal library

    //Transport object to communicate with curl
    $transport = new PsrTransport(
        HttpClientDiscovery::find(),
        Psr17FactoryDiscovery::findUrlFactory(),
        Psr17FactoryDiscovery::findRequestFactory(),
        Psr17FactoryDiscovery::findStreamFactory(),
        'https://api-3t.sandbox.paypal.com/nvp',
        '93',
        '',
        '',
        '',
        'PP-ECWizard'
    );

    //Paypal service
    $service = new ExpressCheckout(
        $transport,
        'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token={token}'
    );

    //Prepare demo purchase
    $purchase = new Purchase('http://localhost:'.$_SERVER['SERVER_PORT'].'/index.php');

    //To avoid bad html generation on exception
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <title>Teknoo Software - Paypal Express - Demo</title>
    </head>
    <body>
    <h1>Teknoo Software, Paypal Express library demo</h1>
    <?php if (isset($_GET['method'])):
        if ('cancel' === $_GET['method']): ?>
            <p>Checkout canceled by the consumer</p>
        <?php else:
            $result = $service->getTransactionResult($_GET['token']);
            if ($result->isSuccessful()) {
                $confirmationResult = $service->confirmTransaction(
                        $_GET['token'],
                        $result->getPayerIdValue(),
                        $purchase
                );
                if ($confirmationResult->isSuccessful()) {
                    echo '<p>Checkout successful</p>';
                } else {
                    $errors = $confirmationResult->getErrors();
                    foreach ($errors as $error) {
                        echo '<p>'.$error->getShortMessage().' : '.$error->getLongMessage().'</p>';
                    }
                }
            } else {
                echo '<p>Error from Paypal</p>';
            }
        endif; else: ?>
        <p>
            <a href="<?php echo $service->prepareTransaction($purchase);
            ?>">Process to checkout to paypal</a>
        </p>
    <?php endif;
    ?>
    </body>
    </html>
    <?php
    ob_end_flush();
} catch (\Exception $e) {
    ob_end_clean();
    print 'Error : '.$e->getMessage().'<br/>';
    print $e->getTraceAsString().'<br/>';
    print $e->getPrevious().'<br/>';
}
