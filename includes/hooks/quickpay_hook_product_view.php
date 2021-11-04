
<?php


add_hook('ClientAreaProductDetailsOutput', 1, function($service) {
    if($service['service']['product']['paytype'] == 'recurring')
    {
        if(isset($_GET["isCardUpdate"]))
        {
            if(isset($_GET["updatedId"]))
            {
                //get card update status
                $query_quickpay_transaction = select_query("quickpay_transactions", "id, transaction_id, paid", ["transaction_id" => $_GET["updateId"]], "id DESC");
                $quickpay_transaction = mysql_fetch_array($query_quickpay_transaction);
                $card_update_status_message = "Your card has been declined, please try again!";
                if($quickpay_transaction['paid'] == '1')
                {
                    $card_update_status_message = "Your card has been succesfully changed for this subscription";
                }

                return '<div>'.$card_update_status_message.'</div><br><form method="post" id="changeSubscriptionForm">
                <input type="hidden" id="changeCardFlag" name="changeCardFlag" value="TRUE">
                </form>
                <button type="submit" form="changeSubscriptionForm" value="Submit">Change card details</button>';   
            }
        }

        return '<form method="post" id="changeSubscriptionForm">
         <input type="hidden" id="changeCardFlag" name="changeCardFlag" value="TRUE">
        </form>
        <button type="submit" form="changeSubscriptionForm" value="Submit">Change card details</button>';   
    }
    
    
});

add_hook('ClientAreaProductDetails', 1, function($vars) {
   require_once __DIR__ . '/../gatewayfunctions.php';
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
        "continue_url" => getServerUrl()."/clientarea.php?action=productdetails&id=".$serviceId."&isCardUpdate=1"
    ];
    require_once __DIR__ . '/../../modules/gateways/quickpay.php';
    $url = helper_update_subscription($params)->url;
    header("Location:" . $url);

}

function getServerUrl(){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['SERVER_NAME'];
}

?>