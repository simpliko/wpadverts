<?php
/**
 * List of registered shortcodes
 * 
 * @package     Adverts
 * @copyright   Copyright (c) 2015, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Register shortcodes
add_shortcode('adverts_list', 'shortcode_adverts_list');
add_shortcode('adverts_add', 'shortcode_adverts_add');
add_shortcode('adverts_manage', 'shortcode_adverts_manage');
add_shortcode('adverts_categories', 'shortcode_adverts_categories');
add_shortcode('advert_single', 'shortcode_advert_single' );

// Shortcode functions

/**
 * Generates HTML for [adverts_list] shortcode
 * 
 * @param array $atts Shorcode attributes
 * @since 0.1
 * @return string Fully formatted HTML for adverts list
 */
function shortcode_adverts_list( $atts ) {

    wp_enqueue_style( 'adverts-frontend' );
    wp_enqueue_style( 'adverts-icons' );

    wp_enqueue_script( 'adverts-frontend' );

    $params = shortcode_atts(array(
        'name' => 'default',
        'author' => null,
        'redirect_to' => '',
        'search_bar' => adverts_config( 'config.ads_list_default__search_bar' ),
        'show_results' => true,
        'category' => null,
        'columns' => adverts_config( 'config.ads_list_default__columns' ),
        'display' => adverts_config( 'config.ads_list_default__display' ),
        'switch_views' => adverts_config( 'config.ads_list_default__switch_views' ),
        'allow_sorting' => 0,
        'order_by' => 'date-desc',
        'paged' => adverts_request("pg", 1),
        'posts_per_page' => adverts_config( 'config.ads_list_default__posts_per_page' ),
        'show_pagination' => true
    ), $atts, 'adverts_list' );

    extract( $params );

    if( is_numeric( $redirect_to ) ) {
        $action = get_permalink( $redirect_to );
    } else {
        $action = $redirect_to;
    }
    
    $taxonomy = null;
    $meta = array();
    $orderby = array();
    
    $query = adverts_request("query");
    $location = adverts_request("location");
    
    if($location) {
        $meta[] = array('key'=>'adverts_location', 'value'=>$location, 'compare'=>'LIKE');
    }

    if( is_string( $category) && $category == "current" && is_tax( "advert_category") ) {
        $category = get_queried_object_id();
    }
    if($category) {
        $taxonomy =  array(
            array(
                'taxonomy' => 'advert_category',
                'field'    => 'term_id',
                'terms'    => $category,
            ),
	);
    }

    if($allow_sorting && adverts_request("adverts_sort")) {
        $adverts_sort = adverts_request("adverts_sort");
    } else {
        $adverts_sort = $order_by;
    }
    
    // options: title, post_date, adverts_price
    $sort_options = apply_filters( "adverts_list_sort_options", array(
        "date" => array(
            "label" => __("Publish Date", "wpadverts"),
            "items" => array(
                "date-desc" => __("Newest First", "wpadverts"),
                "date-asc" => __("Oldest First", "wpadverts")
            )
        ),
        "price" => array(
            "label" => __("Price", "wpadverts"),
            "items" => array(
                "price-asc" => __("Cheapest First", "wpadverts"),
                "price-desc" => __("Most Expensive First", "wpadverts")
            )
        ),
        "title" => array(
            "label" => __("Title", "wpadverts"),
            "items" => array(
                "title-asc" => __("From A to Z", "wpadverts"),
                "title-desc" => __("From Z to A", "wpadverts")
            )
        )
    ) );
    
    $sarr = explode("-", $adverts_sort);
    $sort_current_text = __("Publish Date", "wpadverts");
    $sort_current_title = sprintf( __( "Sort By: %s - %s", "wpadverts"), __("Publish Date", "wpadverts"), __("Newest First", "wpadverts") );
    
    if( isset( $sarr[1] ) && isset( $sort_options[$sarr[0]]["items"][$adverts_sort] ) ) {

        $sort_key = $sarr[0];
        $sort_dir = $sarr[1];

        if($sort_dir == "asc") {
            $sort_dir = "ASC";
        } else {
            $sort_dir = "DESC";
        }

        if($sort_key == "title") {
            $orderby["title"] = $sort_dir;
        } elseif($sort_key == "date") {
            $orderby["date"] = $sort_dir;
        } elseif($sort_key == "price") {
            $orderby["adverts_price__orderby"] = $sort_dir;
            $meta["adverts_price__orderby"] = array(
                'key' => 'adverts_price',
                'type' => 'NUMERIC',
                'compare' => 'NUMERIC',
            );
        } else {
            // apply sorting using adverts_list_query filter.
        }

        $sort_current_text = $sort_options[$sort_key]["label"] ;
        $s_descr = $sort_options[$sort_key]["items"][$adverts_sort];
        $sort_current_title = sprintf( __( "Sort By: %s - %s", "wpadverts"), $sort_current_text, $s_descr );
    } else {
        $adverts_sort = $order_by;
        $orderby["date"] = "desc"; 
    }


    $args = apply_filters( "adverts_list_query", array( 
        'author' => $author,
        'post_type' => 'advert', 
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_page, 
        'paged' => $paged,
        's' => $query,
        'meta_query' => $meta,
        'tax_query' => $taxonomy,
        'orderby' => $orderby
    ), $params);

    if( ( $category || isset( $params["tax__advert_category"] ) ) && is_tax( 'advert_category' ) ) {
        $pbase = get_term_link( get_queried_object()->term_id, 'advert_category' );
    } else {
        $pbase = get_the_permalink();
    }

    $loop = new WP_Query( $args );
    $paginate_base = apply_filters( 'adverts_list_pagination_base', $pbase . '%_%' );
    $paginate_format = stripos( $paginate_base, '?' ) ? '&pg=%#%' : '?pg=%#%';

    include_once ADVERTS_PATH . 'includes/class-html.php';
    include_once ADVERTS_PATH . 'includes/class-form.php';

    if( $switch_views && in_array( adverts_request( "display", "" ), array( "grid", "list" ) ) ) {
        $display = adverts_request( "display" );
        add_filter( "adverts_form_load", "adverts_form_search_display_hidden" );
    }

    if( $display == "list" ) {
        $columns = 1;
    }
    
    if( adverts_request( "reveal_hidden" ) == "1" ) {
        add_filter( "adverts_form_load", "adverts_form_search_reveal_hidden" );
    }
    
    $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get("form_search"), $params );
    
    $form = new Adverts_Form( $form_scheme );
    $form->bind( stripslashes_deep( $_GET ) );
    
    $fields_hidden = array();
    $fields_visible = array();
    
    $counter = array(
        "visible-half" => 0,
        "visible-full" => 0,
        "hidden-half" => 0,
        "hidden-full" => 0
    );
    
    foreach($form->get_fields() as $field) {
        
        $search_group = "hidden";
        $search_type = "half";
        
        if( isset( $field['meta']["search_group"] ) ) {
            $search_group = $field['meta']['search_group'];
        }
        
        if( isset( $field['meta']['search_type'] ) ) {
            $search_type = $field['meta']['search_type'];
        }
        
        $counter[ $search_group . '-' . $search_type ]++;
        
        if( $search_type == 'full' ) {
            $field['adverts_list_classes'] = 'advert-input-type-full';
        } else if( $counter[ $search_group . '-' . $search_type ] % 2 == 0 ) {
            $field['adverts_list_classes'] = 'advert-input-type-half advert-input-type-half-right';
        } else {
            $field['adverts_list_classes'] = 'advert-input-type-half advert-input-type-half-left';
        }
        
        if( $search_group == "visible" ) {
            $fields_visible[] = $field;
        } else {
            $fields_hidden[] = $field;
        }
    }
    
    // adverts/templates/list.php
    ob_start();
    include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/list.php' );
    return ob_get_clean();
}

/**
 * 
 * Generates HTML for [adverts_add] shortcode
 * 
 * @since 0.1
 * @since 1.1.5 'requires' and 'requires_error' params
 * 
 * @param array $atts Shortcode attributes
 * @return string Fully formatted HTML for "post ad" form.
 */
function shortcode_adverts_add( $atts ) {
    
    include_once ADVERTS_PATH . 'includes/class-shortcode-adverts-add.php';
    
    $shortcode = new Adverts_Shortcode_Adverts_Add();
    return $shortcode->main($atts);
}

/**
 * Generates HTML for [adverts_manage] shortcode
 * 
 * @param array $atts Shortcode attributes
 * @since 0.1
 * @return string Fully formatted HTML for ads management panel.
 */
function shortcode_adverts_manage( $atts ) {
    
    if(!get_current_user_id()) {
        wp_enqueue_style( 'adverts-frontend' );
        wp_enqueue_style( 'adverts-icons' );
        
        ob_start();
        $permalink = get_permalink();
        $message = __('Only logged in users can access this page. <a href="%1$s">Login</a> or <a href="%2$s">Register</a>.', "wpadverts");
        $parsed = sprintf($message, wp_login_url( $permalink ), wp_registration_url( $permalink ) );
        adverts_flash( array( 
            "error" => array( 
                array( "message" => $parsed, "icon" => "adverts-icon-lock" ),  
            ),
            "info" => array() 
        ) );
        $content = ob_get_clean();
        return $content;
    }
    
    if( adverts_request("advert_id") ) {
        $action = "edit";
    } else {
        $action = adverts_request( "action", "" );
    }
    
    $action = apply_filters( "adverts_manage_action", $action );
    $content = "";

    if( $action == "" ) {
        $content = _adverts_manage_list( $atts );
    } else if( $action == "edit" ) {
        $content = _adverts_manage_edit( $atts );
    } else if( $action == "preview" ) {
        $content = _adverts_manage_preview( $atts );
    }
    
    return apply_filters("adverts_manage_action_$action", $content, $atts);
}

/**
 * Generates HTML for list of posted ads (in [adverts_manage] shortcode)
 * 
 * @param array $atts Shortcode attributes
 * @since 0.1
 * @return void 
 * @access private
 */
function _adverts_manage_list( $atts ) {
    
    wp_enqueue_style( 'adverts-frontend' );
    wp_enqueue_style( 'adverts-icons' );
    wp_enqueue_style( 'adverts-icons-animate' );

    wp_enqueue_script( 'adverts-frontend' );
    wp_enqueue_script( 'adverts-frontend-manage' );

    extract(shortcode_atts(array(
        'name' => 'default',
        'paged' => adverts_request("pg", 1),
        'posts_per_page' => 20,
    ), $atts));
    
    // Load ONLY current user data
    $loop = new WP_Query( apply_filters( "adverts_manage_query", array( 
        'post_type' => 'advert', 
        'post_status' => apply_filters("adverts_sh_manage_list_statuses", array('publish', 'advert-pending', 'pending', 'expired') ),
        'posts_per_page' => $posts_per_page, 
        'paged' => $paged,
        'author' => get_current_user_id()
    ) ) );

    $baseurl = apply_filters( "adverts_manage_baseurl", get_the_permalink() );
    $paginate_base = $baseurl . '%_%';
    $paginate_format = stripos( $paginate_base, '?' ) ? '&pg=%#%' : '?pg=%#%';
    $edit_format = stripos( $baseurl, '?' ) ? '&advert_id=%#%' : '?advert_id=%#%';

    // adverts/templates/manage.php
    ob_start();
    include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/manage.php' );
    return ob_get_clean();
} 

/**
 * Generates HTML for ad edit form (in [adverts_manage] shortcode)
 * 
 * @param array $atts Shortcode attributes
 * @since 0.1
 * @return void 
 * @access private
 */
function _adverts_manage_edit( $atts ) {
    
    wp_enqueue_style( 'adverts-frontend' );
    wp_enqueue_style( 'adverts-icons' );
    wp_enqueue_style( 'adverts-icons-animate' );

    wp_enqueue_script( 'adverts-frontend' );
    wp_enqueue_script( 'adverts-auto-numeric' );
    

    $params = shortcode_atts(array(
        'name' => 'default',
        'moderate' => false
    ), $atts, "adverts_manage");
    
    extract( $params );
    
    include_once ADVERTS_PATH . 'includes/class-html.php';
    include_once ADVERTS_PATH . 'includes/class-form.php';
    include_once ADVERTS_PATH . 'includes/class-checksum.php';
    
    $checksum_args = array(
        "requires-post-id" => 1,
        "form_name" => "advert",
        "name" => $params["name"],
        "moderate" => $params["moderate"]
    );
    
    $checksum = new Adverts_Checksum();
    $checksum_keys = $checksum->get_integrity_keys( $checksum_args );
    
    add_filter( 'adverts_form_load', 'adverts_remove_account_field' );
    
    $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get("form"), $params );
    $form = new Adverts_Form( $form_scheme );
    $valid = null;
    $error = array();
    $info = array();
    $bind = array();
    $flash = array( "error" => array(), "info" => array() );
    
    remove_filter( 'adverts_form_load', 'adverts_remove_account_field' );
    
    $action = apply_filters( 'adverts_action', adverts_request("_adverts_action", ""), __FUNCTION__ );
    $post_id = (adverts_request("advert_id", null));

    // $post_id hijack attempt protection here!

    $post = get_post( $post_id );
    
    if( $post === null) {
        $flash["error"][] = array(
            "message" =>  __("Ad does not exist.", "wpadverts"),
            "icon" => "adverts-icon-attention-alt"
        );
        ob_start();
        adverts_flash( $flash );
        return ob_get_clean();
    }

    if( $post->post_author != get_current_user_id() ) {
        $flash["error"][] = array(
            "message" =>  __("You do not own this Ad.", "wpadverts"),
            "icon" => "adverts-icon-attention-alt"
        );
        ob_start();
        adverts_flash( $flash );
        return ob_get_clean();
    }
    
    $slist = apply_filters("adverts_sh_manage_list_statuses", array( 'publish', 'expired', 'pending', 'advert-pending', 'draft') );
    
    if( !in_array( $post->post_status, $slist ) ) {
        $flash["error"][] = array(
            "message" =>  sprintf( __( "Incorrect post status [%s].", "wpadverts" ), $post->post_status ),
            "icon" => "adverts-icon-attention-alt"
        );
        ob_start();
        adverts_flash( $flash );
        return ob_get_clean();
    }
    
    $bind = Adverts_Post::get_form_data($post, $form);
    $bind["_adverts_action"] = "update";
    $bind["_post_id"] = $post_id;
    $bind["_post_id_nonce"] = wp_create_nonce( "wpadverts-publish-" . $post_id );
    $bind["_wpadverts_checksum"] = $checksum_keys["checksum"];
    $bind["_wpadverts_checksum_nonce"] = $checksum_keys["nonce"];

    $form->bind( $bind );
    
    if($action == "update") {
        
        $form->bind( (array)stripslashes_deep( $_POST ) );
        $valid = $form->validate();

        if($valid) {
            
            $init = array();
            
            if( adverts_config( "adverts_manage_moderate") == "1" ) {
                $init["post"] = array( "post_status" => "pending" );
            }
            
            $post_id = Adverts_Post::save( $form, $post_id, $init );

            if(is_wp_error($post_id)) {
                $error[] = array(
                    "message" => $post_id->get_error_message(),
                    "icon" => "adverts-icon-attention-alt"
                );
            } else {
                adverts_force_featured_image( $post_id );
                
                $info[] = array(
                    "message" => __("Post has been updated.", "wpadverts"),
                    "icon" => "adverts-icon-ok"
                );
            }
            
        } else {
            $error[] = array(
                "message" => __("Cannot update. There are errors in your form.", "wpadverts"),
                "icon" => "adverts-icon-attention-alt"
            );
        }
    }
    
    $adverts_flash = array( "error" => $error, "info" => $info );
    $baseurl = apply_filters( "adverts_manage_baseurl", get_the_permalink() );
    $actions_class = "adverts-field-actions";
    
    // adverts/templates/manage-edit.php
    ob_start();
    include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/manage-edit.php' );
    return ob_get_clean();
}

function _adverts_manage_preview( $atts = array() ) {

    wp_enqueue_style( 'adverts-frontend' );
    wp_enqueue_style( 'adverts-icons' );
    wp_enqueue_style( 'adverts-icons-animate' );

    wp_enqueue_script( 'adverts-frontend' );

    $post_id = intval( adverts_request( "preview_id" ) );

    $post = get_post( $post_id );
    
    if( $post === null) {
        $flash["error"][] = array(
            "message" =>  __("Ad does not exist.", "wpadverts"),
            "icon" => "adverts-icon-attention-alt"
        );
        ob_start();
        adverts_flash( $flash );
        return ob_get_clean();
    }

    if( $post->post_author != get_current_user_id() ) {
        $flash["error"][] = array(
            "message" =>  __("You do not own this Ad.", "wpadverts"),
            "icon" => "adverts-icon-attention-alt"
        );
        ob_start();
        adverts_flash( $flash );
        return;
    }
    
    $slist = apply_filters("adverts_sh_manage_list_statuses", array( 'publish', 'expired', 'pending', 'advert-pending', 'draft') );
    
    if( !in_array( $post->post_status, $slist ) ) {
        $flash["error"][] = array(
            "message" =>  sprintf( __( "Incorrect post status [%s].", "wpadverts" ), $post->post_status ),
            "icon" => "adverts-icon-attention-alt"
        );
        adverts_flash( $flash );
        return;
    }

    $go_back = add_query_arg( array( "action" => false, "preview_id" => false, "advert_id" => $post_id ) );
    $flash["warn"][] = array(
        "message" =>  sprintf( __( 'You are seeing a post preview. This page is not publicly available. <a href="%s">Go back</a>', "wpadverts" ), $go_back ),
        "icon" => "adverts-icon-eye-off"
    );

    remove_action( 'adverts_tpl_single_bottom', 'adverts_single_contact_information' );
    remove_action( 'adverts_tpl_single_bottom', 'adext_contact_form' );
    remove_action( 'adverts_tpl_single_bottom', 'adext_bp_send_private_message_button', 50 );
    
    ob_start();
    adverts_flash( $flash );
    echo do_shortcode( sprintf( '[advert_single post_id="%d"]', $post_id ));
    return ob_get_clean();
}

/**
 * Generates HTML for [adverts_categories] shortcode
 * 
 * @param array $atts Shortcode attributes
 * @since 0.3
 * @return string Fully formatted HTML for "categories" form.
 */
function shortcode_adverts_categories( $atts ) {
    
    extract(shortcode_atts(array(
        'name' => 'default',
        'show' => 'top',
        'columns' => 4,
        'default_icon' => 'adverts-icon-folder',
        'show_count' => true,
        'sub_count' => 5
    ), $atts));
    
    $columns = "adverts-flexbox-columns-" . (int)$columns;
    
    if($show != 'top') {
        $show = 'all';
    }
    
    $terms = get_terms( apply_filters( 'adverts_categories_query', array( 
        'taxonomy' => 'advert_category',
        'hide_empty' => 0, 
        'parent' => null, 
    ), $atts ) );
    
    wp_enqueue_style( 'adverts-frontend');
    wp_enqueue_style( 'adverts-icons' );

    ob_start();
    // adverts/templates/categories.php
    include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/categories-'.$show.'.php' );
    return ob_get_clean();
}

/**
 * Flash message shortcode
 * 
 * Wrapper for adverts_flash function. Catches the content generated by adverts_flash() 
 * and returns it as a string.
 * 
 * @see adverts_flash()
 * 
 * @since   1.4.0
 * @param   array       $data   Flash array ["info"=>[ 0=>[ "icon"=>"adverts-icon-xxx", "message"=>"..."], ...], "error"=>[...]]
 * @return  string              Flash message content
 */
function shortcode_adverts_flash( $data ) {
    ob_start();
    adverts_flash( $data );
    return ob_get_clean();
} 

/**
 * Renders flash messages
 * 
 * @param array $data
 * @since 0.1
 * @return void
 */
function adverts_flash( $data ) {

    $data = apply_filters( "adverts_flash_data", $data );
    
    ?>

    <?php foreach(array_keys($data) as $key): ?>
    <?php if(isset($data[$key]) && is_array($data[$key]) && !empty($data[$key])): ?>
    <div class="adverts-flash-messages adverts-flash-<?php echo esc_attr($key) ?>">
    <?php foreach( $data[$key] as $key => $info): ?>
        <?php if(is_string($info)) {
            $info = array( "message" => $info, "icon" => "");
        } ?>
        
        <div class="adverts-flash-single">
            <?php if($info["icon"]): ?>
            <span class="adverts-flash-message-icon <?php echo esc_html($info["icon"]) ?>"></span>
            <span class="adverts-flash-message-text adverts-flash-padding"><?php echo $info["message"] ?></span>
            <?php else: ?>
            <span class="adverts-flash-message-text"><?php echo $info["message"] ?></span>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>

    <?php
}

/**
 * Generates HTML for [advert_single] shortcode
 * 
 * @since   1.5.8
 * @param   array       $atts   Shortcode params
 * @return  string              Single Advert Page HTML
 */
function shortcode_advert_single( $atts = array() ) {

    extract( shortcode_atts( array(
        'post_id' => get_the_ID(),
    ), $atts ) );

    ob_start();
        
    $post_content = get_post( $post_id )->post_content;
    $post_content = wp_kses($post_content, wp_kses_allowed_html( 'post' ) );
    $post_content = apply_filters( "adverts_the_content", $post_content );
        
    include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/single.php' );

    $content = ob_get_clean();

    return $content;
}