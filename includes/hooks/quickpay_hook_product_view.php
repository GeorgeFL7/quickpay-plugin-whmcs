
<?php


add_hook('ClientAreaProductDetailsOutput', 1, function($service) {
    if($service['service']['product']['paytype'] == 'recurring')
    {
        return '<form method="post" id="changeSubscriptionForm">
        <input type="hidden" id="changeCardFlag" name="changeCardFlag" value="TRUE">
        </form>
        <button type="submit" form="changeSubscriptionForm" value="Submit">Change card details</button>';    
    }
    
});

add_hook('ClientAreaProductDetails', 1, function($vars) {
   if(isset($_POST["changeCardFlag"]))
   {
       handle_change_card_request($vars['service']['subscriptionid'], $vars['service']['id']);
   }
});

function handle_change_card_request($subscriptionId, $serviceId)
{
    require_once __DIR__ . '/../../init.php';
    require_once __DIR__ . '/../gatewayfunctions.php';
    $gatewayModuleName = 'quickpay';
    $gateway = getGatewayVariables($gatewayModuleName);
    $params = [
        "autocapture" => $gateway['autocapture'],
        "apikey" => $gateway['apikey'],
        "subscriptionid" => $subscriptionId,
        "continue_url" => \WHMCS\Utility\Environment\WebHelper::getBaseUrl()."/clientarea.php?action=productdetails&id=".$serviceId
    ];
    require_once __DIR__ . '/../../modules/gateways/quickpay.php';
    $url = helper_update_subscription($params)->url;
    error_log($url);
    header("Location:" . $url);

}

?>