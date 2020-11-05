<?php
/**
 * Payments Module
 * 
 * This module allows to charge users for posting ads.
 * 
 * Note. In order to use this module you should enabled at least one payment gateway module.
 *
 * @package Adverts
 * @subpackage Payments
 * @author Grzegorz Winiarski
 * @version 0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

global $adverts_namespace;

$adverts_namespace['payments'] = array(
    'option_name' => 'adext_payments_config',
    'default' => array(
        'default_gateway' => '',
        'default_pricing' => '',
        'checkout_page' => ''
    )
);

add_action( 'init', 'adext_payments_init' );
add_action( 'adverts_core_initiated', 'adext_payments_core_init');

if(is_admin() ) {
    add_action( 'init', 'adext_payments_init_admin' );
} else {
    add_action( 'init', 'adext_payments_init_frontend' );
}

function adext_payments_init() {

    $args = array(
        'labels'        => array(),
        'public'        => false,
        'show_ui'       => false,
        'supports'      => array( 'title' ),
        'has_archive'   => false,
    );
  
    register_post_type( 'adverts-pricing', apply_filters( 'adverts_post_type', $args, 'adverts-pricing') ); 
    
    register_post_type( 'adverts-renewal', apply_filters( 'adverts_post_type', $args, 'adverts-renewal') ); 
    
    $args = array(
        'labels'        => array(),
        'public'        => false,
        'show_ui'       => false,
        'supports'      => array( 'title' ),
        'has_archive'   => false,
    );
    
    register_post_type( 'adverts-payment', apply_filters( 'adverts_post_type', $args, 'adverts-payment') ); 
    
    add_action( 'save_post_adverts-payment', 'adverts_create_hash', 10, 3 );
    
    register_post_status( 'adverts-payment-tmp', array(
        'label'                     => _x( 'Temporary', 'temporary status payment', 'wpadverts' ),
        'public'                    => is_admin(),
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Unread <span class="count">(%s)</span>', 'Unread <span class="count">(%s)</span>', 'wpadverts' ),
    ) );
    
    register_post_status( 'completed', array(
        'label'                     => _x( 'Completed', 'completed status payment', 'wpadverts' ),
        'public'                    => is_admin(),
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Unread <span class="count">(%s)</span>', 'Unread <span class="count">(%s)</span>', 'wpadverts' ),
    ) );
    
    register_post_status( 'failed', array(
        'label'                     => _x( 'Failed', 'failed status payment', 'wpadverts' ),
        'public'                    => is_admin(),
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'wpadverts' ),
    ) );
    
    register_post_status( 'pending', array(
        'label'                     => _x( 'Pending', 'pending status payment', 'wpadverts' ),
        'public'                    => is_admin(),
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'wpadverts' ),
    ) );
    
    register_post_status( 'refunded', array(
        'label'                     => _x( 'Refunded', 'refunded status payment', 'wpadverts' ),
        'public'                    => is_admin(),
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>', 'wpadverts' ),
    ) );
    
    register_post_status( 'advert-pending', array(
        'label'        => _x( 'Pending', 'post' ),
        'public'       => is_admin() || current_user_can( "edit_pages" ),
        'label_count'  => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'wpadverts' )
     ) );
    
    adverts_form_add_field( 'adverts_field_listing_type', array(
        ''
    ));
    
    add_action("adverts_install_module_payments", "adext_payments_install");
    add_filter("adverts_form_load", "adext_payments_form_load");
    
    wp_register_style( 'adverts-payments-frontend', ADVERTS_URL . '/addons/payments/assets/css/payments-frontend.css', array(), '1.2.8' );
    wp_register_style( 'adverts-payments-white-page', ADVERTS_URL . '/addons/payments/assets/css/white-page.css', array(), '1.3.0' );
    
    include_once ADVERTS_PATH . 'addons/payments/includes/shortcodes.php';
    include_once ADVERTS_PATH . 'addons/payments/includes/events.php';
}

/**
 * Payments module installer
 * 
 * This function is executed when Payments module is activated. It creates
 * new default pricings if the pricings list is empty.
 * 
 * @since 0.2
 * @return void
 */
function adext_payments_install() {
    
    $args = array(
       'posts_per_page' => 1,
       'post_type' => 'adverts-pricing',
    );
    $query = new WP_Query( $args );

    if( $query->found_posts > 0) {
        return;
    }
    
    $id = wp_insert_post( array( 
        'post_title' => "Free",
        'post_content' => "",
        'post_type' => "adverts-pricing"
    ) );

    add_post_meta( $id, 'adverts_visible', '30' );
    add_post_meta( $id, 'adverts_price', '0' );
    
    $id = wp_insert_post( array( 
        'post_title' => "Premium",
        'post_content' => "",
        'post_type' => "adverts-pricing"
    ) );

    add_post_meta( $id, 'adverts_visible', '45' );
    add_post_meta( $id, 'adverts_price', '50' );
}

/**
 * Function initiates Payments module in the frontend
 * 
 * @since 1.0
 */
function adext_payments_init_frontend() {
    wp_register_script('adext-payments', plugins_url().'/wpadverts/addons/payments/assets/js/payments.js', array('jquery'), '1.3.0');
    
    add_filter("adverts_action", "adext_payments_add_action_payment");
    add_filter("adverts_action", "adext_payments_add_action_notify");

    add_filter("adverts_action_payment", "adext_payments_action_payment", 10, 2);
    
    add_action( "adverts_sh_manage_actions_more", "adext_payments_action_renew" );
    add_action( "adverts_sh_manage_actions_more", "adext_payments_action_complete" );
    
    add_filter( "adverts_manage_action", "adext_payments_manage_action" );
    add_filter( "adverts_manage_action_renew", "adext_payments_manage_action_renew" );
    
    add_action( "adverts_sh_manage_list_status", 'adext_payments_manage_list_status' );
}

/**
 * Switch shortcode_adverts_add action to "payment"
 * 
 * Function checks if next current action in shortcode shortcode_adverts_add
 * is "save" and if listing price is greater than 0. If so then current action 
 * is changed to "payment".
 * 
 * @see shortcode_adverts_add()
 * @since 1.0
 * 
 * @param type $action
 * @return string
 */
function adext_payments_add_action_payment( $action ) {
    
    if( $action != "save" ) {
        return $action;
    }
    
    $listing_type = get_post_meta( adverts_request("_post_id", null), "payments_listing_type", true );

    if( $listing_type === false || empty($listing_type) ) {
        return $action;
    } 
    
    $price = get_post_meta( $listing_type, "adverts_price", true );

    if( $price === false || empty($price) ) {
        
        $publish = current_time('mysql');
        $visible = get_post_meta( $listing_type, "adverts_visible", true );
        $post_id = absint( adverts_request("_post_id", null) );
        
        if( $visible > 0 ) {
            $expiry = strtotime( $publish . " +$visible DAYS" );
            update_post_meta( $post_id, "_expiration_date", $expiry );
        } else {
            delete_post_meta( $post_id, "_expiration_date" );
        }
        
        $menu_order = absint( get_post_meta( $listing_type, "is_featured", true ) );
        
        if( $menu_order > 0 ) {
            wp_update_post( array( "ID" => $post_id, "menu_order" => $menu_order ) );
        }
        
        return $action;
    }
    
    return "payment";
}

/**
 * Switch shortcode_adverts_add action to $gateway_name
 * 
 * Function checks if adverts-notify-id param is sent via GET or POST if so
 * then the function will try to get payment gateway for this payment object and
 * switch action to gateway_name.
 * 
 * In other words the function will trigger "adverts_action_$gateway_name" filter 
 * execution.
 * 
 * @see shortcode_adverts_add()
 * @since 1.0
 * 
 * @param string $action
 * @return string
 */
function adext_payments_add_action_notify( $action ) {
    if( $action != "" || !adverts_request( "adverts-notify-id" ) ) {
        return $action;
    }
    
    $payment = get_post( adverts_request( "adverts-notify-id" ) );
    
    if( !$payment ) {
        return $action;
    }
    
    $gateway_name = get_post_meta( $payment->ID, "_adverts_payment_gateway", true );
    $gateway = adext_payment_gateway_get( $gateway_name );
    
    return $gateway_name;
}

/**
 * Adds Listing Type field to Add Advert form.
 * 
 * This function is applied to "adverts_form_load" filter in Adverts_Form::load()
 * when Advert form is being loaded.
 * 
 * @since 1.0
 * @see Adverts_Form::load()
 * 
 * @param array $form
 * @return array
 */
function adext_payments_form_load( $form ) {
    
    if($form["name"] != 'advert' || is_admin() || adverts_request( "advert_id" ) ) {
        return $form;
    }
    
    // do not show payment options when editing Ad.
    $id = adverts_request( "advert_id" );
    $ad = get_post( $id );
    if( intval($id) && $ad && in_array($ad->post_status, array("publish", "expired", "pending" ) ) ) {
        return $form;
    }
    
    $form["field"][] = array(
        "name" => "_listing_information",
        "type" => "adverts_field_header",
        "order" => 1000,
        "label" => __( 'Listing Information', 'wpadverts' )
    );
    
    $opts = array();
    $pricings = new WP_Query( array( 
        'post_type' => 'adverts-pricing',
        'post_status' => 'draft',
        'posts_per_page' => -1
    ) );
    
    $pricings = apply_filters( 'wpadverts_filter_pricings', $pricings );
    
    adverts_form_add_field("adverts_payments_field_payment", array(
        "renderer" => "adverts_payments_field_payment",
        "callback_save" => "adverts_save_single",
        "callback_bind" => "adverts_bind_single",
    ) );
    
    foreach($pricings->posts as $data) {
        
        if( get_post_meta( $data->ID, 'adverts_price', true ) ) {
            $adverts_price = adverts_price( get_post_meta( $data->ID, 'adverts_price', true ) );
        } else {
            $adverts_price = __("Free", "wpadverts");
        }
        
        $opts[] = array( "value"=>$data->ID, "text"=> $data->post_content );
    }

    wp_enqueue_style( 'adverts-payments-frontend' );
    
    $form["field"][] = array(
        "name" => "payments_listing_type",
        "type" => "adverts_payments_field_payment",
        "label" => __("Listing", "wpadverts"),
        "order" => 1001,
        "empty_option" => true,
        "options" => apply_filters( "wpadverts_filter_pricings_options", $opts ),
        "value" => "",
        "validator" => array(
            array( "name" => "is_required" ),
            array( "name" => "verify_choices" )
        )
    );
    
    add_filter("adverts_form_bind", "adext_payments_form_bind");
    
    return $form;
}

/**
 * HTML for Listing Type field in [adverts_add]
 * 
 * This function echos HTML for "Listing Type" field in [adverts_add], it is being 
 * regstered as a new field and called in adext_payments_form_load() function.
 * 
 * @see adext_payments_form_load()
 * 
 * @since 1.0.8
 * @access public
 * @param array $field      Form field
 * @return void
 */
function adverts_payments_field_payment($field) {
    
    ob_start();
    
    echo '<div class="adverts-pricings-list">';
    
    foreach( $field["options"] as $option ) {
    
        $post_id = $option["value"];
        $post = get_post( $post_id );
        $visible = get_post_meta( $post_id, 'adverts_visible', true );

        if( get_post_meta( $post_id, 'adverts_price', true ) ) {
            $adverts_price = adverts_price( get_post_meta( $post_id, 'adverts_price', true ) );
        } else {
            $adverts_price = __("Free", "wpadverts");
        }

        if($post->post_content) {
            $post_content = '<br/><small style="padding-left:25px">'.$post->post_content.'</small>' ;
        } else {
            $post_content = '';
        }
        
        if( isset( $option['disabled'] ) && $option['disabled'] ) {
            $disabled = true;
        } else {
            $disabled = false;
        }
        
        ?>

        <div class="adverts-listing-type-x <?php if($disabled): ?>adverts-listing-type-x-disabled<?php endif; ?>">

            <label class="adverts-cute-input adverts-cute-radio " for="<?php echo esc_attr( $field["name"] . "_" . $option["value"] ) ?>">
                <input name="<?php echo esc_attr( $field["name"] ) ?>" class="adverts-listing-type-input" id="<?php echo esc_attr( $field["name"] . "_" . $option["value"] ) ?>" type="radio" value="<?php echo $post->ID ?>" <?php checked($post->ID, $field["value"]) ?> <?php disabled( $disabled) ?> />
                <div class="adverts-cute-input-indicator"></div>
            </label>

            <div class="adverts-listing-type-field">
                <div class="adverts-listing-type-name">
                    <span class="adverts-listing-type-title"><?php echo esc_html( $post->post_title ) ?></span>

                </div>

                <div class="adverts-listing-type-features">
                    <span class="adverts-listing-type-feature-duration">
                        <span class="adverts-listing-type-icon adverts-icon-clock"></span>
                        <?php if( $visible > 0 ): ?>
                        <?php printf( _n("Visible 1 day", "Visible %d days", $visible, "wpadverts"), $visible) ?>
                        <?php else: ?>
                        <?php echo esc_html_e( "Never Expires", "wpadverts" ) ?>
                        <?php endif; ?>
                    </span>

                    <?php do_action("adverts_payments_features", $post->ID ) ?>
                </div>

                <?php if($post->post_content): ?>
                <div class="adverts-listing-type-features adverts-listing-type-icon adverts-icon-info">
                    <?php echo $post->post_content ?>
                </div>
                <?php endif; ?>
            </div>

            <span class="adverts-listing-type-cost">
                <?php echo $adverts_price ?>
            </span>
        </div>

        <?php
    }
    
    echo '</div>';
    echo ob_get_clean();
}

/**
 * Binds default payment_listing_type value
 * 
 * @see adext_payments_form_load() Function which adds this function to filters list
 * @uses adverts_form_bind Filter which exexutes this function
 * 
 * @since 1.0
 * @access public
 * @param Adverts_Form $form
 * @return Adverts_Form
 */
function adext_payments_form_bind( Adverts_Form $form ) {
    
    if( ! $form->get_value( "payments_listing_type" ) ) {
        $form->set_value("payments_listing_type", adverts_config('payments.default_pricing'));
    }
    return $form;
}

/**
 * Payment action
 * 
 * This function is executed when "payment" action is run shortcode_adverts_add
 * 
 * @see shortcode_adverts_add()
 * @since 1.0
 * 
 * $param string $content 
 * @param Adverts_Form $form
 * @return null
 */
function adext_payments_action_payment($content, Adverts_Form $form ) {
    
    $post_id = adverts_request( "_post_id" );
    $post = get_post( $post_id );
    
    $info[] = array(
        "message" => sprintf( __( "<p><strong>Your Payment Is Required</strong><p>Please complete payment for the <em>'%s'</em> Ad posting to have it published.</p>", "wpadverts" ), $post->post_title ),
        "icon" => "adverts-icon-basket"
    );
    $error = array();
    
    wp_enqueue_script( 'adext-payments' );
    
    $adverts_flash = array( "error" => $error, "info" => $info );


    wp_update_post( array( 
        "ID" => $post_id,
        "post_status" => "advert-pending"
    ) );

    do_action( "wpadverts_advert_saved", $post_id );
    
    if( !is_user_logged_in() && get_post_meta( $post_id, "_adverts_account", true) == 1 ) {
        adverts_create_user_from_post_id( $post_id, true );
    }
    
    $listing_id = get_post_meta( $post_id, "payments_listing_type", true );
    $listing = get_post( $listing_id );
    
    $price = get_post_meta($listing_id, 'adverts_price', true);
    
    $payment_id = adext_post_get_payment( $post_id, $listing_id, "pending" );
    
    if( ! $payment_id  ) {
        $payment_id = adext_insert_payment( array(
            "buyer_name" => get_post_meta( $post_id, "adverts_person", true ), 
            "buyer_email" => get_post_meta( $post_id, "adverts_email", true ),           
            "buyer_ip" => null,
            "buyer_id" => get_current_user_id(),
            "payment_status" => "pending",
            "payment_type" => "adverts-payment",
            "object_id" => $post_id,
            "pricing_id" => $listing_id,
            "payment_gateway" => adverts_config("payments.default_gateway"),
            "payment_for" => "post",
            "payment_paid" => 0
        ) );
    }
    
    $payment = get_post( $payment_id );
    
    ob_start();
    include ADVERTS_PATH . 'addons/payments/templates/add-payment.php';
    return ob_get_clean();
}

/**
 * Switch shortcode_adverts_manage action to "renew"
 * 
 * Function checks if $_GET param advert_renew is set and if it is an ID
 * of existing Advert owned by current user, if it is then action "renew"
 * is enqueued.
 * 
 * @see shortcode_adverts_manage()
 * @since 1.1.0
 * 
 * @param   string  $action     Current action to execute
 * @return  string
 */
function adext_payments_manage_action( $action ) {
    
    // maybe render payment success
    if( adverts_request( "adverts-notify-id" ) ) {
        $payment = get_post( adverts_request( "adverts-notify-id" ) );
        
        if( $payment && $payment->post_type = "adverts-payment" ) {
        
            $payment_gateway = get_post_meta( $payment->ID, "_adverts_payment_gateway", true );

            if( $payment->post_author == get_current_user_id()  && ! empty( $payment_gateway ) ) {
                return $payment_gateway;
            }
        }
    }
    
    // continue if there is advert_renew param
    if( ! adverts_request( "advert_renew" ) ) {
        return $action;
    }
    
    $advert = get_post( adverts_request( "advert_renew" ) );
    
    if( ! $advert instanceof WP_Post ) {
        return $action;
    }
    
    if( $advert->post_type != "advert" ) {
        return $action;
    }
    
    if( $advert->post_author != get_current_user_id() ) {
        return $action;
    }
    
    $action = "renew";
    
    return $action;
}

/**
 * Displays "Complete Payment" button in [adverts_manage].
 * 
 * This function is executed by adverts_sh_manage_actions_more filter, so
 * it will be displayed after clicking "More".
 * 
 * @see adverts_sh_manage_actions_more
 * 
 * @since   1.3.3
 * @param   int     $post_id    Advert ID
 * @return  void
 */
function adext_payments_action_complete( $post_id ) {
    
    if( get_post_field( "post_status", $post_id ) != "advert-pending" ) {
        return;
    }
    
    $loop = get_posts( array( 
        'post_type' => 'adverts-payment', 
        'post_status' => 'pending',
        'posts_per_page' => 1, 
        'meta_query' => array(
            array(
                'key' => '_adverts_object_id', 
                'value' => $post_id
            )
        )
    ) );

    if( ! isset( $loop[0] ) ) {
        return;
    }
    
    $id = $loop[0]->ID;

    include_once ADVERTS_PATH . "/includes/class-html.php";

    $span = '<span class="adverts-icon-arrows-cw"></span>';
    $a = new Adverts_Html("a", array(
        "href" => adext_payments_get_checkout_url( $id ),
        "class" => "adverts-manage-action",
    ), $span . " " . __("Complete Payment", "wpadverts") );

    echo $a->render();
}

/**
 * Displays "Renew Ad" button in [adverts_manage].
 * 
 * This function is executed by adverts_sh_manage_actions_more filter, so
 * it will be displayed after clicking "More".
 * 
 * @see adverts_sh_manage_actions_more
 * 
 * @since 1.1.0
 * @param   int     $post_id    Post ID
 * @return  void
 */
function adext_payments_action_renew( $post_id ) {

    if( get_post_field( "post_status", $post_id ) == "advert-pending" ) {
        return;
    }
        
    $renewals = get_posts( array( 
        'post_type' => 'adverts-renewal', 
        'post_status' => 'any',
        'posts_per_page' => 1, 
    ) );
    
    $renewals = apply_filters( "wpadverts_filter_renewals", $renewals, $post_id );
    
    if( empty( $renewals ) ) {
        return;
    }
    
    include_once ADVERTS_PATH . "/includes/class-html.php";
    
    $span = '<span class="adverts-icon-arrows-cw"></span>';
    $a = new Adverts_Html("a", array(
        "href" => add_query_arg( "advert_renew", $post_id ),
        "class" => "adverts-manage-action",
    ), $span . " " . __("Renew Ad", "wpadverts") );
    
    echo $a->render();
}

/**
 * Renders a form which allows to renew an Advert
 * 
 * This function is executed using adverts_manage_action_renew filter.
 * 
 * @see     adverts_manage_action_renew
 * @since   1.1.0
 * 
 * @param   string    $content  Content generated form [adverts_manage]
 * @param   array     $atts     [adverts_manage] params
 * @return  string              HTML for form which will allow to renew an Ad
 */
function adext_payments_manage_action_renew( $content, $atts = array() ) {

    $error = null;
    $info = null;
    
    $baseurl = apply_filters( "adverts_manage_baseurl", get_the_permalink() );
    
    wp_enqueue_style( 'adverts-frontend' );
    wp_enqueue_style( 'adverts-payments-frontend' );
    
    $adverts_flash = array( "error" => array(), "info" => array() );
    $post = get_post( adverts_request( "advert_renew" ) );
    
    if( ! in_array( $post->post_status, array( 'publish', 'expired' ) ) ) {
        $format = __( 'Cannot renew Ads with status \'pending\', <a href="%s">cancel and go back</a>.', "wpadverts" );
        $adverts_flash["error"][] = sprintf( $format, $baseurl );
        ob_start();
        adverts_flash( $adverts_flash );
        return ob_get_clean();
    }
    
    $form["field"][] = array(
        "name" => "_listing_information",
        "type" => "adverts_field_header",
        "order" => 1000,
        "label" => __( 'Listing Information', 'wpadverts' )
    );
    
    $opts = array();
    $pricings = get_posts( array( 
        'post_type' => 'adverts-renewal',
        'post_status' => 'draft',
        'posts_per_page' => -1
    ) );
    
    $pricings = apply_filters( "wpadverts_filter_renewals", $pricings, $post->ID );
    
    adverts_form_add_field("adverts_payments_field_payment", array(
        "renderer" => "adverts_payments_field_payment",
        "callback_save" => "adverts_save_single",
        "callback_bind" => "adverts_bind_single",
    ) );
    
    foreach($pricings as $data) {
        
        if( get_post_meta( $data->ID, 'adverts_price', true ) ) {
            $adverts_price = adverts_price( get_post_meta( $data->ID, 'adverts_price', true ) );
        } else {
            $adverts_price = __("Free", "wpadverts");
        }
        
        $opts[] = array( "value"=>$data->ID, "text"=> $data->post_content );
    }

    $form = array(
        "name" => "advert-renew",
        "layout" => "stacked",
        "field" => array(
            array(
                "name" => "_adverts_renew",
                "type" => "adverts_field_hidden",
                "value" => "1",
                "order" => 1000
            ),
            array(
                "name" => "payments_listing_type",
                "type" => "adverts_payments_field_payment",
                "label" => null,
                "order" => 1001,
                "empty_option" => true,
                "options" => apply_filters( "wpadverts_filter_pricings_options", $opts ),
                "value" => "",
                "validator" => array(
                    array( "name" => "is_required" )
                )
            )
        )
    );
    
    include_once ADVERTS_PATH . 'includes/class-html.php';
    include_once ADVERTS_PATH . 'includes/class-form.php';
    
    $form_scheme = apply_filters( "adverts_form_scheme", $form, null );
    $form = new Adverts_Form( $form_scheme );
    $form_label_placement = "adverts-form-stacked";
    $buttons = array(
        array(
            "html" => "",
            "tag" => "input",
            "type" => "submit",
            "value" => __( "Renew", "wpadverts" ),
            "style" => "font-size:1.2em"
        )
    );
    
    if( isset( $_POST ) && ! empty( $_POST ) ) {
        $form->bind( stripslashes_deep( $_POST ) );
        $valid = $form->validate();
        
        if( $valid ) {
            
            
            wp_enqueue_script( 'adext-payments' );
            wp_enqueue_script( 'adverts-frontend' );
            
            $listing = get_post( $form->get_value( "payments_listing_type" ) );
            $price = get_post_meta( $listing->ID, 'adverts_price', true );
            
            $renewal_diff = current_time( 'timestamp' ) - strtotime( $post->post_date );
            $renewal_diff_min_days = apply_filters( "adverts_renewal_time_min", 7 );
            $renewal_diff_min = 3600 * 24 * $renewal_diff_min_days;
            
            if( $price > 0 ) {
                $m = __( 'Renew <strong>%s</strong> or <a href="%s">cancel and go back</a>.', 'wpadverts');
                $adverts_flash["info"][] = sprintf( $m, $post->post_title, $baseurl );
                
                $post_id = $post->ID;
                $listing_id = $listing->ID;
                
                $payment_id = adext_post_get_payment( $post_id, $listing_id, "pending" );

                if( ! $payment_id  ) {
                    $payment_id = adext_insert_payment( array(
                        "buyer_name" => get_post_meta( $post_id, "adverts_person", true ), 
                        "buyer_email" => get_post_meta( $post_id, "adverts_email", true ),           
                        "buyer_ip" => null,
                        "buyer_id" => get_current_user_id(),
                        "payment_status" => "pending",
                        "payment_type" => "adverts-payment",
                        "object_id" => $post_id,
                        "pricing_id" => $listing_id,
                        "payment_gateway" => adverts_config("payments.default_gateway"),
                        "payment_for" => "post",
                        "payment_paid" => 0
                    ) );
                }

                $payment = get_post( $payment_id );
                
                ob_start();
                // wpadverts/addons/payments/templates/add-payment.php
                include ADVERTS_PATH . 'addons/payments/templates/add-payment.php';
                return ob_get_clean();
            } else if( $price == 0 && $renewal_diff < $renewal_diff_min ) {
                $m = __( 'Free Renewals cannot be used more than once every %d days.', 'wpadverts');
                $adverts_flash["error"][] = sprintf( $m, $renewal_diff_min_days );
            } else {
                $m = __( 'Ad <strong>%s</strong> renewed. <a href="%s">Go back to Ads list</a>.', 'wpadverts');
                $adverts_flash["info"][] = sprintf( $m, $post->post_title, $baseurl );
                
                $payment_id = adext_insert_payment( array(
                    "payment_status" => "completed",
                    "object_id" => $post->ID,
                    "pricing_id" => $listing->ID,
                    "payment_gateway" => null,
                    "payment_for" => "post",
                    "payment_paid" => 0,
                ) );
                
                adext_payments_log($payment_id, __( "Free Renewal automatically marked as completed.", "wpadverts" ) );
                
                $payment = get_post( $payment_id );
                
                $post_id = $post->ID;
                $moderate = $post->post_status === 'pending' ? true : false;
                
                do_action( "wpadverts_advert_saved", $post_id );
                
                ob_start();
                // wpadverts/templates/add-payment.php
                include ADVERTS_PATH . '/templates/add-save.php';
                return ob_get_clean();
            }
        }
    } 
    
    $m1 = __( 'Renew <strong>%s</strong> or <a href="%s">cancel and go back</a>.', 'wpadverts');
    $m2 = __( 'Select renewal option and click "Renew" button.', 'wpadverts');
    $adverts_flash["info"][] = sprintf( $m1, $post->post_title, $baseurl ) . "<br/>" . sprintf( $m2, $baseurl );

    ob_start();
    // adverts/templates/form.php
    include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/form.php' );
    return ob_get_clean();
}

/**
 * Function initiates Payments module in wp-admin
 * 
 * @since 1.0
 */
function adext_payments_init_admin() {
    
    include_once ADVERTS_PATH . 'addons/payments/includes/admin-pages.php';
    include_once ADVERTS_PATH . 'addons/payments/includes/ajax.php';
    
    add_action( 'admin_menu', 'adext_payments_add_history_link');
    add_filter( 'display_post_states', 'adext_payments_display_pending_state' );
    add_action( 'admin_head', 'adext_payments_admin_head' );
    add_action( 'adext_payments_details_box', 'adext_payments_details_box' );
}

/**
 * Adds "Payment History" link to wp-admin menu.
 * 
 * @see admin_menu
 * @since 1.0
 */
function adext_payments_add_history_link() {
    
   $menu_page = apply_filters('adverts_menu_page', array(
        "parent_slug" => "edit.php?post_type=advert",
        "page_title" => __( 'Adverts Payment History', 'wpadverts' ),
        "menu_title" => __( 'Payment History', 'wpadverts' ),
        "capability" => "manage_options",
        "menu_slug" => 'adext-payment-history',
        "function" => "adext_payments_page_history"
    ));
    
    add_submenu_page(
        $menu_page["parent_slug"], 
        $menu_page["page_title"], 
        $menu_page["menu_title"], 
        $menu_page["capability"], 
        $menu_page["menu_slug"], 
        $menu_page["function"]
    );
    
}

/**
 * Payments Init
 * 
 * Payments module init functions, this function is executed when Adverts 
 * core is initiated.
 * 
 * @see adverts_core_init
 * @since 1.0
 */
function adext_payments_core_init() {
    
    include_once ADVERTS_PATH . 'addons/payments/includes/payment-actions.php';
    
    add_action("adverts_payment_completed", "adext_payment_completed_publish");
    add_action("adverts_payment_completed", "adext_payment_completed_renew");
    add_action("adverts_payment_completed", "adext_payment_completed_notify_user");
    add_action("adverts_payment_completed", "adext_payment_completed_notify_admin");
    
    if( class_exists( "Adext_Emails" ) ) {
        include_once ADVERTS_PATH . 'addons/payments/includes/class-emails-integration.php';
        new Adext_Payments_Emails_Integration();
    }
    
    do_action("adext_register_payment_gateway");
}

/**
 * Registers new payment method
 * 
 * @see Adverts
 * @since 1.0
 * 
 * @param string $name
 * @param array $data
 */
function adext_payment_gateway_add( $name, $data ) {
    
    $pg = Adverts::instance()->get("payment_gateways");

    if(!is_array($pg)) {
        $pg = array( $name => $data );
    } else {
        $pg[$name] = $data;
    }

    Adverts::instance()->set("payment_gateways", $pg);
}

/**
 * Returns payment gateway by $name, if $name is NULL then all payment
 * gateways are returned.
 * 
 * @see Adverts
 * @since 1.0
 * 
 * @param string $name
 * @return mixed
 */
function adext_payment_gateway_get( $name = null ) {
    $pg = Adverts::instance()->get("payment_gateways");
    
    if( $name === null ) {
        return $pg;
    } elseif( isset( $pg[$name] ) ) {
        return $pg[$name];
    } else {
        return null;
    }
}

/**
 * Adds message log to payment object.
 * 
 * @param string $name
 * @since 0.1
 * @return void
 */
function adext_payments_log( $payment_id, $message ) {
    
    $payment = get_post( $payment_id );
    
    if( $payment->post_type != 'adverts-payment' ) {
        return new WP_Error("Invalid Post Type.");
    }
    
    $pattern = apply_filters('adext_payments_log', '%1$s - %2$s');
    $log = sprintf( $pattern, current_time('mysql'), $message);
    
    wp_update_post( array(
        "ID" => $payment_id,
        "post_content" => $payment->post_content . "\r\n" . $log
    ));
}

/**
 * Display locked flag in [adverts_manage] shortcode
 * 
 * This function is being executed by adverts_sh_manage_list_status action, in
 * wpadverts/templates/manage.php
 * 
 * @since   1.1.0
 * @param   WP_Post     $post   Post for which we want to check the stataus
 * @return  void
 */
function adext_payments_manage_list_status( $post ) {

    if( $post->post_status != "advert-pending" ) {
        return;
    }
    
    ?>
    <span class="adverts-inline-icon adverts-inline-icon-warn adverts-icon-credit-card" title="<?php _e("Inactive — Waiting for payment.", "wpadverts") ?>"></span>
    <?php 
}

/**
 * Inserts or updates a Payment
 * 
 * If the $postarr parameter has 'ID' set to a value, then post will be updated.
 * 
 * @since 1.2.8
 * @param array $postarr {
 *      @type int    $ID                    The post ID. If equal to something other than 0,
 *                                          the post with that ID will be updated. Default 0.
 *      @type string $buyer_name            The buyer first and last name or company name
 *      @type string $buyer_email           The buyer email address
 *      @type string $buyer_ip              User IP Address
 *      @type int    $buyer_id              User ID (from wp_users table)
 *      @type string $payment_status        One of ("pending", "completed", "failed", "refunded")
 *      @type string $payment_type          For future use currently defaults to "adverts-payment"
 *      @type int    $object_id             ID of an Advert
 *      @type int    $pricing_id            ID of a selected Pricing
 *      @type string $payment_gateway       Payment Gateway name
 *      @type string $payment_for           For future use currently default to "post"
 *      @type float  $payment_paid          How much user already paid
 *      @type string $post_date_gmt         Payment creation date in YYYY-MM-DD H:i:s format
 * }
 * @return int|WP_Error The post ID on success. The WP_Error on failure.
 */
function adext_insert_payment( $postarr, $pricing = null ) {
    
    $defaults = array(
        "ID" => 0,
        "buyer_name" => "",
        "buyer_email" => "",
        "buyer_ip" => adverts_get_ip(),
        "buyer_id" => get_current_user_id(),
        "payment_status" => "pending",
        "payment_type" => "adverts-payment",
        "object_id" => null,
        "pricing_id" => null,
        "payment_gateway" => null,
        "payment_for" => "post",
        "payment_paid" => 0,
        "post_date_gmt" => current_time( 'mysql', true )
    );
    
    $data = array_merge( $defaults, $postarr );
    
    if( $data["ID"] > 0 ) {
        $old_status = get_post_status( $data["ID"] );
    } else {
        $old_status = "new";
    }
    
    if( $data["buyer_id"] > 0 ) {
        $user_info = get_userdata( $data["buyer_id"] );
    }
    
    if( empty( $data["buyer_name"] ) && $user_info ) {
        $data["buyer_name"] = trim( sprintf( "%s %s", $user_info->first_name, $user_info->last_name ) );
    }
    
    if( empty( $data["buyer_email"] ) && $user_info ) {
        $data["buyer_email"] = $user_info->user_email;
    }
    
    if( ! $pricing ) {
        $pricing = get_post( $data["pricing_id"] );
        $price = get_post_meta( $data["pricing_id"], "adverts_price", true );
    } else {
        $price = $pricing->price;
    }

    $payment_data = array(
        'post_title'    => $data['buyer_name'],
        'post_content'  => '',
        'post_status'   => $data['payment_status'],
        'post_type'     => $data['payment_type'],
        'post_date_gmt' => $data['post_date_gmt']
    );

    $meta = array(
        "pricing" => array(
            "post_title" => $pricing->post_title,
            "visible" => get_post_meta( $pricing->ID, "adverts_visible", true )
        ),
    );
    
    $data["meta"] = $meta;

    if( $data["ID"] > 0 ) {
        $payment_id = wp_update_post( $payment_data, true );
    } else {
        $payment_id = wp_insert_post( $payment_data, true );
    }
    
    if( is_wp_error( $payment_id ) ) {
        return $payment_id;
    }

    if( $data["payment_paid"] === "total" ) {
        $paid = $price;
    } else {
        $paid = $data["payment_paid"];
    }

    update_post_meta( $payment_id, 'adverts_person', $data['buyer_name'] );
    update_post_meta( $payment_id, 'adverts_email', $data['buyer_email'] );
    update_post_meta( $payment_id, '_adverts_user_ip', $data['buyer_ip'] );
    update_post_meta( $payment_id, '_adverts_user_id', $data['buyer_id'] );
    update_post_meta( $payment_id, '_adverts_object_id', $data["object_id"] );
    update_post_meta( $payment_id, '_adverts_pricing_id', $data["pricing_id"] );
    update_post_meta( $payment_id, '_adverts_payment_type', $pricing->post_type );
    update_post_meta( $payment_id, '_adverts_payment_gateway', $data["payment_gateway"] );
    update_post_meta( $payment_id, '_adverts_payment_for', $data["payment_for"] );
    update_post_meta( $payment_id, '_adverts_payment_paid', $paid );
    update_post_meta( $payment_id, '_adverts_payment_total', $price );
    update_post_meta( $payment_id, '_adverts_payment_meta', $meta );
    
    $new_status = $data["payment_status"];
    
    do_action( "adext_insert_payment", $payment_id, $postarr, $pricing );
    
    if( $old_status != $new_status ) {
        do_action( "adverts_payment_status_change", get_post( $payment_id ), $new_status, $old_status );
        do_action( "adverts_payment_{$new_status}", get_post( $payment_id ) );
        do_action( "adverts_payment_{$old_status}_to_{$new_status}", get_post( $payment_id ) );
    }

    
    
    return $payment_id;
}


function adext_get_payment_pricing( $payment ) {
    _deprecated_function(__FUNCTION__, "1.0");
    if( is_object( $payment ) ) {
        $payment_id = $payment->ID;
    } else {
        $payment_id = $payment;
        $payment = get_post( $payment_id );
    }
    
    $default_types = array( "adverts-pricing", "adverts-renewal" );
    
    if( in_array( $payment->post_type, $default_types ) ) {
        $pricing_id = get_post_meta( $payment_id, "_adverts_pricing_id", true );
        $pricing = get_post( $pricing_id );
    } 
    
    return apply_filters( "adext_get_payment_pricing", $pricing, $payment );
}

function adext_get_payment_pricing_price( $payment ) {
    _deprecated_function(__FUNCTION__, "1.0");
}

function adext_payments_get_payment_object( $payment ) {
    
    $object_id = get_post_meta( $payment->ID, '_adverts_object_id', true );
    $type = get_post_meta( $payment->ID, "_adverts_payment_for", true);
    $object = null;
    
    if( in_array( $type, array( "post", "renewal" ) ) ) {
        $object = get_post( $object_id );
    } else {
        $object = null;
    }
    
    return apply_filters( "adext_payments_get_payment_object", $object, $payment, $type );
}

function adext_payments_get_payment_pricing( $payment ) {
    
    $listing = get_post( get_post_meta( $payment->ID, "_adverts_pricing_id", true ) );
    $type = get_post_meta( $payment->ID, "_adverts_payment_for", true);

    if( in_array( $type, array( "post", "renewal" ) ) ) {
        $object = get_post( $listing->ID );
    } else {
        $object = null;
    }
    
    return apply_filters( "adext_payments_get_payment_pricing", $object, $payment, $type );
}

function adext_payments_payment_type( $item ) {
    
    $listing_id = get_post_meta( $item->ID, "_adverts_pricing_id", true );
    $listing = get_post( $listing_id );
      
    $payment_type = "—";
    
    if( $listing->post_type == "adverts-pricing" ) {
        $payment_type = __( "Posting", "wpadverts" ); 
    } elseif( $listing->post_type == "adverts-renewal" ) {
        $payment_type = __( "Renewal", "wpadverts" );
    } else {
               
    }
    
    return apply_filters( "adext_payments_payment_type", $payment_type, $item ); 
}

function adext_payments_details_box( $payment ) {
    
    $type = get_post_meta( $payment->ID, "_adverts_payment_for", true);
    
    if( ! in_array( $type, array( "post", "renewal" ) ) ) {
        return;
    }
    
    $listing_id = get_post_meta( $payment->ID, "_adverts_pricing_id", true );
    $listing = get_post( $listing_id );
    
    ?>
    <div class="inside " style="font-size:1.1em; clear:both; overflow:hidden">

        <div class="column" style="width:33%; float:left">
            <strong><?php _e("Purchase Type", "wpadverts") ?></strong><br/>
            <span>
                <?php echo adext_payments_payment_type( $payment ) ?>
            </span>
        </div>

        <div class="column" style="width:33%; float: left">
            <strong><?php _e("Listing Type", "wpadverts") ?></strong><br/>
            <?php if($listing): ?>
            <span>
                <a href="<?php echo admin_url('edit.php?post_type=advert&page=adverts-extensions&module=payments&adaction=list&edit='.$listing->ID) ?>"><?php esc_html_e($listing->post_title) ?></a> 
                <?php echo adverts_price( get_post_meta( $listing->ID, "adverts_price", true ) ) ?>
            </span>
            <?php else: ?>
            <?php esc_html_e( sprintf( __("Listing [%d] no longer exists.", "wpadverts"), $listing_id ) ) ?>
            <?php endif; ?>
        </div>

        <div class="column" style="width:33%; float: left">
            <strong><?php _e("Purchased Item", "wpadverts") ?></strong><br/>
            <?php $post_id = get_post_meta( $payment->ID, "_adverts_object_id", true ); ?>
            <?php $post = get_post( $post_id ) ?>
            <?php if($post): ?>
            <span>
                <a href="<?php echo admin_url('post.php?post='.$post->ID.'&action=edit') ?>"><?php esc_html_e($post->post_title) ?></a>
            </span>
            <?php else: ?>
            <?php esc_html_e( sprintf( __("Ad [%d] no longer exists.", "wpadverts"), $listing_id ) ) ?>
            <?php endif; ?>
        </div>

    </div><!-- /.inside -->
    
    <?php
}

/**
 * Checks if post has payment created
 * 
 * @since 1.3.0
 * @since 1.3.5       Added $post_type param
 * 
 * @param   int       $post_id          Post ID
 * @param   int       $listing_id       Pricing ID
 * @param   string    $post_status      Payment status (default 'pending')
 * @param   string    $post_type        Payment type (default 'post')
 * @return  int|bool                    The Payment ID or false
 */
function adext_post_get_payment( $post_id, $listing_id, $post_status = "pending", $post_type = "post" ) {
    $query = new WP_Query(array(
        "post_type" => "adverts-payment",
        "post_status" => $post_status,
        "orderby" => "date",
        "order" => "ASC",
        "posts_per_page" => 1,
        "meta_query" => array(
            array(
                "key" => '_adverts_object_id',
                "value" => $post_id
            ),
            array(
                "key" => '_adverts_pricing_id',
                "value" => $listing_id
            ),
            array(
                "key" => '_adverts_payment_for',
                "value" => $post_type
            )
        )
    ));
    
    if( isset( $query->posts[0] ) ) {
        return $query->posts[0]->ID;
    } else {
        return false;
    }
}

/**
 * Returns an URL to complete payment page
 * 
 * The passed argument needs to be a post with post_type = adverts-payment
 * 
 * @since   1.3.0
 * @param   WP_Post $post   Post object
 * @return  string          Complete Payment URL
 */
function adext_payments_get_checkout_url( $post = null ) {
    
    if( ! is_object( $post ) ) {
        $post = get_post( $post );
    }
    
    $hash = get_post_meta( $post->ID, '_adverts_frontend_hash', true );
    
    $url = "";
    $args = array( 'advert-hash' => $hash );
    
    if( adverts_config( "payments.checkout_page" ) ) {
        $url = get_permalink( adverts_config( "payments.checkout_page" ) );
    } else {
        $url = admin_url( "admin-ajax.php" );
        $args['action'] = 'adext_payments_complete_payment';
    }
    
    return add_query_arg( $args, $url );
}

/**
 * Returns formatted order id
 * 
 * This functions applies adext_payments_format_order_id filter which allows
 * changing the order ID formatting.
 * 
 * @see     adext_payments_format_order_id
 * 
 * @since   1.3.0
 * @param   WP_Post $post   Post object
 * @return  string          Formatted order id
 */
function adext_payments_format_order_id( $post ) {
    
    if( ! is_object( $post ) ) {
        $post_id = $post;
    } else {
        $post_id = $post->ID;
    }
    
    return apply_filters("adext_payments_format_order_id", "#".str_pad($post_id, 6, "0", STR_PAD_LEFT), $post_id );
}