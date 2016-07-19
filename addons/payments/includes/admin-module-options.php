<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if(adverts_request('adaction') == null) {
    adext_payments_page_options();
} else {
    adext_payments_page_pricing();
}