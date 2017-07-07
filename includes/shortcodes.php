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
    $orderby = array('menu_order'=>'DESC');
    
    $query = adverts_request("query");
    $location = adverts_request("location");
    
    if($location) {
        $meta[] = array('key'=>'adverts_location', 'value'=>$location, 'compare'=>'LIKE');
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
            "label" => __("Publish Date", "adverts"),
            "items" => array(
                "date-desc" => __("Newest First", "adverts"),
                "date-asc" => __("Oldest First", "adverts")
            )
        ),
        "price" => array(
            "label" => __("Price", "adverts"),
            "items" => array(
                "price-asc" => __("Cheapest First", "adverts"),
                "price-desc" => __("Most Expensive First", "adverts")
            )
        ),
        "title" => array(
            "label" => __("Title", "adverts"),
            "items" => array(
                "title-asc" => __("From A to Z", "adverts"),
                "title-desc" => __("From Z to A", "adverts")
            )
        )
    ) );
    
    $sarr = explode("-", $adverts_sort);
    $sort_current_text = __("Publish Date", "adverts");
    $sort_current_title = sprintf( __( "Sort By: %s - %s", "adverts"), __("Publish Date", "adverts"), __("Newest First", "adverts") );
    
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
                'compare' => 'NUMERIC',
            );
        } else {
            // apply sorting using adverts_list_query filter.
        }

        $sort_current_text = $sort_options[$sort_key]["label"] ;
        $s_descr = $sort_options[$sort_key]["items"][$adverts_sort];
        $sort_current_title = sprintf( __( "Sort By: %s - %s", "adverts"), $sort_current_text, $s_descr );
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
    
    if( $category && is_tax( 'advert_category' ) ) {
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
    wp_enqueue_style( 'adverts-frontend' );
    wp_enqueue_style( 'adverts-icons' );
    wp_enqueue_style( 'adverts-icons-animate' );

    wp_enqueue_script( 'adverts-frontend' );
    wp_enqueue_script( 'adverts-auto-numeric' );
    
    $params = shortcode_atts(array(
        'name' => 'default',
        'moderate' => false,
        'requires' => "",
        'requires_error' => ""
    ), $atts, 'adverts_add');
    
    if( ! empty( $params["requires"] ) && ! current_user_can( $params["requires"] ) ) {
        

        if( !empty( $params["requires_error"] ) ) {
            $parsed = $params["requires_error"];
        } else {
            $permalink = get_permalink();
            $message = __('Only logged in users can access this page. <a href="%1$s">Login</a> or <a href="%2$s">Register</a>.', "adverts");
            $parsed = sprintf($message, wp_login_url( $permalink ), wp_registration_url( $permalink ) );

        }
        
        $adverts_flash = array(
            "error" => array(
                array( 
                    "message" => $parsed,
                    "icon" => "adverts-icon-lock"
                )
            ),
            "info" => array()
        );
        ob_start();
        adverts_flash($adverts_flash);
        return ob_get_clean();
    }
    
    extract( $params );
    
    include_once ADVERTS_PATH . 'includes/class-html.php';
    include_once ADVERTS_PATH . 'includes/class-form.php';

    $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get("form"), $params );
    
    $form = new Adverts_Form( $form_scheme );
    $valid = null;
    $error = array();
    $info = array();
    $bind = array();
    $content = "";
    $adverts_flash = array( "error" => array(), "info" => array() );
    
    $action = apply_filters( 'adverts_action', adverts_request("_adverts_action", ""), __FUNCTION__ );
    $post_id = (adverts_request("_post_id", null));
    $post_id = ($post_id>0) ? $post_id : null;
    
    // $post_id hijack attempt protection here!
    
    if( $post_id>0 && get_post( $post_id )->post_author == get_current_user_id() ) {
        
        // if post was already saved in DB (for example for preview) then load it.
        $post = get_post( $post_id );
        
        // bind data by field name
        foreach( $form->get_fields() as $f ) {
            $value = get_post_meta( $post_id, $f["name"], false );
            if( empty( $value ) ) {
                $bind[$f["name"]] = "";
            } else if( count( $value ) == 1 ) {
                $bind[$f["name"]] = $value[0];
            } else {
                $bind[$f["name"]] = $value;
            }
        }
        
        $bind["post_title"] = $post->post_title;
        $bind["post_content"] = $post->post_content;
        $bind["advert_category"] = array();
        
        $terms = get_the_terms( $post_id, 'advert_category' );
        
        if(is_array($terms)) {
            foreach($terms as $term) {
                $bind["advert_category"][] = $term->term_id;
            }
        }
        
    } elseif( is_user_logged_in() ) {
        $bind["adverts_person"] = wp_get_current_user()->display_name;
        $bind["adverts_email"] = wp_get_current_user()->user_email;
    }
    
    if($action == "") {
        // show post ad form page
        wp_enqueue_style( 'adverts-frontend-add' );
        
        $bind["_post_id"] = $post_id;
        $bind["_adverts_action"] = "preview";
        
        $form->bind( $bind );
        
        // adverts/templates/add.php
        ob_start();
        include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/add.php' );
        $content = ob_get_clean();
        
    } elseif($action == "preview") {
        // show preview page
        wp_enqueue_style( 'adverts-frontend-add' );

        $form->bind( (array)stripslashes_deep( $_POST ) );
        $valid = $form->validate();

        $adverts_flash = array( "error" => $error, "info" => $info );
        
        // Allow to preview only if data in the form is valid.
        if($valid) {
            
            $init = array(
                "post" => array(
                    "ID" => $post_id,
                    "post_name" => sanitize_title( $form->get_value( "post_title" ) ),
                    "post_type" => "advert",
                    "post_author" => get_current_user_id(),
                    "post_date" => current_time( 'mysql' ),
                    "post_date_gmt" => current_time( 'mysql', 1 ),
                    "post_status" => adverts_tmp_post_status(),
                    "guid" => ""
                ),
                "meta" => array()
            );
            
            if( adverts_config( "config.visibility" ) > 0 ) {
                $init["meta"]["_expiration_date"] = array(
                    "value" => strtotime( current_time('mysql') . " +". adverts_config( "config.visibility" ) ." DAYS" ),
                    "field" => array(
                        "type" => "adverts_field_hidden"
                    )
                );
            }
            
            // Save post as temporary in DB
            $post_id = Adverts_Post::save($form, $post_id, $init);
            $post_content = apply_filters("the_content", get_post( $post_id )->post_content );
            
            if(is_wp_error($post_id)) {
                $error[] = $post_id->get_error_message();
                $valid = false;
            } 
            
            $adverts_flash = array( "error" => $error, "info" => $info );
            
            // adverts/templates/add-preview.php
            ob_start();
            include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/add-preview.php' );
            $content = ob_get_clean();
            
        } else {
            $error[] = array(
                "message" => __("There are errors in your form. Please correct them before proceeding.", "adverts"),
                "icon" => "adverts-icon-attention-alt"
            );
            
            $adverts_flash = array( "error" => $error, "info" => $info );
            
            // adverts/templates/add.php
            ob_start();
            include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/add.php' );
            $content = ob_get_clean();
            
        } // endif $valid

    } elseif( $action == "save") {
        
        // Save form in the database
        $post_id = wp_update_post( array(
            "ID" => $post_id,
            "post_status" => $moderate == "1" ? 'pending' : 'publish',
        ));
        
        $info[] = __("Thank you for submitting your ad!", "adverts");
        
        $adverts_flash = array( "error" => $error, "info" => $info );

        if( !is_user_logged_in() && get_post_meta( $post_id, "_adverts_account", true) == 1 ) {
            adverts_create_user_from_post_id( $post_id, true );
        }
    
        
        // adverts/templates/add-save.php
        ob_start();
        include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/add-save.php' );
        $content = ob_get_clean();
        
    }
    
    return apply_filters("adverts_action_$action", $content, $form);
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
        $message = __('Only logged in users can access this page. <a href="%1$s">Login</a> or <a href="%2$s">Register</a>.', "adverts");
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
        $action = "";
    }
    
    $action = apply_filters( "adverts_manage_action", $action );
    
    ob_start();
    if( $action == "" ) {
        $content = _adverts_manage_list( $atts );
    } else if( $action == "edit" ) {
        $content = _adverts_manage_edit( $atts );
    } 
    $content = ob_get_clean();
    
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
    $loop = new WP_Query( array( 
        'post_type' => 'advert', 
        'post_status' => apply_filters("adverts_sh_manage_list_statuses", array('publish', 'advert-pending', 'expired') ),
        'posts_per_page' => $posts_per_page, 
        'paged' => $paged,
        'author' => get_current_user_id()
    ) );

    $baseurl = apply_filters( "adverts_manage_baseurl", get_the_permalink() );
    $paginate_base = $baseurl . '%_%';
    $paginate_format = stripos( $paginate_base, '?' ) ? '&pg=%#%' : '?pg=%#%';
    $edit_format = stripos( $baseurl, '?' ) ? '&advert_id=%#%' : '?advert_id=%#%';

    // adverts/templates/manage.php
    include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/manage.php' );
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

    
    
    add_filter( 'adverts_form_load', 'adverts_remove_account_field' );
    
    $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get("form"), $params );
    $form = new Adverts_Form( $form_scheme );
    $valid = null;
    $error = array();
    $info = array();
    $bind = array();
    
    remove_filter( 'adverts_form_load', 'adverts_remove_account_field' );
    
    $action = apply_filters( 'adverts_action', adverts_request("_adverts_action", ""), __FUNCTION__ );
    $post_id = (adverts_request("advert_id", null));

    // $post_id hijack attempt protection here!

    $post = get_post( $post_id );
    
    if( $post === null) {
        $error[] = __("Ad does not exist.", "adverts");
        adverts_flash( array("error"=>$error) );
        return;
    }
    
    if( $post->post_author != get_current_user_id() ) {
        $error[] = __("You do not own this Ad.", "adverts");
        adverts_flash( array("error"=>$error) );
        return;
    }
    
    $slist = apply_filters("adverts_sh_manage_list_statuses", array( 'publish', 'expired', 'advert-pending', 'draft') );
    
    if( !in_array( $post->post_status, $slist ) ) {
        $error[] = sprintf( __( "Incorrect post status [%s].", "adverts" ), $post->post_status );
        adverts_flash( array("error"=>$error) );
        return;
    }
    
    foreach( $form->get_fields() as $f ) {
        $value = get_post_meta( $post_id, $f["name"], false );
        if( empty( $value ) ) {
            $bind[$f["name"]] = "";
        } else if( count( $value) == 1 ) {
            $bind[$f["name"]] = $value[0];
        } else {
            $bind[$f["name"]] = $value;
        }
    }
    
    $bind["_adverts_action"] = "update";
    $bind["_post_id"] = $post_id;
    
    $bind["post_title"] = $post->post_title;
    $bind["post_content"] = $post->post_content;
    $bind["advert_category"] = array();

    $taxonomy_objects = get_object_taxonomies( 'advert', 'objects' );
    foreach( $taxonomy_objects as $taxonomy_key => $taxonomy ) {
        $terms = get_the_terms( $post_id, $taxonomy_key );
        if( is_array( $terms ) ) {
            foreach( $terms as $term ) {
                if(!isset($bind[$taxonomy_key]) || !is_array($bind[$taxonomy_key])) {
                    $bind[$taxonomy_key] = array();
                }
                $bind[$taxonomy_key][] = $term->term_id;
            }
        }
    }
    
    $form->bind( $bind );
    
    if($action == "update") {
        
        $form->bind( (array)stripslashes_deep( $_POST ) );
        $valid = $form->validate();

        if($valid) {
            
            $post_id = Adverts_Post::save( $form, $post_id );

            if(is_wp_error($post_id)) {
                $error[] = $post_id->get_error_message();
            } else {
                $info[] = __("Post has been updated.", "adverts");
            }
            
        } else {
            $error[] = __("Cannot update. There are errors in your form.", "adverts");
        }
    }
    
    $adverts_flash = array( "error" => $error, "info" => $info );
    $baseurl = apply_filters( "adverts_manage_baseurl", get_the_permalink() );
    
    // adverts/templates/manage-edit.php
    include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/manage-edit.php' );
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
    
    $terms = get_terms( 'advert_category', array( 
        'hide_empty' => 0, 
        'parent' => null, 
    ) );
    
    wp_enqueue_style( 'adverts-frontend');
    wp_enqueue_style( 'adverts-icons' );

    ob_start();
    // adverts/templates/categories.php
    include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/categories-'.$show.'.php' );
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
