<?php
/**
 * Featured Ads Module Admin Pages
 * 
 * This file contains function to handle bank transfer module logic in wp-admin 
 * and config form.
 *
 * @package     Adverts
 * @subpackage  Featured
 * @copyright   Copyright (c) 2015, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.3
 */

/**
 * Renders config Featured Ads config form.
 * 
 * The page is rendered in wp-admin / Classifieds / Options / Featured Ads 
 * 
 * @since 1.0.3
 * @return void
 */
function adext_featured_page_options() {
    
    wp_enqueue_style( 'adverts-admin' );
    $flash = Adverts_Flash::instance();
    $error = array();

    include ADVERTS_PATH . 'addons/featured/admin/options.php';
}