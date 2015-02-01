#Uni Alteri - Paypal Express library - Documentation

##Presentation

This library is build arround two components :

*   a transport, implementing `UniAlteri\Paypal\Express\Transport\TransportInterface` to  execute request to the Paypal API
*   a service, implementing `UniAlteri\Paypal\Express\Service\ServiceInterface` to prepare and perform call to the Paypal API.

Two defaults implementations are provided :

*   `UniAlteri\Paypal\Express\Transport\Curl93` a transport, using curl via the library `zeroem/curl-bundle` and the Paypal API NVP under its version 93.
*   `UniAlteri\Paypal\Express\Service\ExpressCheckout` a service to use Express Checkout Paypal service.

All Paypal returns are encapsulated in an object implementing `UniAlteri\Paypal\Express\Service\TransactionResultInterface`.

##Quick Startup

###Get Paypal credentials

Before you must create a Seller account on Paypal : https://www.paypal.com/webapps/mpp/merchant

To get sandbox credentials, go to https://developer.paypal.com/ then in the dashboard and "Sandbox account" 
( or directly, click here https://developer.paypal.com/webapps/developer/applications/accounts )

Paypal's credentials are available in your profile, tab "API Credentials"

https://developer.paypal.com/docs/classic/lifecycle/sb_credentials/

###Configuration

You must instantiate the transport object like this :

    use UniAlteri\Paypal\Express\Transport\Curl93;
    use Zeroem\CurlBundle\Curl\RequestGenerator;
    
    //Request generator to communicate with paypal via curl
    $requestGenerator = new RequestGenerator();

    //Transport object to communicate with curl
    $transport = new Curl93(
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
    
Now you must instantiate your service like this :
    
    //Paypal service
    $service = new ExpressCheckout($transport);

###Entities interfaces

Objects passed to the service to perform the paypal checkout must implement some interface :

*   The consumer object must implement the interface `UniAlteri\Paypal\Express\Entity\ConsumerInterface` to extract 
user's address and identity
*   The order/purchase object must implement the interface `UniAlteri\Paypal\Express\Entity\PurchaseInterface` to extract
amount of the invoice and its currency. This object must  also return URLs to use to redirect the consumer after Paypal 
operations.

###Process to checkout

Now, you must put a link in your webpage to redirect your user to paypal to process to the check out :

    <a href="<?php echo $service->prepareTransaction($purchase); ?>">Process to checkout to paypal</a>
    
###Get the payment result

The URL that the user was returned by Paypal contains the parameter "token". It is available via the superglobal $_GET.

This parameter is mandatory to retrieve via the service the operation's result via the method : 

    $result = $service->getTransactionResult($_GET['token']);
    
This method returns an object `UniAlteri\Paypal\Express\Service\TransactionResultInterface`

If the payment has been processed by Paypal, the method `isSuccessful` of the result object returns true

    $result = $service->getTransactionResult($_GET['token']);
    if ($result->isSuccessful()) {
        /* ... */
    } else {
       $errors = $result->getErrors();
    }

###Confirm checkout

The last step is the confirmation of the transaction. You must before get the payer id from the paypal's return :

    $payerId = $result->getPayerIdValue();
    
And confirm via the Paypal's API the transaction. You must pass again the token provided by Paypal in the URL 
    
    $confirmationResult = $service->confirmTransaction($_GET['token'], $payerId, $purchase);
    if ($confirmationResult->isSuccessful()) {
        echo '<p>Checkout successful</p>';
    } else {
        $errors = $confirmationResult->getErrors();
        foreach ($errors as $error) {
            echo '<p>'.$error->getShortMessage().' : '.$error->getLongMessage().'</p>';
        }
    }
