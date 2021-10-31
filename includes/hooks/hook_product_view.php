<?php
add_hook('ClientAreaProductDetailsOutput', 1, function($service) {

    return '<form method="post" id="changeSubscriptionForm">
    <input type="hidden" id="changeCardFlag" name="changeCardFlag" value="TRUE">
   </form>
   <button type="submit" form="changeSubscriptionForm" value="Submit">Change card details</button>';    

    
});

add_hook('ClientAreaProductDetails', 1, function($vars) {
   if(isset($_POST["changeCardFlag"]))
   {
       error_log("A fost apasat butonul");
   }
});

?>