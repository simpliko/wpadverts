<?php
/*
 * Plugin Name: WP Adverts
 * Plugin URI: https://wpadverts.com/
 * Description: The lightweight WordPress classifieds plugin done right.
 * Author: Greg Winiarski
 * Text Domain: wpadverts
 * Version: 1.5.3
 * 
 * Adverts is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Adverts is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Adverts. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Adverts
 * @category Core
 * @author Grzegorz Winiarski
 * @version 1.0.0
 */

if( !defined("ADVERTS_FILE") ) {
    define( "ADVERTS_FILE", __FILE__ );
    define( "ADVERTS_PATH", plugin_dir_path( ADVERTS_FILE ) );
    define( "ADVERTS_URL", plugins_url() . "/" . basename(ADVERTS_PATH) );
}

// init global variables
global $wpadverts, $adverts_config, $adverts_namespace;

// define global $wpadverts variable
$wpadverts = null;

// define global $adverts_config variable
$adverts_config = null;

// define global $adverts_namespace variable
$adverts_namespace = array();

$adverts_namespace['config'] = array(
    'option_name' => 'adverts_config',
    'default' => array(
        'module' => array(),
        'license' => array(),
        'currency_code' => 'USD',
        'currency_sign' => '$',
        'currency_sign_type' => 'p', // either p=prefix or s=suffix
        'currency_decimals' => 2,
        'currency_char_decimal' => '.',
        'currency_char_thousand' => ',',
        'visibility' => 30,
        'ads_list_id' => null,
        'ads_list_default__search_bar' => 'enabled',
        'ads_list_default__columns' => 2,
        'ads_list_default__display' => 'grid',
        'ads_list_default__switch_views' => 0,
        'ads_list_default__posts_per_page' => 20,
        'expired_ad_status' => '404', // one of: 404, 200, 301
        'expired_ad_redirect_url' => '',
        'expired_ad_public_cap' => 'edit_pages',
        'empty_price' => '',
        'hide_images_in_media_library' => 0,
        'adverts_manage_moderate' => 0
        
    )
);

$adverts_namespace['gallery'] = array(
    'option_name' => 'adverts_gallery',
    'default' => array(
        'ui' => 'pagination', // either paginator or thumbnails
        'visible_items' => 5,
        'scrolling_items' => 1,
        'lightbox' => 1,
        'image_fit' => '',
        'image_edit_cap' => 'read',
        'image_sizes' => array(
            // supported sizes: adverts-upload-thumbnail, adverts-list, adverts-gallery
            "adverts-gallery" => array( 'enabled' => 1, 'width' => 650, 'height' => 300, 'crop' => false ),
            "adverts-list" => array( 'enabled' => 1, 'width' => 310, 'height' => 190, 'crop' => true ),
            "adverts-upload-thumbnail" => array( 'enabled' => 1, 'width' => 150, 'height' => 105, 'crop' => true ),
            //"adverts-gallery-thumbnail"
        ),
    )
);

/**
 * Main Adverts Init Function
 * 
 * Registers: custom post types, additional post statuses, taxonomies, image sizes
 * and Adverts scripts.
 * 
 * @global WP_Embed $wp_embed
 * @global WP_Rewrite $wp_rewrite
 * @since 0.1
 * @return void
 */
function adverts_init() {
    global $wp_embed, $wpadverts;
    
    add_filter( 'adverts_the_content', array( $wp_embed, 'autoembed' ), 8 );
    add_filter( 'adverts_the_content', 'wptexturize' );
    add_filter( 'adverts_the_content', 'convert_chars' );
    add_filter( 'adverts_the_content', 'wpautop' );
    
    add_action( 'save_post_advert', 'adverts_create_hash', 10, 3 );
    add_action( 'deleted_post', 'adverts_deleted_post', 10, 2 );
    
    add_filter( 'adverts_form_load', 'adverts_form_load_checksum_fields', 9999 );
    
    wp_register_style( 'wpadverts-autocomplete', ADVERTS_URL . '/assets/css/wpadverts-autocomplete.css', array(), "1.4.5" );
    wp_register_style( 'adverts-upload', ADVERTS_URL . '/assets/css/wpadverts-upload.css', array(), "1.3.5" );
    wp_register_style( 'adverts-icons', ADVERTS_URL . '/assets/css/wpadverts-glyphs.css', array(), "4.7.2" );
    wp_register_style( 'adverts-icons-animate', ADVERTS_URL . '/assets/css/animation.css', array(), "1.3.5" );
    
    wp_register_script('adverts-form', ADVERTS_URL . '/assets/js/wpadverts-form.js', array( 'jquery' ), "1.5.2" );
    
    load_plugin_textdomain("wpadverts", false, dirname(plugin_basename(__FILE__))."/languages/");
    
    include_once ADVERTS_PATH . 'includes/class-adverts.php';
    $wpadverts = Adverts::instance();

    include_once ADVERTS_PATH . 'includes/functions.php';
    
    $expired_status = adverts_config( 'expired_ad_status' );
    $public_cap = adverts_config( 'expired_ad_public_cap' );
    if( is_admin() ) {
        // current user is viewing admin panel
        $expired_is_public = is_admin();
    } else if( $public_cap && current_user_can( $public_cap ) ) {
        // current user can always see expired Ads.
        $expired_is_public = true;
        add_action( "wp", "adverts_handle_expired_ads" );
    } else if( $expired_status == '404' ) {
        // show 404 page
        $expired_is_public = false;
    } else if( $expired_status == '301' ) {
        // redirect (301) to some other page.
        $expired_is_public = true;
        add_action( "template_redirect", "adverts_template_redirect_expired" );
    } else {
        // most likely 200
        $expired_is_public = true;
        add_action( "wp", "adverts_handle_expired_ads" );
    }

    if( adverts_config( 'hide_images_in_media_library' ) == 1 ) {
        add_action( "ajax_query_attachments_args", "adverts_query_attachments_args" );
    }
    
    register_post_status( 'expired', array(
        'label'          => _x( 'Expired', 'post' ),
        'public'         => $expired_is_public,
        'internal'       => false,
        'label_count'    => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>' )
     ) );
    
    register_post_status( 'advert_tmp', array(
        'label'          => _x( 'Temporary', 'post' ),
        'public'         => false,
        'internal'       => false,
        'show_in_admin_all_list' => false,
        'label_count'    => _n_noop( 'Temporary <span class="count">(%s)</span>', 'Temporary <span class="count">(%s)</span>' )
     ) );

    $labels = array(
        'name'               => _x( 'Classifieds', 'post type general name', 'wpadverts' ),
        'singular_name'      => _x( 'Classified', 'post type singular name', 'wpadverts' ),
        'add_new'            => _x( 'Add New', 'classified', 'wpadverts' ),
        'add_new_item'       => __( 'Add New Classified', 'wpadverts' ),
        'edit_item'          => __( 'Edit Classified', 'wpadverts' ),
        'new_item'           => __( 'New Classified', 'wpadverts' ),
        'all_items'          => __( 'All Classifieds', 'wpadverts' ),
        'view_item'          => __( 'View Classified', 'wpadverts' ),
        'search_items'       => __( 'Search Classifieds', 'wpadverts' ),
        'not_found'          => __( 'No Classifieds found', 'wpadverts' ),
        'not_found_in_trash' => __( 'No Classifieds found in the Trash', 'wpadverts' ), 
        'parent_item_colon'  => '',
        'menu_name'          => __( 'Classifieds', 'wpadverts' )
    );
    
    $args = array(
        'labels'        => $labels,
        'description'   => '',
        'public'        => true,
        'menu_icon'     => 'dashicons-megaphone',
        'menu_position' => 5,
        'supports'      => array( 'title', 'editor', 'author' ),
        'taxonomies'    => array( 'advert_category' ),
        'has_archive'   => true,
        'rewrite'       => array(
            'slug'          => 'advert',
            'with_front'    => false,
            'feeds'         => true
        )
    );
  
    include_once ADVERTS_PATH . "/includes/class-types.php";
    $adverts_types = new Adverts_Types();

    register_post_type( 'advert', apply_filters( 'adverts_post_type', $args, 'advert') ); 
    
    $labels = array(
        'name'                       => _x( 'Adverts Categories', 'taxonomy general name', 'wpadverts' ),
        'singular_name'              => _x( 'Advert Category', 'taxonomy singular name', 'wpadverts' ),
        'menu_name'                  => __( 'Categories', 'wpadverts' ),
    );
    
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'advert-category')
    );
    
    register_taxonomy( 
        'advert_category', 
        apply_filters('adverts_register_taxonomy_post_type', 'advert', 'advert_category' ), 
        apply_filters('adverts_register_taxonomy', $args, 'advert_category') 
    );
    
    include_once ADVERTS_PATH . 'includes/class-adverts.php';
    include_once ADVERTS_PATH . 'includes/class-taxonomies.php';
    include_once ADVERTS_PATH . 'includes/class-flash.php';
    include_once ADVERTS_PATH . 'includes/class-post.php';
    include_once ADVERTS_PATH . 'includes/events.php';
    include_once ADVERTS_PATH . 'includes/functions.php';
    
    include_once ADVERTS_PATH . 'includes/defaults.php';

    foreach( adverts_config( "gallery.image_sizes" ) as $image_key => $image_size ) {
        if( $image_size["enabled"] ) {
            add_image_size( $image_key, $image_size["width"], $image_size["height"], $image_size["crop"] );
        }
    }
    
    $currency = Adverts::instance()->get("currency");
    
    Adverts::instance()->set( "taxonomies", new Adverts_Taxonomies() );
    
    wp_register_script( 
        'adverts-auto-numeric', 
        ADVERTS_URL  .'/assets/js/auto-numeric.js', 
        array( 'jquery' ), 
        "1", 
        true
    );
    
    wp_register_script( 
        'adverts-autocomplete', 
        ADVERTS_URL . '/assets/js/wpadverts-autocomplete.js', 
        array( 'jquery' ), 
        "1.4.4", 
        true
    );
    
    wp_register_script( 
        'adverts-multiselect', 
        ADVERTS_URL . '/assets/js/wpadverts-multiselect.js', 
        array( 'jquery' ), 
        "1.3.5", 
        true
    );
    
    wp_register_script( 
        'adverts-gallery', 
        ADVERTS_URL . '/assets/js/wpadverts-gallery.js', 
        array( 'jquery', 'plupload-all', 'jquery-ui-sortable', 'jquery-effects-core', 'jquery-effects-fade', 'wp-util', 'jcrop'  ), 
        "1.5.1", 
        true
    );
    
    wp_register_script(
        'adverts-slick',
        ADVERTS_URL . '/assets/js/slick.min.js',
        array( 'jquery' ),
        "1.8.1",
        true
    );

    wp_localize_script( 'adverts-auto-numeric', 'adverts_currency', array(
        "aSign" => $currency["sign"], 
        "pSign" => $currency["sign_type"],
        "aSep" => $currency["char_thousand"], 
        "aDec" => $currency["char_decimal"],
        "mDec" => $currency["decimals"]
    ));
    
    wp_localize_script( 'adverts-multiselect', 'adverts_multiselect_lang', array(
        "hint" => __("Select options ...", "wpadverts")
    ));
    
    wp_localize_script( 'adverts-autocomplete', 'adverts_autocomplete_lang', array(
        "ajaxurl" => adverts_ajax_url(),
        "no_results" => __( "No results found.", "wpadverts" ),
        "open" => __( "Open", "wpadverts" ),
        "close" => __( "Close", "wpadverts" ),
        "ok" => __( "OK", "wpadverts" ),
        "cancel" => __( "Cancel", "wpadverts" ),
        "selected" => __( "Selected", "wpadverts" ),
        "max_choices" => __( "Cannot select more than %s items.", "wpadverts" ),
        "search_placeholder" => __( "Type in the box above to see suggestions ...", "wpadverts" ),
        "start_typing_here" => __( "start typing here ...", "wpadverts" )
    ));
    
    wp_localize_script( 'adverts-gallery', 'adverts_gallery_lang', array(
        "ajaxurl" => adverts_ajax_url(),
        "edit_image" => __( "Edit Image", "wpadverts" ),
        "delete_image" => __( "Delete Image", "wpadverts" ),
        "view_image" => __( "View Full Image", "wpadverts" ),
        "featured" => __( "Main", "wpadverts" ),
    ));
    
    $module = adverts_config( 'config.module' );

    foreach((array)$module as $mod => $status) {
        if(!is_file(ADVERTS_PATH . "addons/$mod/$mod.php")) {
            continue;
        }
        if($status > 0) {
            include_once ADVERTS_PATH . "addons/$mod/$mod.php";
        }
        if($status == 0.5) {
            add_action("init", "adverts_install_modules", 1000);
        }
    }

    if( get_option( "adverts_delayed_install" ) == "yes" ) {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
        
        delete_option( "adverts_delayed_install" );
    }
    
    do_action("adverts_core_initiated");
}

/**
 * Frontend Adverts Init Function
 * 
 * Registers frontend: scripts, styles and shortcodes
 * 
 * @since 0.1
 * @return void
 */
function adverts_init_frontend() {
    ;
    wp_register_style( 'adverts-frontend', ADVERTS_URL . '/assets/css/wpadverts-frontend.css', array(), "1.5.3" );
    wp_register_style( 'adverts-swipebox', ADVERTS_URL . '/assets/css/swipebox.min.css', array(), "1.4.5" );
    
    wp_register_script('adverts-single', ADVERTS_URL . '/assets/js/wpadverts-single.js', array( 'jquery' ), "1.4.0" );
    wp_register_script('adverts-frontend', ADVERTS_URL . '/assets/js/wpadverts-frontend.js', array( 'jquery' ), "1.3.5" );
    wp_register_script('adverts-frontend-add', ADVERTS_URL . '/assets/js/wpadverts-frontend-add.js', array( 'jquery'), "1.4.0" );
    wp_register_script('adverts-frontend-manage', ADVERTS_URL . '/assets/js/wpadverts-frontend-manage.js', array( 'jquery'), "1.3.5" );
    wp_register_script('adverts-swipebox', ADVERTS_URL . '/assets/js/jquery.swipebox.js', array( 'jquery', 'adverts-frontend' ), "1.4.5");
    
    include_once ADVERTS_PATH . 'includes/functions.php';
    include_once ADVERTS_PATH . 'includes/gallery.php';
    include_once ADVERTS_PATH . 'includes/shortcodes.php';
    
    add_filter('the_content', 'adverts_the_content', 9999 );
    
    // Below filters and actions were removed in version 1.4.0 in favor of Adverts_Taxonomies class
    // add_filter('posts_results', 'adverts_posts_results', 10, 2 );
    // add_filter('template_include', 'adverts_template_include'); 
    
    add_action('template_redirect', 'adverts_disable_default_archive');
    
    add_filter('post_thumbnail_html', 'adverts_post_thumbnail_html');
    add_action('adverts_new_user_notification', 'wp_new_user_notification', 10, 3 );
    add_filter('post_class', 'adverts_post_class' );
    
    add_action('adverts_tpl_single_top', 'adverts_single_rslides');
    add_action('adverts_tpl_single_bottom', 'adverts_single_contact_information');
    
    add_filter('adverts_create_user_from_post_id', '_adverts_create_user_from_post_id', 20, 2 );
    add_action('template_redirect', 'adverts_skip_preview' );
    
    wp_localize_script( 'adverts-frontend', 'adverts_frontend_lang', array(
        "ajaxurl" => adverts_ajax_url(),
        "als_visible_items" => adverts_config( "gallery.visible_items" ),
        "als_scrolling_items" => adverts_config( "gallery.scrolling_items" ),
        "lightbox" => adverts_config( "gallery.lightbox" )
    ) );
    
    wp_localize_script( 'adverts-frontend-manage', 'adverts_frontend_manage_lang', array(
        "ajaxurl" => adverts_ajax_url(),
        "ok" => __( "OK", "wpadverts" )
    ) );
    
    if(wp_get_theme()->get_template() == "twentytwelve") {
        add_action("wp_head", "adverts_css_rem_fix");
    }

}

/**
 * Adverts Admin Init Function
 * 
 * Registers admin: ajax actions, scripts, styles and meta boxes when adding new advert.
 * 
 * @since 0.1
 * @return void
 */
function adverts_init_admin() {
    
    include_once ADVERTS_PATH . 'includes/ajax.php';
    include_once ADVERTS_PATH . 'includes/admin-pages.php';
    include_once ADVERTS_PATH . 'includes/admin-post-type.php';
    include_once ADVERTS_PATH . 'includes/class-form.php';
    include_once ADVERTS_PATH . 'includes/class-html.php';
    include_once ADVERTS_PATH . 'includes/gallery.php';
    
    add_action( 'add_meta_boxes', 'adverts_data_box' );
    add_action( 'add_meta_boxes', 'adverts_box_gallery' );
    
    wp_register_script('adverts-admin', ADVERTS_URL . '/assets/js/wpadverts-admin.js', array( 'jquery' ), "1.3.5", true);
    wp_register_script('adverts-admin-config-core', ADVERTS_URL . '/assets/js/wpadverts-admin-config-core.js', array( 'jquery' ), "1.3.5", true);
    wp_register_style('adverts-admin', ADVERTS_URL . '/assets/css/wpadverts-admin.css', array(), "1.4.4" );
    
    wp_register_script( 'adverts-admin-updates', ADVERTS_URL . '/assets/js/wpadverts-admin-updates.js', array( 'jquery' ), "1.3.5", true );
    wp_register_style( 'adverts-admin-updates', ADVERTS_URL . '/assets/css/wpadverts-admin-updates.css', array(), "1.5.3" );
    
    wp_register_script( 'adverts-types-post', ADVERTS_URL . '/assets/js/wpadverts-types-post.js', array( 'jquery' ), "1.6.0", true );
    
    add_filter( 'display_post_states', 'adverts_display_expired_state' );
    add_action( 'post_submitbox_misc_actions', 'adverts_expiry_meta_box' );
    
    add_action( 'admin_print_scripts-post-new.php', 'adverts_admin_script', 11 );
    add_action( 'admin_print_scripts-post.php', 'adverts_admin_script', 11 );
    add_action( 'admin_print_scripts-edit.php', 'adverts_admin_script', 11 );
    
    add_action( 'admin_head', 'adverts_admin_head' );
    add_filter( 'wp_insert_post_data', 'adverts_insert_post_data');
    add_action( 'save_post', 'adverts_save_post', 10, 2 );
    add_action( 'save_post', 'adverts_save_post_validator', 10, 2 );
    
    add_filter( 'post_updated_messages', 'adverts_post_updated_messages');
    
    add_filter( 'add_menu_classes', 'adverts_add_menu_classes' );
    
    // Adverts category meta handlers
    add_action( 'edited_advert_category', 'adverts_save_category', 10, 2);
    add_action( 'advert_category_edit_form_fields', 'adverts_category_form_fields', 10, 2);
    
    // Preserve Ad author when editing Ad via Quick Edit
    add_filter( "wp_insert_post_data", "wpadverts_qe_preserve_author", 10, 2 );
    add_action( "admin_footer", "wpadverts_qe_hide_author_field" );
    
    // AJAX filters
    add_action('wp_ajax_adverts_author_suggest', 'adverts_author_suggest');
    add_action('wp_ajax_adverts_gallery_upload', 'adverts_gallery_upload');
    add_action('wp_ajax_adverts_gallery_update', 'adverts_gallery_update');
    add_action('wp_ajax_adverts_gallery_update_order', 'adverts_gallery_update_order');
    add_action('wp_ajax_adverts_gallery_delete', 'adverts_gallery_delete');
    add_action('wp_ajax_adverts_gallery_delete_file', 'adverts_gallery_delete_file');
    add_action('wp_ajax_adverts_gallery_image_stream', 'adverts_gallery_image_stream');
    add_action('wp_ajax_adverts_gallery_image_restore', 'adverts_gallery_image_restore');
    add_action('wp_ajax_adverts_gallery_image_save', 'adverts_gallery_image_save');
    add_action('wp_ajax_adverts_gallery_video_cover', 'adverts_gallery_video_cover');
    add_action('wp_ajax_adverts_show_contact', 'adverts_show_contact');
    add_action('wp_ajax_adverts_delete_tmp', 'adverts_delete_tmp');
    add_action('wp_ajax_adverts_delete_tmp_files', 'adverts_delete_tmp_files');
    add_action('wp_ajax_adverts_delete', 'adverts_delete');
    
    add_action('wp_ajax_nopriv_adverts_gallery_upload', 'adverts_gallery_upload');
    add_action('wp_ajax_nopriv_adverts_gallery_update', 'adverts_gallery_update');
    add_action('wp_ajax_nopriv_adverts_gallery_update_order', 'adverts_gallery_update_order');
    add_action('wp_ajax_nopriv_adverts_gallery_delete', 'adverts_gallery_delete');
    add_action('wp_ajax_nopriv_adverts_gallery_delete_file', 'adverts_gallery_delete_file');
    add_action('wp_ajax_nopriv_adverts_gallery_image_stream', 'adverts_gallery_image_stream');
    add_action('wp_ajax_nopriv_adverts_gallery_image_restore', 'adverts_gallery_image_restore');
    add_action('wp_ajax_nopriv_adverts_gallery_image_save', 'adverts_gallery_image_save');
    add_action('wp_ajax_nopriv_adverts_gallery_video_cover', 'adverts_gallery_video_cover');
    
    add_action('wp_ajax_nopriv_adverts_show_contact', 'adverts_show_contact');
    add_action('wp_ajax_nopriv_adverts_delete_tmp', 'adverts_delete_tmp');
    add_action('wp_ajax_nopriv_adverts_delete_tmp_files', 'adverts_delete_tmp_files');
    
    add_filter( 'manage_edit-advert_columns', 'adverts_edit_columns' );
    add_action( 'manage_advert_posts_custom_column', 'adverts_manage_post_columns', 10, 2 );
    add_filter( 'manage_edit-advert_sortable_columns', 'adverts_admin_sortable_columns' );
    
    /* Only run our customization on the 'edit.php' page in the admin. */
    add_action( 'load-edit.php', 'adverts_admin_load' );
}

/**
 * Registers Adverts Widgets
 * 
 * This function is executed by 'widgets_init' action.
 * 
 * @since 0.3
 * @return void
 */
function adverts_widgets_init() {

    include_once ADVERTS_PATH . '/includes/class-widget-categories.php';
    include_once ADVERTS_PATH . '/includes/class-widget-ads.php';
    
    register_widget( "Adverts_Widget_Categories" );
    register_widget( "Adverts_Widget_Ads" );
}

/**
 * Install Modules 
 * 
 * Run module installation functions (if any) on modules that were "just" activated. 
 * This function is executed in "init" filter with low priority so all the modules 
 * are initiated before it is run.
 * 
 * @since 0.2
 * @return void
 */
function adverts_install_modules() {
    $module = adverts_config( 'config.module' );
    $install = array();
    
    foreach((array)$module as $mod => $status) {
        if(!is_file(ADVERTS_PATH . "addons/$mod/$mod.php")) {
            continue;
        }
        if($status > 0) {
            include_once ADVERTS_PATH . "addons/$mod/$mod.php";
        }
        if($status == 0.5) {
            add_action("init", "adverts_install_modules");
            Adverts_Flash::instance()->add_info( __( "Module activated successfully.", "wpadverts" ) );
            
            $module[$mod] = 1;
            adverts_config_set( 'config.module', $module );
            adverts_config_save( 'config' );
            
            do_action("adverts_install_module_$mod");
            
        }
    }
}

/**
 * Activation Filter
 * 
 * This function is run when Adverts is activated.
 * 
 * @since 0.1
 * @return void
 */
function adverts_activate() {
    
    // on activation ALWAYS do this
    global $wp_rewrite;

    add_option( "adverts_delayed_install", "yes");
    
    // on FIRST activation do this.
    if(get_option("adverts_first_run", "1") == "0") {
        return;
    }

    // make sure this will not be ran again.
    add_option("adverts_first_run", "0", '', false);
    
    // register post type and taxonomy in order to allow default data insertion.
    register_post_type( 'advert' ); 
    register_taxonomy( 'advert_category', 'advert' );
    
    $hid = wp_insert_post(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => 'Adverts',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_content' => "[adverts_list]"
    ));
    
    $aid = wp_insert_post(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => 'Add',
        'post_parent' => $hid,
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_content' => "[adverts_add]"
    ));
    
    $mid = wp_insert_post(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => 'Manage',
        'post_parent' => $hid,
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_content' => "[adverts_manage]"
    ));

    if( is_int( $hid ) ) {
        $option = get_option( "adverts_config" );
        
        if( is_array( $option ) ) {
            $option["ads_list_id"] = $hid;
            update_option( "adverts_config", $option );
        } else {
            add_option( "adverts_config", array( "ads_list_id" => $hid ) );
        }
    }
    
    wp_insert_term(
        'Default',
        'advert_category'
    );
}

// Register activation function
register_activation_hook( __FILE__, 'adverts_activate' );

// Run Adverts
add_action( 'init', 'adverts_init', 5 );
add_action( 'widgets_init', 'adverts_widgets_init' );

if(is_admin() ) {
    // Run Adverts admin only actions
    add_action( 'init', 'adverts_init_admin' );
} else {
    // Run Adverts frontend only actions
    add_action( 'init', 'adverts_init_frontend' );
}

