<?php
/**
 * Payments Module Admin Pages
 * 
 * This file contains function to handle Payments config logic in wp-admin 
 * and config form.
 *
 * @package     Adverts
 * @subpackage  Payments
 * @copyright   Copyright (c) 2015, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

/**
 * Renders Payments config form.
 * 
 * The page is rendered in wp-admin / Classifieds / Options / Payments panel
 * 
 * @since 0.1
 * @return void
 */
function adext_payments_page_options() {
    
    wp_enqueue_style( 'adverts-admin' );
    $flash = Adverts_Flash::instance();
    $error = array();
    
    add_filter("adverts_form_load", "adext_payments_load");
    
    $scheme = Adverts::instance()->get("form_payments_config");
    $form = new Adverts_Form( $scheme );
    $form->bind( get_option ( "adext_payments_config", array() ) );
    
    $button_text = __("Update Options", "adverts");
    
    if(isset($_POST) && !empty($_POST)) {
        $form->bind( stripslashes_deep( $_POST ) );
        $valid = $form->validate();

        if($valid) {
            update_option("adext_payments_config", $form->get_values());
            $flash->add_info( __("Settings updated.", "adverts") );
        } else {
            $flash->add_error( __("There are errors in your form.", "adverts") );
        }
    }
    
    include ADVERTS_PATH . 'addons/payments/admin/options.php';
}

/**
 * Customizes Payments Config Form Options.
 * 
 * This function is applied to adverts_form_load filter in order to customize
 * form options on load. The filter is applied in function adext_payments_page_options()
 * 
 * @see adext_payments_page_options()
 * 
 * @since 0.1
 * @return void
 */
function adext_payments_load( $form ) {
    
    $gateways = adext_payment_gateway_get();
    $pricings = new WP_Query( array( 
        'post_type' => 'adverts-pricing',
        'posts_per_page' => -1    
    ) );

    foreach($form["field"] as $key => $field) {
        $opts = array();
        
        if($field["name"] == "default_gateway") {
            if(empty($gateways)) {
                $content = __("Enable at least one <a href='%s'>Payment Gateway</a> before selecting default.", "adverts");
                $form["field"][$key]["type"] = 'adverts_field_label';
                $form["field"][$key]["content"] = sprintf($content, admin_url('edit.php?post_type=advert&page=adverts-extensions'));
            } else {
                foreach($gateways as $name => $data) {
                    $opts[] = array("value"=>$name, "text"=>$data["title"]);
                }
                $form["field"][$key]["options"] = $opts;
            } // endelse;
        } // endif; 
        
        if($field["name"] == "default_pricing") {
            if($pricings->posts == 0) {
                $content = __("Create at least one <a href='%s'>Pricing</a> before selecting default.", "adverts");
                $form["field"][$key]["type"] = 'adverts_field_label';
                $form["field"][$key]["content"] = sprintf($content, admin_url('edit.php?post_type=advert&page=adverts-extensions&module=payments&adaction=list'));
            } else {
                foreach($pricings->posts as $data) {
                    $opts[] = array("value"=>$data->ID, "text"=>$data->post_title);
                }
                $form["field"][$key]["options"] = $opts;
            }
        }
        
    } // endforeach; 
    
    
    return $form;
}

/**
 * Renders Payments Pricing: List, Add, Edit and Delete Pages.
 * 
 * The page is rendered in wp-admin / Classifieds / Options / Payments / Pricing panel
 * 
 * @since 0.1
 * @return void
 */
function adext_payments_page_pricing() {
    
    wp_enqueue_style( 'adverts-admin' );
    $flash = Adverts_Flash::instance();
    $error = array();
    $init = array(
        "post" => array( 'post_type'=> null ),
        "meta" => array( )
    );
    
    if(adverts_request("add")) {
        // Show Add New Pricing Page
        wp_enqueue_script( 'adverts-auto-numeric' );
        
        $title = __("Add Pricing", "adverts");
        $button_text = __("Add Pricing", "adverts");
        $form = new Adverts_Form(Adverts::instance()->get("form_payments"));
        
        if(isset($_POST) && !empty($_POST)) {
            
            // If $_POST is not empty the form was sent: validate and save it.
            $form->bind( stripslashes_deep( $_POST ) );
            $valid = $form->validate();

            if($valid) {
  
                $init["post"]["post_type"] = $form->get_value( "_post_type" );
                $form->set_value( "_post_type", null );
                
                $post_id = Adverts_Post::save($form, null, $init);
                
                $form->set_value( "_post_type", $init["post"]["post_type"] );

                if(is_wp_error($post_id)) {
                    $flash->add_error( $post_id->get_error_message() );
                } elseif($post_id === 0 ) {
                    $flash->add_error( __("There was an error while saving pricing in database.", "adverts") );
                } else {
                    $redirect = remove_query_arg('add');
                    $redirect = add_query_arg('edit', $post_id, $redirect);
                    $flash->add_info( __("New pricing has been added.", "adverts") );
                    return adverts_admin_js_redirect( $redirect );
                }
            } else {
                $flash->add_error( __("There are errors in your form.", "adverts") );
            }
        }
        
        include ADVERTS_PATH . 'addons/payments/admin/pricing-edit.php';
        
    } elseif( adverts_request("edit") ) {
        // Show Pricing Edit Page
        wp_enqueue_script( 'adverts-auto-numeric' );
        
        $title = __("Edit Pricing", "adverts");
        $button_text = __("Update Pricing", "adverts");
        
        $post = get_post( adverts_request("edit") );
        $bind = Adverts_Post::to_array( $post );
        $bind["_post_type"] = $bind["post_type"];
        
        $form = new Adverts_Form(Adverts::instance()->get("form_payments"));
        $form->bind( $bind );
        
        if(isset($_POST) && !empty($_POST)) {
            
            // If $_POST is not empty the form was sent: validate and save it.
            $form->bind( stripslashes_deep( $_POST ) );
            $valid = $form->validate();

            if($valid) {
  
                $init["post"]["post_type"] = $form->get_value( "_post_type" );
                $form->set_value( "_post_type", null );
                
                $post_id = Adverts_Post::save($form, $post, $init);

                $form->set_value( "_post_type", $init["post"]["post_type"] );
                
                if(is_wp_error($post_id)) {
                    $flash->add_error( $post_id->get_error_message() );
                } elseif($post_id === 0 ) {
                    $flash->add_error( __("There was an error while saving pricing in database.", "adverts") );
                } else {
                    $flash->add_info( __( "Pricing updated.", "adverts" ) );
                }
            } else {
                $flash->add_error( __("There are errors in your form.", "adverts") );
            }
        }
        
        include ADVERTS_PATH . 'addons/payments/admin/pricing-edit.php';
    } elseif( adverts_request( 'delete' ) ) {
        // Delete Pricing
        $post = get_post( adverts_request( 'delete' ) );

        if( ! $post || ! in_array( $post->post_type, array( 'adverts-pricing', 'adverts-renewal' ) ) ) {
            wp_die(__('Adverts Pricing with given ID does not exist.', 'adverts'));
        }
        
        foreach( get_children( $post->ID ) as $child) {
            wp_delete_post( $child->ID, true );
        }
        
        $flash->add_info( __("Pricing deleted.", "adverts"));
        
        wp_delete_post( $post->ID, true );
        wp_redirect( remove_query_arg( array( 'delete', 'noheader', 'pg' ) ) );
        exit;
    } elseif( adverts_request( 'action' ) || adverts_request( 'action2' ) ) {
        // Execute bulk actions
        $action = adverts_request( 'action', adverts_request( 'action2' ) );
        $item = ( adverts_request( 'item' ) );
        $i = 0;
        
        foreach($item as $id) {
            foreach( get_children( $id ) as $child) {
                wp_delete_post( $child->ID, true );
            }

            wp_delete_post( $id, true );
            $i++;
        }
        
        $flash->add_info( sprintf( _n( "1 Pricing deleted.", "%s Pricings deleted.", $i, "adverts"), $i) );
        
        wp_redirect( remove_query_arg( array( 'delete', 'noheader', 'pg' ) ) );
        exit;
    } else {
        // List pricings
        $loop = new WP_Query( array( 
            'post_type' => array( 'adverts-pricing', 'adverts-renewal' ),
            'posts_per_page' => 20, 
            'paged' => adverts_request( 'pg', 1 ),
        ) );
        
        include ADVERTS_PATH . 'addons/payments/admin/pricing-list.php';
    }
}

/**
 * Renders Payments History List and Edit Page.
 * 
 * The page is rendered in wp-admin / Classifieds / Payments History panel
 * 
 * @global wpdb $wpdb
 * @global wp_locale $wp_locale
 * 
 * @since 0.1
 * @return void
 */
function adext_payments_page_history() {
    global $wpdb, $wp_locale;
    
    wp_enqueue_style( 'adverts-admin' );
    $flash = Adverts_Flash::instance();
    
    if( adverts_request( "add" ) ) {
        // Do nothing currently users cannot add pricing manually.
    } elseif( adverts_request( "edit" ) ) {
        // Display payment edit page.
        $payment = get_post( adverts_request( "edit" ) );
        $form = new Adverts_Form();
        $form->load( Adverts::instance()->get( "form_payments_history" ) );
        $form->bind( Adverts_Post::to_array( $payment ) );
        
        $gateway_name = get_post_meta( $payment->ID, '_adverts_payment_gateway', true);
        $gateway = adext_payment_gateway_get( $gateway_name );
        
        if( !$gateway ) {
            $msg = sprintf( __( "Payment Method [%s] assigned to this Payment does not exist or was disabled.", "adverts" ), $gateway_name );
            $flash->add_error( $msg );
        }
        
        if(isset($_POST) && !empty($_POST)) {
            $form->bind( stripslashes_deep( $_POST ) );
            $valid = $form->validate();

            if($valid) {

                $status_new = $form->get_value("post_status");
                $status_old = $payment->post_status;
                
                $post_id = Adverts_Post::save($form, $payment);
                
                if(is_numeric($post_id) && $post_id>0 &&  $status_old!=$status_new ) {
                    do_action("adverts_payment_".$status_new, $payment);
                    $text = __('<strong>%1$s</strong> changed payment status to <strong>%2$s</strong>', 'adverts');
                    $message = sprintf( $text, wp_get_current_user()->user_login, $status_new);
                    adext_payments_log( $post_id, $message );
                    
                    $payment = get_post( $post_id );
                }
                
                if(is_wp_error($post_id)) {
                    $flash->add_error( $post_id->get_error_message() );
                } elseif($post_id === 0 ) {
                    $flash->add_error( __("There was an error while saving pricing in database.", "adverts") );
                } else {
                    $flash->add_info( __( "Payment updated.", "adverts" ) );
                }
            } else {
                $flash->add_error( __("There are errors in your form.", "adverts") );
            }
        }
        
        include ADVERTS_PATH . 'addons/payments/admin/payment-history-edit.php';
    } elseif( adverts_request( 'delete' ) ) {
        // Delete Payment
        $post = get_post( adverts_request( 'delete' ) );
        $i = 1;
        
        if( !$post || $post->post_type != 'adverts-payment' ) {
            wp_die(__('Adverts Payment with given ID does not exist.', 'adverts'));
        }
        
        foreach( get_children( $post->ID ) as $child) {
            wp_delete_post( $child->ID, true );
        }

        $flash->add_info( sprintf( _n( "1 Payment deleted.", "%s Payments deleted.", $i, "adverts"), $i) );
        
        wp_delete_post( $post->ID, true );
        wp_redirect( remove_query_arg( array( 'delete', 'noheader', 'pg' ) ) );
        exit;
    } elseif( adverts_request( 'filter_action' ) ) {
        // Apply filters and return to payments history list
        $url = remove_query_arg( array( 'delete', 'noheader', 'pg' ) );
        $url = add_query_arg( array( 'month' => adverts_request('month') ), $url );
        
        wp_redirect( $url );
        exit;
    } elseif( adverts_request( 'action' ) || adverts_request( 'action2' ) ) {
        // Apply bulk actions and return to payments history list
        $action = adverts_request( 'action' );
        
        if( empty( $action) ) {
            $action = adverts_request( 'action2' );
        }
        
        $item = ( adverts_request( 'item' ) );

        if( $action == "delete" ) {
            $i = 0;
            foreach($item as $id) {
                foreach( get_children( $id ) as $child) {
                    wp_delete_post( $child->ID, true );
                }
                $i++;
                wp_delete_post( $id, true );
            }
            
            $flash->add_info( sprintf( _n( "1 Payment deleted.", "%s Payments deleted.", $i, "adverts"), $i) );
            
        } elseif( stripos($action, "set-status-") === 0 ) {
            
            $status_new = str_replace("set-status-", "", $action);
            $status_obj = get_post_status_object($status_new);
            
            foreach($item as $id) {
                $status_old = get_post_status( $id );

                if($status_old != $status_new) {
                    wp_update_post(array("ID"=>$id, "post_status"=>$status_new));
                    do_action("adverts_payment_".$status_new, $payment);
                }
            }
            
            $flash->add_info( sprintf(__("Status for selected Payments was changed to: %s"), $status_obj->label ) );
        }
        
        wp_redirect( remove_query_arg( array( 'delete', 'noheader', 'pg' ) ) );
        exit;
    } else {
        // Display Payments History
        $status_list = array(
            "pending" => 0,
            "completed" => 0,
            "failed" => 0,
            "refunded" => 0
        );
        
        foreach($status_list as $k => $v) {
            $sql = "SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = %s AND post_type = 'adverts-payment'";
            $status_list[$k] = (int) $wpdb->get_var( $wpdb->prepare( $sql, $k ) );
        }
        
        $sql = "SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month FROM $wpdb->posts WHERE post_type = %s ORDER BY post_date DESC";
        $months_list = $wpdb->get_results( $wpdb->prepare( $sql, 'adverts-payment' ) );
        $months = array();
        foreach($months_list as $m) {
            $months[] = array(
                "value" => $m->year."-".$m->month, 
                "label" => sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $m->month ), $m->year )
            );
        }
        
        $month = adverts_request("month", "");
        $filter = adverts_request("status", "");
        

        
        $loop_params = array( 
            'post_type' => 'adverts-payment',
            'posts_per_page' => 20, 
            'paged' => adverts_request( 'pg', 1 ),
        );
        
        if($filter) {
            $loop_params['post_status'] = $filter;
        }
        
        if($month == "this-month") {
            $before = date('Y-m-d H:i:s', strtotime('last day of this month', current_time('timestamp')));
            $after = date('Y-m-d H:i:s', strtotime('first day of this month', current_time('timestamp')));
            $loop_params['date_query'] = array( array( 'before'=>$before, 'after'=>$after, 'inclusive'=>true) );
        } elseif($month == "last-month") {
            $before = date('Y-m-d H:i:s', strtotime('last day of last month', current_time('timestamp')));
            $after = date('Y-m-d H:i:s', strtotime('first day of last month', current_time('timestamp')));
            $loop_params['date_query'] = array( array( 'before'=>$before, 'after'=>$after, 'inclusive'=>true) );
        } elseif( !empty($month) ) {
            $time = strtotime( $month."-10");
            $before = date('Y-m-d', strtotime('last day of this month', $time));
            $after = date('Y-m-d H:i:s', strtotime('first day of this month', $time));
            $loop_params['date_query'] = array( array( 'before'=>'', 'after'=>'', 'inclusive'=>true) );
        }

        $loop = new WP_Query( $loop_params );

        include ADVERTS_PATH . 'addons/payments/admin/payment-history-list.php';
    }
}

/**
 * Display 'Pending' state on Classifieds list
 * 
 * This functions shows Expired state in the wp-admin / Classifieds panel
 * 
 * @see display_post_states filter
 * 
 * @global WP_Post $post
 * @param array $states
 * @return array
 */
function adext_payments_display_pending_state( $states ) {
    global $post;
    $arg = get_query_var( 'post_status' );
     
    if($arg == 'advert-pending'){
        return $states;
    }
     
    if($post->post_status != 'advert-pending'){
        return $states;
    }
     
    $loop = get_posts( array( 
        'post_type' => 'adverts-payment', 
        'post_status' => 'pending',
        'posts_per_page' => 1, 
        'meta_query' => array(
            array(
                'key' => '_adverts_object_id', 
                'value' => $post->ID
            )
        )
    ) );
    
    if( isset($loop[0]) ) {
        $id = $loop[0]->ID;
    } else {
        $id = null;
    }
    
    $order_link = null;
    
    if($id !== null) {
        $span = new Adverts_Html("span", array(
            "class" => "dashicons dashicons-cart",
            "style" => "font-size: 18px"
        ));
        $span->forceLongClosing(true);
        
        $order_link = new Adverts_Html("a", array(
            "href" => admin_url("edit.php?post_type=advert&page=adext-payment-history&edit=".$id),
            "title" => __("View order", "adverts")
        ), $span->render());
    } else {
        $span = new Adverts_Html("span", array(
            "class" => "dashicons dashicons-info",
            "title" => __( 'Abandoned', 'adverts' ),
            "style" => "font-size: 18px"
        ));
        $span->forceLongClosing(true);
        
        $order_link = $span->render();
    }
    
    return array( __( 'Pending Payment', 'adverts' ) . $order_link );

    return $states;
}

/**
 * Renders Pending Payment post status
 * 
 * This function is executed by admin_head action, it adds 'pending payment' 
 * status in Advert edition panel
 * 
 * @see admin_head action
 * 
 * @global string $post_type
 * @global WP_Post $post
 * @since 1.0
 * @return void
 */
function adext_payments_admin_head() {
    global $post_type, $post;
    
    // Make sure this is Adverts post type
    if ( $post_type == 'advert' && $post && $post->post_status == 'advert-pending' ):  
    ?>

    <script type="text/javascript">
        jQuery(function($) {
            $("select#post_status").append($("<option></option>")
                .attr("id", "adverts-payments-pending-payment")
                .attr("value", "advert-pending")
                .addClass("adverts-post-status")
                .css("display", "none")
                .html("<?php _e( "Pending Payment", "adverts" ) ?>")
            );
                
            $("#adverts-payments-pending-payment").prop("selected", true).attr("selected", "selected");
            $("input#publish").val("<?php _e("Update", "adverts") ?>");
            var x = 0;
        });
    </script>
    <?php 
    
    endif; 
}


// Payment History Form Structure
Adverts::instance()->set("form_payments_history", array(
    "name" => "payments_history",
    "action" => "",
    "field" => array(
        array(
            "name" => "_adverts_user_id",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __( "User ID", "adverts" ),
        ),
        array(
            "name" => "adverts_person",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __( "Contact Person", "adverts" ),
            "is_required" => true,
            "validator" => array( 
                array( "name" => "is_required" ),
            )
        ),
        array(
            "name" => "adverts_email",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __( "Email", "adverts" ),
            "is_required" => true,
            "validator" => array( 
                array( "name" => "is_required" ),
                array( "name" => "is_email" )
            )
        ),
        array(
            "name" => "_adverts_payment_total",
            "type" => "adverts_field_text",
            "order" => 10,
            "filter" => array(
                array( "name" => "money" )
            ),
        ),
        array(
            "name" => "_adverts_payment_paid",
            "type" => "adverts_field_text",
            "order" => 10,
            "filter" => array(
                array( "name" => "money" )
            ),
        ),
        array(
            "name" => "post_status",
            "type" => "adverts_field_text",
            "order" => 10,
            "is_required" => true,
            "validator" => array( 
                array( "name" => "is_required" ),
            )
        ),
    )
));

// Payment Default Config Form Structure
Adverts::instance()->set("form_payments_config", array(
    "name" => "payments_config",
    "action" => "",
    "field" => array(
        array(
            "name" => "default_gateway",
            "type" => "adverts_field_select",
            "label" => __("Default Payment Gateway", "adverts"),
            "order" => 10,
            "empty_option" => true,
            "options" => array(),
            "content" => ''
        ),
        array(
            "name" => "default_pricing",
            "type" => "adverts_field_select",
            "label" => __("Default Pricing", "adverts"),
            "order" => 10,
            "empty_option" => true,
            "options" => array()
        )
    )
));

// Payment Pricing Form Structure
Adverts::instance()->set("form_payments", array(
    "name" => "payment",
    "action" => "",
    "field" => array(
        array(
            "name" => "_post_type",
            "type" => "adverts_field_select",
            "label" => __( "Pricing Type", "adverts" ),
            "order" => 10,
            "empty_option" => true,
            "options" => array(
                array( "value" => "adverts-pricing", "text" => __( "New Advert Posting", "adverts" ) ),
                array( "value" => "adverts-renewal", "text" => __( "Advert Renewal", "adverts" ) )
            ),
            "validator" => array(
                array( "name" => "is_required" )
            ),
            "value" => ""
        ),
        array(
            "name" => "post_title",
            "type" => "adverts_field_text",
            "label" => __("Title", "adverts"),
            "order" => 10,
            "validator" => array(
                array( "name" => "is_required" )
            )
        ),
        array(
            "name" => "post_content",
            "type" => "adverts_field_text",
            "label" => __("Description", "adverts"),
            "order" => 10,
        ),
        array(
            "name" => "adverts_price",
            "type" => "adverts_field_text",
            "label" => __("Price", "adverts"),
            "order" => 10,
            "filter" => array(
                array( "name" => "money" )
            )
        ),
        array(
            "name" => "adverts_visible",
            "type" => "adverts_field_text",
            "label" => __("Visible", "adverts"),
            "hint" => __("Number of days the Ad will be visible.", "adverts"),
            "order" => 10,
            "validator" => array(
                array( "name" => "is_required" ),
                array( "name" => "is_integer" )
            )
            
        ),

    )
    
));

