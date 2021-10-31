
<?php




/** Detect module name from filename. */

/** Fetch gateway configuration parameters. */


/** Die if module is not active. */

add_hook('ClientAreaProductDetailsOutput', 1, function($service) {
    
    return '<form method="post" id="changeSubscriptionForm">
    <input type="hidden" id="changeCardFlag" name="changeCardFlag" value="TRUE">
   </form>
   <button type="submit" form="changeSubscriptionForm" value="Submit">Change card details</button>';    

    
});


add_hook('ClientAreaProductDetails', 1, function($vars) {
   if(isset($_POST["changeCardFlag"]))
   {
       error_log("A fost apsasat butonul");
       handle_change_card_request($vars['service']['subscriptionid']);
   }
});

function handle_change_card_request($subscriptionId)
{
    require_once __DIR__ . '/../../init.php';
    require_once __DIR__ . '/../gatewayfunctions.php';
    require_once __DIR__ . '/../invoicefunctions.php';
    $gatewayModuleName = 'quickpay';
    $gateway = getGatewayVariables($gatewayModuleName);
    $params = [
        "autocapture" => $gateway['autocapture'],
        "apikey" => $gateway['apikey'],
        "subscriptionid" => $subscriptionId
    ];
    error_log(json_encode($params));
}

?>