<?php
/**
 * Core Admin Pages
 * 
 * This file contains function to handle default/core config logic in wp-admin 
 * and config form.
 *
 * @package     Adverts
 * @subpackage  Core
 * @copyright   Copyright (c) 2015, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

/**
 * Renders default/core config form.
 * 
 * The page is rendered in wp-admin / Classifieds / Options / Core 
 * 
 * @since 0.1
 * @return void
 */
function adext_core_page_options() {
    
    if(adverts_request( "adaction" ) == "gallery" ) {
        _adext_core_page_options_gallery();
    } else if( adverts_request( "adaction") == "types" ) {
        include_once ADVERTS_PATH . "/includes/class-types-admin.php";
        $admin_types = new Adverts_Types_Admin();
        $admin_types->render();
    } else if( adverts_request( "adaction") == "moderate" ) {
        include_once ADVERTS_PATH . "/includes/class-moderate-admin.php";
        $admin_moderate = new Adverts_Moderate_Admin();
        $admin_moderate->render();
    } else {
        _adext_core_page_options_main();
    }
}

/**
 * Renders default/core config form.
 * 
 * The page is rendered in wp-admin / Classifieds / Options / Core 
 * 
 * @since 1.2
 * @return void
 */
function _adext_core_page_options_main() {
    
    $button_text = __("Update Options", "wpadverts");
    
    wp_enqueue_style( 'adverts-admin' );
    wp_enqueue_script( 'adverts-admin-config-core' );
        
    $flash = Adverts_Flash::instance();
    $error = array();
    
    $scheme = Adverts::instance()->get("form_core_config");
    $form = new Adverts_Form( $scheme );
    
    if(isset($_POST) && !empty($_POST)) {
        $form->bind( stripslashes_deep( $_POST ) );
        $valid = $form->validate();

        if($valid) {
            
            $data = $form->get_values();
            $data['expired_ad_public_cap'] = adverts_request( 'expired_ad_public_cap' );
            
            $ckbox = array( 'hide_images_in_media_library', 'delete_from_media_library' );

            foreach( $ckbox as $ckbox_option ) {
                if( adverts_request( $ckbox_option ) ) {
                    $data[$ckbox_option] = 1;
                } else {
                    $data[$ckbox_option] = 0;
                }
            }

            $data["adverts_manage_moderate"] = $form->get_value( "adverts_manage_moderate", 0 );
            $data["module"] = adverts_config( 'config.module' );
            $data["license"] = adverts_config( 'config.license' );

            update_option("adverts_config", $data );
            $flash->add_info( __("Settings updated.", "wpadverts") );
        } else {
            $flash->add_error( __("There are errors in your form.", "wpadverts") );
        }
    } else {
        $form->bind( adverts_config("config.ALL") );
    }
    
    include ADVERTS_PATH . 'addons/core/admin/options.php';
} 

/**
 * Renders default/gallery config form.
 * 
 * The page is rendered in wp-admin / Classifieds / Options / Core / Gallery
 * 
 * @since 1.2
 * @return void
 */
function _adext_core_page_options_gallery() {
    
    $button_text = __("Update Options", "wpadverts");
    
    wp_enqueue_style( 'adverts-admin' );
    $flash = Adverts_Flash::instance();
    $error = array();
    
    adverts_form_add_field("adext_field_image_size", array(
        "renderer" => "adext_field_image_size",
        "callback_save" => "adverts_save_multi",
        "callback_bind" => null,
    ));
    
    $scheme = Adverts::instance()->get("form_gallery_config");
    $form = new Adverts_Form( $scheme );
    
    if(isset($_POST) && !empty($_POST)) {
        
        $lightbox = adverts_request( "lightbox" );
        if( ! empty( $lightbox ) ) {
            $lightbox_enabled = 1;
        } else {
            $lightbox_enabled = 0;
        }
        
        $tosave = array();
        $bind = array(
            "ui" => adverts_request( "ui" ),
            "visible_items" => absint( adverts_request( "visible_items" ) ),
            "scrolling_items" => absint( adverts_request( "scrolling_items" ) ),
            "lightbox" => $lightbox_enabled,
            "image_edit_cap" => adverts_request( "image_edit_cap" ),
            "image_editor" => adverts_request( "image_editor" ),
            "image_fit" => adverts_request( "image_fit" ),
        );
        
        $tosave = $bind;
        $tosave["image_sizes"] = array();
        
        foreach( adverts_config( "gallery.image_sizes" ) as $size_key => $size ) {
            $image_size = $_POST['image_sizes'][$size_key];
            if( ! isset( $image_size["crop"] ) ) {
                $image_size["crop"] = 0;
            } 
            $bind["image_size__".$size_key] = $image_size;
            $tosave["image_sizes"][$size_key] = $image_size;
        }
        
        add_filter( "adverts_form_bind", "_adext_core_form_gallery_bind", 10, 2 );
        
        $form->bind( stripslashes_deep( $bind ) );
        $valid = $form->validate();

        if($valid) {
            
            update_option("adverts_gallery", $tosave );
            $flash->add_info( __("Settings updated.", "wpadverts") );
        } else {
            $flash->add_error( __("There are errors in your form.", "wpadverts") );
        }
    } else {
        $bind = array(
            "ui" => adverts_config( "gallery.ui" ),
            "visible_items" => adverts_config( "gallery.visible_items" ),
            "scrolling_items" => adverts_config( "gallery.scrolling_items" ),
            "lightbox" => adverts_config( "gallery.lightbox" ),
            "image_edit_cap" => adverts_config( "gallery.image_edit_cap" ),
            "image_editor" => adverts_config( "gallery.image_editor" ),
            "image_fit" => adverts_config( "gallery.image_fit" ),
        );
        
        foreach( adverts_config( "gallery.image_sizes" ) as $size_key => $size ) {
            $bind["image_size__".$size_key] = $size;
        }
        
        add_filter( "adverts_form_bind", "_adext_core_form_gallery_bind", 10, 2 );
        
        $form->bind( $bind );
    }
    
    include ADVERTS_PATH . 'addons/core/admin/options-gallery.php';
}

$currency_code_options = array();

foreach(adverts_currency_list() as $tmp_currency) {
    
    $sign = "";
    
    if($tmp_currency['sign']) {
        $sign = ' (' . $tmp_currency['sign'] . ')';
    }
    
    $currency_code_options[] = array(
        "value" => $tmp_currency["code"],
        "text" => $tmp_currency["label"] . $sign
    );
}

/**
 * Register <select> input with list of Pages as options.
 * 
 * This is basically a wrapper for wp_dropdown_pages() WordPress function.
 * 
 * @see wp_dropdown_pages()
 * 
 * @param array $field Fields settings
 * @since 0.3
 * @return void
 */
function adverts_dropdown_pages( $field ) {
    
    if(isset($field["value"])) {
        $value = $field["value"];
    } else {
        $value = null;
    }
    
    if( isset( $field["attr"]["id"] ) ) {
        $id = $field["attr"]["id"];
    } else {
        $id = $field["name"];
    }
    
    $args = array(
        'selected' => $value, 
        'echo' => 1,
	'name' => $field["name"], 
        'id' => $id,
	'show_option_none' => ' ',
        'option_none_value' => 0
    );
    
    wp_dropdown_pages( $args );
}

// Register <select> with list of pages 
/** @see adverts_dropdown_pages() */
adverts_form_add_field("adverts_dropdown_pages", array(
    "renderer" => "adverts_dropdown_pages",
    "callback_bind" => "adverts_bind_single",
    "callback_save" => "adverts_save_single",
));

// Core options config form
Adverts::instance()->set("form_core_config", array(
    "name" => "",
    "action" => "",
    "field" => array(
        array(
            "name" => "_common_settings",
            "type" => "adverts_field_header",
            "order" => 10,
            "label" => __( 'Common Settings', "wpadverts" ),
            "title" => __( 'Common Settings', "wpadverts" )
        ),
        array(
            "name" => "ads_list_id",
            "type" => "adverts_dropdown_pages",
            "order" => 10,
            "label" => __("Default Ads List Page", "wpadverts"),
            "hint" => __("Select page on which the main [adverts_list] shortcode is being used.", "wpadverts"),
            "attr" => array(
                "id" => "option__ads_list_id"
            )
        ),
        array(
            "name" => "visibility",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Default Visibility", "wpadverts"),
            "hint" => __("Number of days Ad will be visible by default.", "wpadverts"),
            "validator" => array(
                array("name"=>"is_required"),
                array("name"=>"is_integer")
            )
        ),
        array(
            "name" => "empty_price",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Empty Price Text", "wpadverts"),
            "hint" => __("The text to display instead of price if item price was not provided.", "wpadverts"),
            "validator" => array()
        ),
        array(
            "name" => "hide_images_in_media_library",
            "type" => "adverts_field_checkbox",
            "label" => __( "Media Library", "wpadverts" ),
            "order" => 10,
            "options" => array(
                array( "value" => "1", "text" => __( "Do not show Advert images (and other files) in Media Library.", "wpadverts" ) ),
            )
        ),
        array(
            "name" => "delete_from_media_library",
            "type" => "adverts_field_checkbox",
            "label" => " ",
            "order" => 10,
            "options" => array(
                array( "value" => "1", "text" => __( "Delete Advert images from Media Library when deleting an Advert.", "wpadverts" ) ),
            )
        ),
        array(
            "name" => "adverts_manage_moderate",
            "type" => "adverts_field_checkbox",
            "label" => __( "Adverts Manage Moderation", "wpadverts" ),
            "order" => 10,
            "options" => array(
                array( "value" => "1", "text" => __( "Set Ad status to 'pending' when user updates his Ad from page with [adverts_manage] shortcode.", "wpadverts" ) ),
            )
        ),
        array(
            "name" => "_defaults_adverts_list",
            "type" => "adverts_field_header",
            "order" => 10,
            "label" => __( 'Defaults Values For [adverts_list]', "wpadverts" ),
            "title" => __( 'Defaults Values For [adverts_list]', "wpadverts" )
        ),
        array(
            "name" => "ads_list_default__search_bar",
            "type" => "adverts_field_select",
            "label" => __( "Search Bar", "wpadverts" ),
            "order" => 10,
            "options" => array(
                array( "value" => "enabled", "text" => __( "Enabled", "wpadverts" ) ),
                array( "value" => "disabled", "text" => __( "Disabled", "wpadverts" ) )
            ),
            "attr" => array(
                "id" => "option__ads_list_default__search_bar"
            )
        ),
        array(
            "name" => "ads_list_default__display",
            "type" => "adverts_field_select",
            "label" => __( "Display Ads As", "wpadverts" ),
            "order" => 10,
            "options" => array(
                array( "value" => "grid", "text" => __( "Grid (2 or more columns)", "wpadverts" ) ),
                array( "value" => "list", "text" => __( "List (1 column)", "wpadverts" ) )
            ),
            "attr" => array(
                "id" => "option__ads_list_default__display"
            )
        ),
        array(
            "name" => "ads_list_default__columns",
            "type" => "adverts_field_select",
            "order" => 10,
            "label" => __( "Columns", "wpadverts" ),
            "options" => array(
                array( "value" => 2, "text" => "2" ),
                array( "value" => 3, "text" => "3" ),
                array( "value" => 4, "text" => "4" ),
            ),
            "attr" => array(
                "id" => "option__ads_list_default__columns"
            )
        ),
        array(
            "name" => "ads_list_default__posts_per_page",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __( "Posts Per Page", "wpadverts" ),
            "validator" => array(
                array("name"=>"is_required"),
                array("name"=>"is_integer")
            ),
            "attr" => array(
                "id" => "option__ads_list_default__posts_per_page"
            )
        ),
        array(
            "name" => "ads_list_default__switch_views",
            "type" => "adverts_field_checkbox",
            "label" => __( "Switch Views", "wpadverts" ),
            "order" => 10,
            "options" => array(
                array( "value" => "grid", "text" => __( "Allow users to switch between grid and list view.", "wpadverts" ), "id" => "option__ads_list_default__switch_views" ),
            )
        ),
        array(
            "name" => "_currency_settings",
            "type" => "adverts_field_header",
            "order" => 10,
            "label" => __( 'Currency Settings', "wpadverts" ),
            "title" => __( 'Currency Settings', "wpadverts" )
        ),
        array(
            "name" => "currency_code",
            "type" => "adverts_field_select",
            "order" => 10,
            "label" => __("Currency", "wpadverts"),
            "options" => $currency_code_options
        ),
        array(
            "name" => "currency_sign_type",
            "type" => "adverts_field_select",
            "order" => 10,
            "label" => __("Currency Position", "wpadverts"),
            "options" => array(
                array("value"=>"p", "text"=>__("Prefix - $10", "wpadverts")),
                array("value"=>"s", "text"=>__("Suffix - 10$", "wpadverts")),
            )
        ),
        array(
            "name" => "currency_decimals",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Decimal Places", "wpadverts"),
            "validator" => array(
                array("name"=>"is_required"),
                array("name"=>"is_integer")
            )
        ),
        array(
            "name" => "currency_char_decimal",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Decimal Separator", "wpadverts"),
        ),
        array(
            "name" => "currency_char_thousand",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Thousands Separator", "wpadverts"),
        ),
        array(
            "name" => "_expired_ads_settings",
            "type" => "adverts_field_header",
            "order" => 10,
            "label" => __( 'Expired Ads Handling', "wpadverts" ),
            "title" => __( 'Expired Ads Handling', "wpadverts" )
        ),
        array(
            "name" => "expired_ad_status",
            "type" => "adverts_field_radio",
            "order" => 10,
            "label" => __("Expired Ad HTTP Status", "wpadverts"),
            "class" => "expired_ad_status",
            "options" => array(
                array("value"=>"404", "text"=>__("<strong>404</strong> — Show 'Page Not Found' Error", "wpadverts")),
                array("value"=>"301", "text"=>__("<strong>301</strong> — Redirect to a different page ...", "wpadverts")),
                array("value"=>"200", "text"=>__("<strong>200</strong> — Show Ad details page with contact options disabled.", "wpadverts")),
            )
        ),
        array(
            "name" => "expired_ad_redirect_url",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Redirect URL", "wpadverts"),
            "placeholder" => "e.g. https://example.com/"
        ),
        array(
            "name" => "expired_ad_public_cap",
            "type" => "adverts_field_select",
            "order" => 10,
            "label" => __("Always Visible For", "wpadverts"),
            "empty_option" => true,
            "empty_option_text" => "",
            "options_callback" => "adverts_get_roles_dropdown",
            "hint" => __( "Cabability (or Role) required to always see expired Ads details pages.", "wpadverts" )
        ),
    )
));

// Core options config form
Adverts::instance()->set("form_gallery_config", array(
    "name" => "",
    "action" => "",
    "field" => array(
        array(
            "name" => "_common_settings",
            "type" => "adverts_field_header",
            "order" => 10,
            "label" => __( 'Advert Details Pages', "wpadverts" ),
            "title" => __( 'Advert Details Pages', "wpadverts" )
        ),
        array(
            "name" => "ui",
            "type" => "adverts_field_radio",
            "order" => 10,
            "label" => __("Gallery Pagination", "wpadverts"),
            "options" => array(
                array( "value" => "pagination", "text" => __( "Next and Previous Buttons", "wpadverts" ) ),
                array( "value" => "thumbnails", "text" => __( "Thumbnails Slider", "wpadverts" ) )
            ),
            "rows" => 1
        ),
        array(
            "name" => "visible_items",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Visible Thumbnails", "wpadverts"),
            "hint" => __( "Number of thumbnails to show below gallery.", "wpadverts" )
        ),
        array(
            "name" => "scrolling_items",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Scrolling Thumbnails", "wpadverts"),
            "hint" => __( "Number of thumbnails to scroll when clicking Previous or Next buttons.", "wpadverts" )
        ),
        array(
            "name" => "lightbox",
            "type" => "adverts_field_checkbox",
            "order" => 10,
            "label" => __("Lightbox", "wpadverts"),
            "options" => array(
                array( "value" => "1", "text" => __( "Allow opening Gallery images in a Lightbox.", "wpadverts" ) )
            )
        ),
        array(
            "name" => "image_fit",
            "type" => "adverts_field_select",
            "order" => 10,
            "label" => __("Image Fit", "wpadverts"),
            "empty_option" => true,
            "empty_option_text" => "",
            "options" =>  array(
                array( "value" => "contain", "text" => __( "Contain", "wpadverts" ) ),
                array( "value" => "cover", "text" => __( "Cover", "wpadverts" ) ),
                array( "value" => "fill", "text" => __( "Fill", "wpadverts" ) ),
            ),
            "hint" => __( "How images should be displayed in the gallery.", "wpadverts" )
        ),
        array(
            "name" => "_gallery_upload_settings",
            "type" => "adverts_field_header",
            "order" => 10,
            "label" => __( 'Gallery Upload', "wpadverts" ),
            "title" => __( 'Gallery Upload', "wpadverts" )
        ),
        array(
            "name" => "image_editor",
            "type" => "adverts_field_select",
            "order" => 10,
            "label" => __("Image Editor", "wpadverts"),
            "empty_option" => true,
            "empty_option_text" => __( "Default", "wpadverts" ),
            "options_callback" => "adverts_get_image_editors",
            "hint" => __( "The library used for processing uploaded images.", "wpadverts" )
        ),
        array(
            "name" => "image_edit_cap",
            "type" => "adverts_field_select",
            "order" => 10,
            "label" => __("Image Editor Capability", "wpadverts"),
            "empty_option" => true,
            "empty_option_text" => "",
            "options_callback" => "adverts_get_roles_dropdown",
            "hint" => __( "Cabability required to edit images in the Gallery.", "wpadverts" )
        ),
        array(
            "name" => "_gallery_image_sizes",
            "type" => "adverts_field_header",
            "order" => 10,
            "label" => __( 'Image Sizes', "wpadverts" ),
            "title" => __( 'Image Sizes', "wpadverts" )
        ),
        array(
            "name" => "image_size__adverts-gallery",
            "type" => "adext_field_image_size",
            "order" => 10,
            "label" => __("Adverts Gallery", "wpadverts"),
            "hint" => __("This image size is being used in the image slider on Advert details pages.", "wpadverts")
        ),
        array(
            "name" => "image_size__adverts-list",
            "type" => "adext_field_image_size",
            "order" => 10,
            "label" => __("Adverts List", "wpadverts"),
            "hint" => __("This image size is being used in the [adverts_list] shortcode.", "wpadverts")
        ),
        array(
            "name" => "image_size__adverts-upload-thumbnail",
            "type" => "adext_field_image_size",
            "order" => 10,
            "label" => __("Upload Thumbnail", "wpadverts"),
            "hint" => __("This image size is being used in gallery thumbnails.", "wpadverts")
        )
    )
));

function adverts_get_image_editors() {
    $ie = apply_filters( 'wp_image_editors', array( 'WP_Image_Editor_Imagick', 'WP_Image_Editor_GD' ) );
    $arr = array();
    foreach( $ie as $editor ) {
        $arr[] = array( "value" => $editor, "text" => $editor );
    }
    return $arr;
}

function adverts_get_roles_dropdown() {
    $arr = array();
    $roles = get_editable_roles();
    
    foreach( $roles as $key => $role ) {
        $arr[] = array(
            "value" => $key,
            "text" => $role["name"],
            "depth" => 0
        );
        
        foreach( $role["capabilities"] as $cap_key => $cap_value ) {
            $arr[] = array(
                "value" => $cap_key,
                "text" => $cap_key,
                "depth" => 1
            );
        }
    }
    
    return $arr;
}

/**
 * 
 * @param Adverts_Form $form
 * @param array $bind
 * @return Adverts_Form
 */
function _adext_core_form_gallery_bind( $form, $bind ) {
    
    foreach( $bind as $b_key => $b_value ) {
        if( stripos( $b_key, "image_size__" ) === 0 ) {
            $form->set_value( $b_key, $b_value );
        }
    }
    return $form;
}

function adext_field_image_size( $field ) {
    
    $field_name = str_replace( "image_size__", "", $field["name"] );
    $name = "image_sizes[" . $field_name . "][%s]"; 
    
    $width = 0;
    $height = 0;
    $crop = 0;
    
    $v = $field["value"];
    
    if( isset( $v["width"] ) ) {
        $width = $v["width"];
    }
    
    if( isset( $v["height"] ) ) {
        $height = $v["height"];
    }
    
    if( isset( $v["crop"] ) ) {
        $crop = $v["crop"];
    }
    
    ?>
    <input type="hidden" name="<?php echo esc_attr( sprintf( $name, "enabled" ) ) ?>" value="1" />

    <span style="opacity:0.65"><?php _e("max. width", "wpadverts") ?></span>
    <input type="number" name="<?php echo esc_attr( sprintf( $name, "width" ) ) ?>" step="1" min="0" style="width:60px" value="<?php echo esc_attr( $width ) ?>" /> 
    
    <strong>x</strong>
    
    <span style="opacity:0.65"><?php _e("max. height", "wpadverts") ?></span>
    <input type="number" name="<?php echo esc_attr( sprintf( $name, "height" ) ) ?>" step="1" min="0" style="width:60px" value="<?php echo esc_attr( $height ) ?>" />
    
    <label for="<?php echo esc_attr( sprintf( $name, "crop" ) ) ?>" style="opacity:0.65; vertical-align: initial" ><?php _e("crop image", "wpadverts") ?></label>
    <input type="checkbox" name="<?php echo esc_attr( sprintf( $name, "crop" ) ) ?>" id="<?php echo esc_attr( sprintf( $name, "width" ) ) ?>" value="1" <?php checked($crop) ?> />

    <?php
    
}
