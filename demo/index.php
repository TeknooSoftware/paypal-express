<?php

namespace Acme\Demo;

use UniAlteri\Paypal\Express\Service\ExpressCheckout;
use UniAlteri\Paypal\Express\Transport\Curl93;
use Zeroem\CurlBundle\Curl\RequestGenerator;

//Initialize composer
require_once '../vendor/autoload.php';
require_once 'Consumer.php';
require_once 'Purchase.php';

//Initialize Paypal library

//Request generator to communicate with paypal via curl
$requestGenerator = new RequestGenerator();

//Transport object to communicate with curl
$transport = new Curl93(
    'm.quintin-facilitator_api1.uni-alteri.com',
    'BS9H393XSBBDJGBH',
    'A7S1I-wpe.pNYb13N3WkZw3sdVyLAxXHG0.Tgcy-D0bOY4a4YD-CVQcP',
    'https://api-3t.sandbox.paypal.com/nvp',
    'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token={token}',
    93,
    'PP-ECWizard',
    60,
    $requestGenerator
);

//Paypal service
$service = new ExpressCheckout($transport);

//Prepare demo purchase
$purchase = new Purchase('http://localhost:'.$_SERVER['SERVER_PORT']);
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Uni Alteri - Paypal Express - Demo</title>
    </head>
    <body>
        <h1>Uni Alteri, Paypal Express library demo</h1>
        <?php if (isset($_GET['method'])):
            if ('cancel' == $_GET['method']): ?>
                <p>Checkout canceled by the consumer</p>
            <?php else:
                $result = $service->getTransactionResult($_GET['token']);
                if ($result->isSuccessful()) {
                    $confirmationResult = $service->confirmTransaction($_GET['token'], $result->getPayerIdValue(), $purchase);
                    if ($confirmationResult->isSuccessful()) {
                        echo '<p>Checkout successful</p>';
                    } else {
                        echo '<p>Checkout cancelled</p>';
                    }
                } else {
                    echo '<p>Error from Paypal</p>';
                }
            endif;
        else: ?>
            <p>
                <a href="<?php echo $service->prepareTransaction($purchase); ?>">Process to checkout to paypal</a>
            </p>
        <?php endif; ?>
    </body>
</html>
