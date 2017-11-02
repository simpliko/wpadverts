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
    
    $button_text = __("Update Options", "adverts");
    
    wp_enqueue_style( 'adverts-admin' );
    $flash = Adverts_Flash::instance();
    $error = array();
    
    $scheme = Adverts::instance()->get("form_core_config");
    $form = new Adverts_Form( $scheme );
    
    if(isset($_POST) && !empty($_POST)) {
        $form->bind( stripslashes_deep( $_POST ) );
        $valid = $form->validate();

        if($valid) {
            
            $data = $form->get_values();
            $data["module"] = adverts_config( 'config.module' );
            $data["license"] = adverts_config( 'config.license' );
            
            update_option("adverts_config", $data );
            $flash->add_info( __("Settings updated.", "adverts") );
        } else {
            $flash->add_error( __("There are errors in your form.", "adverts") );
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
    
    $button_text = __("Update Options", "adverts");
    
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
            $flash->add_info( __("Settings updated.", "adverts") );
        } else {
            $flash->add_error( __("There are errors in your form.", "adverts") );
        }
    } else {
        $bind = array(
            "ui" => adverts_config( "gallery.ui" ),
            "visible_items" => adverts_config( "gallery.visible_items" ),
            "scrolling_items" => adverts_config( "gallery.scrolling_items" ),
            "lightbox" => adverts_config( "gallery.lightbox" ),
            "image_edit_cap" => adverts_config( "gallery.image_edit_cap" ),
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
    
    $args = array(
        'selected' => $value, 
        'echo' => 1,
	'name' => $field["name"], 
        'id' => $field["name"],
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
            "label" => __( 'Common Settings', 'adverts' ),
            "title" => __( 'Common Settings', 'adverts' )
        ),
        array(
            "name" => "ads_list_id",
            "type" => "adverts_dropdown_pages",
            "order" => 10,
            "label" => __("Default Ads List Page", "adverts"),
            "hint" => __("Select page on which the main [adverts_list] shortcode is being used.", "adverts")
        ),
        array(
            "name" => "visibility",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Default Visibility", "adverts"),
            "hint" => __("Number of days Ad will be visible by default.", "adverts"),
            "validator" => array(
                array("name"=>"is_required"),
                array("name"=>"is_integer")
            )
        ),
        array(
            "name" => "_defaults_adverts_list",
            "type" => "adverts_field_header",
            "order" => 10,
            "label" => __( 'Defaults Values For [adverts_list]', 'adverts' ),
            "title" => __( 'Defaults Values For [adverts_list]', 'adverts' )
        ),
        array(
            "name" => "ads_list_default__search_bar",
            "type" => "adverts_field_select",
            "label" => __( "Search Bar", "adverts" ),
            "order" => 10,
            "options" => array(
                array( "value" => "enabled", "text" => __( "Enabled", "adverts" ) ),
                array( "value" => "disabled", "text" => __( "Disabled", "adverts" ) )
            )
        ),
        array(
            "name" => "ads_list_default__display",
            "type" => "adverts_field_select",
            "label" => __( "Display Ads As", "adverts" ),
            "order" => 10,
            "options" => array(
                array( "value" => "grid", "text" => __( "Grid (2 or more columns)", "adverts" ) ),
                array( "value" => "list", "text" => __( "List (1 column)", "adverts" ) )
            )
        ),
        array(
            "name" => "ads_list_default__columns",
            "type" => "adverts_field_select",
            "order" => 10,
            "label" => __( "Columns", "adverts" ),
            "options" => array(
                array( "value" => 2, "text" => "2" ),
                array( "value" => 3, "text" => "3" ),
                array( "value" => 4, "text" => "4" ),
            )
        ),
        array(
            "name" => "ads_list_default__posts_per_page",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __( "Posts Per Page", "adverts" ),
            "validator" => array(
                array("name"=>"is_required"),
                array("name"=>"is_integer")
            )
        ),
        array(
            "name" => "ads_list_default__switch_views",
            "type" => "adverts_field_checkbox",
            "label" => __( "Switch Views", "adverts" ),
            "order" => 10,
            "options" => array(
                array( "value" => "grid", "text" => __( "Allow users to switch between grid and list view.", "adverts" ) ),
            )
        ),
        array(
            "name" => "_currency_settings",
            "type" => "adverts_field_header",
            "order" => 10,
            "label" => __( 'Currency Settings', 'adverts' ),
            "title" => __( 'Currency Settings', 'adverts' )
        ),
        array(
            "name" => "currency_code",
            "type" => "adverts_field_select",
            "order" => 10,
            "label" => __("Currency", "adverts"),
            "options" => $currency_code_options
        ),
        array(
            "name" => "currency_sign_type",
            "type" => "adverts_field_select",
            "order" => 10,
            "label" => __("Currency Position", "adverts"),
            "options" => array(
                array("value"=>"p", "text"=>__("Prefix - $10", "adverts")),
                array("value"=>"s", "text"=>__("Suffix - 10$", "adverts")),
            )
        ),
        array(
            "name" => "currency_decimals",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Decimal Places", "adverts"),
            "validator" => array(
                array("name"=>"is_required"),
                array("name"=>"is_integer")
            )
        ),
        array(
            "name" => "currency_char_decimal",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Decimal Separator", "adverts"),
        ),
        array(
            "name" => "currency_char_thousand",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Thousands Separator", "adverts"),
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
            "label" => __( 'Advert Details Pages', 'adverts' ),
            "title" => __( 'Advert Details Pages', 'adverts' )
        ),
        array(
            "name" => "ui",
            "type" => "adverts_field_radio",
            "order" => 10,
            "label" => __("Gallery Pagination", "adverts"),
            "options" => array(
                array( "value" => "pagination", "text" => __( "Next and Previous Buttons", "adverts" ) ),
                array( "value" => "thumbnails", "text" => __( "Thumbnails Slider", "adverts" ) )
            ),
            "rows" => 1
        ),
        array(
            "name" => "visible_items",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Visible Thumbnails", "adverts"),
            "hint" => __( "Number of thumbnails to show below gallery.", "adverts" )
        ),
        array(
            "name" => "scrolling_items",
            "type" => "adverts_field_text",
            "order" => 10,
            "label" => __("Scrolling Thumbnails", "adverts"),
            "hint" => __( "Number of thumbnails to scroll when clicking Previous or Next buttons.", "adverts" )
        ),
        array(
            "name" => "lightbox",
            "type" => "adverts_field_checkbox",
            "order" => 10,
            "label" => __("Lightbox", "adverts"),
            "options" => array(
                array( "value" => "1", "text" => __( "Allow opening Gallery images in a Lightbox.", "adverts" ) )
            )
        ),
        array(
            "name" => "_gallery_upload_settings",
            "type" => "adverts_field_header",
            "order" => 10,
            "label" => __( 'Gallery Upload', 'adverts' ),
            "title" => __( 'Gallery Upload', 'adverts' )
        ),
        array(
            "name" => "image_edit_cap",
            "type" => "adverts_field_select",
            "order" => 10,
            "label" => __("Image Editor Capability", "adverts"),
            "empty_option" => true,
            "empty_option_text" => "",
            "options_callback" => "adverts_get_roles_dropdown",
            "hint" => __( "Cabability required to edit images in the Gallery.", "adverts" )
        ),
        array(
            "name" => "_gallery_image_sizes",
            "type" => "adverts_field_header",
            "order" => 10,
            "label" => __( 'Image Sizes', 'adverts' ),
            "title" => __( 'Image Sizes', 'adverts' )
        ),
        array(
            "name" => "image_size__adverts-gallery",
            "type" => "adext_field_image_size",
            "order" => 10,
            "label" => __("Adverts Gallery", "adverts"),
            "hint" => __("This image size is being used in the image slider on Advert details pages.", "adverts")
        ),
        array(
            "name" => "image_size__adverts-list",
            "type" => "adext_field_image_size",
            "order" => 10,
            "label" => __("Adverts List", "adverts"),
            "hint" => __("This image size is being used in the [adverts_list] shortcode.", "adverts")
        ),
        array(
            "name" => "image_size__adverts-upload-thumbnail",
            "type" => "adext_field_image_size",
            "order" => 10,
            "label" => __("Upload Thumbnail", "adverts"),
            "hint" => __("This image size is being used in gallery thumbnails.", "adverts")
        )
    )
));

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

    <span style="opacity:0.65"><?php _e("max. width", "adverts") ?></span>
    <input type="number" name="<?php echo esc_attr( sprintf( $name, "width" ) ) ?>" step="1" min="0" style="width:60px" value="<?php echo esc_attr( $width ) ?>" /> 
    
    <strong>x</strong>
    
    <span style="opacity:0.65"><?php _e("max. height", "adverts") ?></span>
    <input type="number" name="<?php echo esc_attr( sprintf( $name, "height" ) ) ?>" step="1" min="0" style="width:60px" value="<?php echo esc_attr( $height ) ?>" />
    
    <label for="<?php echo esc_attr( sprintf( $name, "crop" ) ) ?>" style="opacity:0.65; vertical-align: initial" ><?php _e("crop image", "adverts") ?></label>
    <input type="checkbox" name="<?php echo esc_attr( sprintf( $name, "crop" ) ) ?>" id="<?php echo esc_attr( sprintf( $name, "width" ) ) ?>" value="1" <?php checked($crop) ?> />

    <?php
    
}
