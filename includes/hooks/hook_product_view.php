<?php
add_hook('ClientAreaProductDetails', 1, function($vars) {
    error_log("The available vars are" . json_encode($vars));
});
