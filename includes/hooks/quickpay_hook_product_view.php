
<?php

add_hook('ClientAreaProductDetailsOutput', 1, function ($service) {
    if ($service['service']['product']['paytype'] == 'recurring') {
        if (isset($_GET["isCardUpdate"])) {
            if (isset($_GET["updatedId"])) {
                //get card update status
                $query_quickpay_transaction = select_query("quickpay_transactions", "id, transaction_id, paid", ["transaction_id" => $_GET["updateId"]], "id DESC");
                $quickpay_transaction = mysql_fetch_array($query_quickpay_transaction);
                $card_update_status_message = "Your card has been declined, please try again!";
                $status = FALSE;
                if ($quickpay_transaction['paid'] == '1') {
                    $card_update_status_message = "Your card has been succesfully chWanged for this subscription";
                    $status = TRUE;
                }
                return dispay_change_payment($card_update_status_messzage, $status, $service['service']['paymentmethod']);
            }
        }

        return dispay_change_payment(null, FALSE, $service['service']['paymentmethod']);
    }
});


add_hook('ClientAreaProductDetails', 1, function ($vars) {
    require_once __DIR__ . '/../gatewayfunctions.php';
    if (isset($_POST["changeCardFlag"])) {
        handle_change_card_request($vars['service']['subscriptionid'], $vars['service']['id']);
    }
});

function dispay_change_payment($message, $success, $paymentmethod)
{
    $output = '<div class="card"><div class="card-body"><div class="row">';

    if (!empty($message)) {
         $output .= '<div class="col-12">';
        if ($success) {
            $output .= '
                <div class="alert alert-success alert-dismissible">
                    <strong>Success!</strong> ' . $message . '
                </div>';
        } else {
            $output .= '
                <div class="alert alert-danger alert-dismissible">
                    <strong>Error!</strong> ' . $message . '
                </div>';
        }
        $output .= '</div>';
    }

    $output .= '
        <div class="col-12"><h4 class="text-capitalize">' . $paymentmethod . '</h4></div>
            <div class="col-12">
                <p class="mb-2">Update card details for this subscription:</p>
                <form method="post" id="changeSubscriptionForm">
                    <input type="hidden" id="changeCardFlag" name="changeCardFlag" value="TRUE">
                </form>
                <div class="row"><div class="col-12 col-md-6">
                    <button class="btn btn-block btn-dark" type="submit" form="changeSubscriptionForm" value="Submit">Change card details</button>
                </div>
            </div>
        </div>';

    $output .= '</div></div></div>';

    return $output;
}

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
        "continue_url" => get_server_url() . "/clientarea.php?action=productdetails&id=" . $serviceId . "&isCardUpdate=1"
    ];
    require_once __DIR__ . '/../../modules/gateways/quickpay.php';
    $url = helper_update_subscription($params)->url;
    header("Location:" . $url);
}

function get_server_url()
{
    if (isset($_SERVER['HTTPS'])) {
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    } else {
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['SERVER_NAME'];
}

?>