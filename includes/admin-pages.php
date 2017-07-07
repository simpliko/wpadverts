<?php
/**
 * Admin Pages
 * 
 * Registers wp-admin / Classifieds / Options page: menu item, UI and logic.
 *
 * @package     Adverts
 * @copyright   Copyright (c) 2015, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Register Options link in Classifieds menu
add_action( 'admin_menu', 'adverts_add_options_link');

/**
 * Adds 'Options' submenu item to 'Classifieds'.
 * 
 * @since 0.1
 * @return viod
 */
function adverts_add_options_link() {
    
    /**
     * Customize 'Options' menu item
     * 
     * Customize data for add_submenu_page() function.
     * 
     * @since 0.1
     * @return array
     */
    $menu_page = apply_filters('adverts_menu_page', array(
        "parent_slug" => "edit.php?post_type=advert",
        "page_title" => __( 'Adverts Options', 'adverts' ),
        "menu_title" => __( 'Options', 'adverts' ),
        "capability" => "manage_options",
        "menu_slug" => "adverts-extensions",
        "function" => "adverts_admin_page_extensions"
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
 * wp-admin / Classifieds / Options panel logic.
 * 
 * This functions is being executed when wp-admin / Classifieds / Options panel 
 * is being loaded. It either generates list of available modules or loads module,
 * depending on $_REQUEST 'disabled', 'enabled' and 'module' params.
 * 
 * @global $_REQUEST
 * 
 * @uses adverts_config()
 * @uses adverts_config_set()
 * @uses adverts_config_save()
 * @uses adverts_request()
 * 
 * @since 0.1
 * @return void
 */
function adverts_admin_page_extensions() {
    
    // We are on one of Adverts admin pages, load default Adverts admin styles.
    wp_enqueue_style( 'adverts-admin' );
    
    // Load modules config
    $module = adverts_config( 'module' );
    $module_groups = array(
        array(
            "title" => __( "Modules", "adverts" ),
            "modules" => array(
                "core" => array(
                    "title" => __("Core", "adverts"),
                    "text" => __("Here you can configure most basic Adverts options, applicable on any site.", "adverts"),
                    "type" => "static",
                    "plugin" => null,
                ),
                "payments" => array(
                    "title" => __("Payments", "adverts"),
                    "text" => __("Charge users for posting classified ads on your site.", "adverts"),
                    "type" => "",
                    "plugin" => null
                ),
                "wc-payments" => array(
                    "title" => __("WooCommerce Payments", "adverts"),
                    "text" => __("Use WooCommerce to charge users for posting classifieds.", "adverts"),
                    "type" => "",
                    "plugin" => "wpadverts-wc/wpadverts-wc.php",
                    "purchase_url" => "https://wpadverts.com/extensions/woocommerce-integration/"
                ),
                "buddypress" => array(
                    "title" => __("BuddyPress Integration", "adverts"),
                    "text" => __("Integrate user Ads with BuddyPress profiles, registration and private messages.", "adverts"),
                    "type" => "",
                    "plugin" => "wpadverts-bp/wpadverts-bp.php",
                    "purchase_url" => "https://wpadverts.com/extensions/buddypress-integration/"
                ),
                "custom-fields" => array(
                    "title" => __("Custom Fields", "adverts"),
                    "text" => __("Allow users to edit: Add, Search and Contact forms using easy to use drag and drop editor.", "adverts"),
                    "type" => "",
                    "plugin" => "wpadverts-custom-fields/wpadverts-custom-fields.php",
                    "purchase_url" => "https://wpadverts.com/extensions/custom-fields/"
                )
            )
        ),
        array(
            "title" => __( "Features", "adverts" ),
            "modules" => array(
                "featured" => array(
                    "title" => __( "Featured Ads", "adverts" ),
                    "text" => __( "Allow users to post Ads displayed always at the top of the list.", "adverts" ),
                    "type" => "",
                    "plugin" => null
                ),
                "contact-form" => array(
                    "title" => __( "Contact Form", "adverts" ),
                    "text" => __( "Display contact form on Ad details pages instead of just the contact information.", "adverts" ),
                    "type" => "",
                    "plugin" => null
                ),
                "wpadverts-mal" => array(
                    "title" => __( "Maps And Locations", "adverts" ),
                    "text" => __( "Locations Taxonomy, Google Maps integration and more.", "adverts" ),
                    "type" => "",
                    "plugin" => "wpadverts-mal/wpadverts-mal.php",
                    "purchase_url" => "https://wpadverts.com/extensions/maps-and-locations/"
                ),
                "wpadverts-fee-per-category" => array(
                    "title" => __( "Fee Per Category", "adverts" ),
                    "text" => __( "Set fee for Ads posting per category or allow to post for free in some categories.", "adverts" ),
                    "type" => "",
                    "plugin" => "wpadverts-fee-per-category/wpadverts-fee-per-category.php",
                    "purchase_url" => "https://wpadverts.com/extensions/fee-per-category/"
                ),
                "wpadverts-recaptcha" => array(
                    "title" => __( "reCAPTCHA", "adverts" ),
                    "text" => __( "Protect your forms from SPAM using easy easy for humans and hard for bots captcha.", "adverts" ),
                    "type" => "",
                    "plugin" => "wpadverts-recaptcha/wpadverts-recaptcha.php",
                    "purchase_url" => "https://wpadverts.com/extensions/recaptcha/"
                ),
                "wpadverts-google-analytics" => array(
                    "title" => __( "Analytics", "adverts" ),
                    "text" => __( "Google Analytics integration, allows users to view their Ads stats in past 30 days.", "adverts" ),
                    "type" => "",
                    "plugin" => "wpadverts-google-analytics/wpadverts-google-analytics.php",
                    "purchase_url" => "https://wpadverts.com/extensions/google-analytics/"
                ),
            )
        ),
        array(
            "title" => __( "Payment Gateways", "adverts" ),
            "modules" => array(
                "bank-transfer" => array(
                    "title" => __("Bank Transfer", "adverts"),
                    "text" => __("Allow users to pay for ad posting using direct bank transfer or cash payments.", "adverts"),
                    "type" => "",
                    "plugin" => null
                ),
                "paypal-standard" => array(
                    "title" => __("PayPal Standard", "adverts"),
                    "text" => __("PayPal Payments Standard, allow users to pay for ads via PayPal.", "adverts"),
                    "type" => "",
                    "plugin" => "wpadverts-paypal-standard/wpadverts-paypal-standard.php",
                    "purchase_url" => "https://wpadverts.com/extensions/paypal-payments-standard/"
                ),
            )
        )

    );

    if(adverts_request('enable')) {
        // User is trying to enable module
        $enable = adverts_request('enable');
        $module[$enable] = 0.5;
        
        // Save config
        adverts_config_set( 'config.module', $module );
        adverts_config_save( 'config' );
        
        wp_redirect( add_query_arg( array( 'enable' => false, 'noheader' => false, 'module' => $enable ) ) );
        
        exit;
    }
    
    if(adverts_request('disable')) {
        // User is trying to disable module
        $disable = adverts_request('disable');
        
        if(isset($module[$disable])) {
            unset($module[$disable]);
        }
        
        // Save config
        adverts_config_set( 'config.module', $module );
        adverts_config_save( 'config' );
        
        wp_redirect( remove_query_arg( array( 'disable', 'noheader' ) ) );
        exit;
    }
    
    if( adverts_request( 'module' ) ) {
        // Load module (based on $_GET[module]) panel
        $name = adverts_request( 'module' );
        $module_current = null;
        $module_key = null;
        
        foreach($module_groups as $group) {
            foreach($group["modules"] as $key => $tmp) {
                if($key == $name) {
                    $module_current = $tmp;
                    $module_key = $key;
                    break 2;
                }
            }
        }
        
        if( $module_current === null ) {
            esc_html_e( sprintf( __( "Module [%s] does not exist.", "adverts" ), $name ) );
            return;
        }

        if($module_current["plugin"]) {
            include_once dirname( ADVERTS_PATH ) . '/' . dirname( $module_current["plugin"] ) . '/includes/admin-module-options.php';
        } else {
            include_once ADVERTS_PATH . 'addons/' . $module_key . '/includes/admin-module-options.php';
        }
    } else {
        // Display modules list template
        include ADVERTS_PATH . 'admin/options.php';
    }
    
    
}

/**
 * Displays meta fields on Adverts Category edit page.
 * 
 * This function renders form responsible for displaying box with avaiable
 * category icons and maybe some other category meta data.
 * 
 * @param type $tag
 * @since 0.3
 * @return void
 */
function adverts_category_form_fields($tag)
{
    wp_enqueue_style( 'adverts-admin' );
    wp_enqueue_style( 'adverts-icons' );
    
    wp_enqueue_style( 'wp-color-picker' ); 
    wp_enqueue_script('wp-color-picker');
    
    $icons = array("");
    $file = file(ADVERTS_PATH . '/assets/css/adverts-glyphs.css');
    
    foreach($file as $line) {
        if(stripos($line, ".adverts-icon-") === 0) {
            $l = explode(":", $line);
            $icons[] = str_replace(".adverts-icon-", "", $l[0]);
        }
    }

    $current_icon = adverts_taxonomy_get($tag->taxonomy, $tag->term_id, 'advert_category_icon', '');

    ?>

<script type="text/javascript">
(function( $ ) {
 
    // Add Color Picker to all inputs that have 'color-field' class
    $(function() {
        $('.color-field').wpColorPicker();
        $(".adverts-image-icon-picker a").click(function(e) {
            e.preventDefault();
            $(".adverts-image-icon-picker a").addClass("button-secondary").removeClass("button-primary");
            $(this).removeClass("button-secondary").addClass("button-primary").blur();
            $("#advert_category_icon").val($(this).data("name"));
            
        });
        
        $("#adverts-category-icon-filter").keyup(function(e) {
            var val = $.trim($(this).val());
            
            if(val.length > 0) {
                $(".adverts-image-icon-picker > li").hide();
                $(".adverts-image-icon-picker > li[data-name*='"+val+"']").show();
            } else {
                $(".adverts-image-icon-picker > li").show();
            }

            
        });
        
        var scrollTo = $(".adverts-image-icon-picker .button-primary");
        var scrollWrap = $(".adverts-image-icon-picker");

        scrollWrap.scrollTop(scrollTo.offset().top - scrollWrap.offset().top + scrollWrap.scrollTop() - 20);
    });
     
})( jQuery );
</script>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="advert_category_icon"><?php _e("Category Icon", "adverts") ?></label></th>
        <td>
            <input type="hidden" name="advert_category_icon" id="advert_category_icon" value="<?php esc_attr_e($current_icon) ?>" />
            <input type="text" autocomplete="off" id="adverts-category-icon-filter" placeholder="<?php _e("Filter Icons ...", "adverts") ?>" />
            <ul class="adverts-image-icon-picker">
                <?php foreach($icons as $icon): ?>
                <?php $title = ucfirst(str_replace("-", " ", $icon ) ) ?>
                <li data-name="<?php esc_html_e($icon) ?>">
                    <a href="#" class="<?php echo $icon==$current_icon ? 'button-primary' : 'button-secondary' ?>" title="<?php esc_html_e( $title ) ?>" data-name="<?php esc_html_e($icon) ?>">
                        <span class="adverts-icon-<?php esc_html_e($icon) ?>"></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            
        </td>
    </tr>

    <?php
}


/**
 * Updates adverts category meta
 * 
 * @param int $term_id Currently edited category id
 * @param int $tt_id
 * @since 0.3
 * @return void
 */
function adverts_save_category($term_id, $tt_id) {
    if (!$term_id) {
        return;
    }

    if (isset($_POST['advert_category_icon'])) {
        adverts_taxonomy_update($_POST['taxonomy'], $term_id, 'advert_category_icon', $_POST['advert_category_icon']);
    }

}
