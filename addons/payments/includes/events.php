<?php
/**
 * Scheduled Events
 * 
 * Schedules Payments module events.
 *
 * @package     Adverts
 * @copyright   Copyright (c) 2015, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_action( 'wp', 'adext_payments_setup_schedule' );

add_action( 'adext_payments_event_gc', 'adext_payments_event_gc' );

/**
 * Schedules Adverts Payments events
 * 
 * This function schedules (in wp-cron) default events.
 * 
 * @since 1.3.5
 * @return void
 */
function adext_payments_setup_schedule() {
    
    // Schedule garbage collection, if not already scheduled
    if ( ! wp_next_scheduled( 'adext_payments_event_gc' ) ) {
        wp_schedule_event( time(), 'daily', 'adext_payments_event_gc');
    }

}

/**
 * Ads Garbage Collector
 * 
 * This function will delete Payments with status adverts-payment-tmp older than
 * 24 hours.
 * 
 * @since   1.3.5
 * @return  int      Number of deleted payments
 */
function adext_payments_event_gc() {
    
    // find tmp adverts that were last modified more than 24 hours ago,
    // by this time it is safe to assume that user will not finish posting the ad.
    $posts = new WP_Query( array(
        "post_type" => "adverts-payment",
        "post_status" => "adverts-payment-tmp",
        "date_query" => array(
            array(
                "column" => "post_modified_gmt",
                "before" => date("Y-m-d H:i:s", current_time( 'timestamp', 1) - 3600*24 )
            )
        )
    ) );
    
    $i = 0;
    
    if($posts->post_count) {
        foreach($posts->posts as $post) {
            
            // delete all attachements associated with this post.
            $param = array( 'post_parent' => $post->ID, 'post_type' => 'attachment' );
            $children = get_posts( $param );

            if( is_array( $children ) ) {
                foreach( $children as $attch) {
                    adverts_delete_post( $attch->ID );
                }
            } 

            // delete or trash the post
            adverts_delete_post( $post->ID, true );
            $i++;
        }
    }
    
    return $i;
}