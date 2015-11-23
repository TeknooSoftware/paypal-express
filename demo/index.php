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

namespace Acme\Demo;

use Teknoo\Paypal\Express\Service\ExpressCheckout;
use Teknoo\Paypal\Express\Transport\Curl93;
use Teknoo\Curl\RequestGenerator;

//Initialize composer
require_once '../vendor/autoload.php';
require_once 'Consumer.php';
require_once 'Purchase.php';

try {

    //Initialize Paypal library

    //Request generator to communicate with paypal via curl
    $requestGenerator = new RequestGenerator();

    //Transport object to communicate with curl
    $transport = new Curl93(
        '',
        '',
        '',
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
        if ('cancel' == $_GET['method']): ?>
            <p>Checkout canceled by the consumer</p>
        <?php else:
            $result = $service->getTransactionResult($_GET['token']);
    if ($result->isSuccessful()) {
        $confirmationResult = $service->confirmTransaction($_GET['token'], $result->getPayerIdValue(), $purchase);
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
    echo 'Error : '.$e->getMessage();
}
