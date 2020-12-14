<?php
/**
 * List of main adverts functions
 * 
 * @package     Adverts
 * @copyright   Copyright (c) 2015, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Returns config value
 * 
 * @global array $adverts_config
 * @global array $adverts_namespace
 * @param string $param Should be module_name.param_name
 * @return mixed
 */
function adverts_config($param = null) {
    global $adverts_config, $adverts_namespace;

    if(stripos($param, '.') === false) {
        $module = 'config';
    } else {
        list($module, $param) = explode(".", $param);
    }
    
    if( !isset($adverts_namespace[$module]) ) {
        trigger_error('Incorrect module name ['.$module.']', E_USER_ERROR);
    }
    
    $default = $adverts_namespace[$module]['default'];
    $option_name = $adverts_namespace[$module]['option_name'];
    
    if($adverts_config === null) {
        $adverts_config = array();
    }
    
    if(!isset($adverts_config[$module])) {
        $adverts_config[$module] = get_option( $option_name );
    }

    if($adverts_config[$module] === false) {
        $adverts_config[$module] = array();
        add_option( $option_name, $adverts_config[$module]);
    }

    // merge with defaults
    $adverts_config[$module] = array_merge( $default, $adverts_config[$module] );

    if( empty($param) || $param == "ALL" ) {
        return $adverts_config[$module];
    }

    if(isset($adverts_config[$module][$param]) && 
        (!empty($adverts_config[$module][$param]) || is_numeric($adverts_config[$module][$param]) || is_array($adverts_config[$module][$param]) || $adverts_config[$module][$param] === "")) {
        return $adverts_config[$module][$param];
    } else {
        return $default;
    }
}

/**
 * Return config default values
 * 
 * @global array $adverts_namespace
 * @param string $param
 * @since 0.1
 * @return array
 */
function adverts_config_default($param = null) {
    global $adverts_namespace;

    if(stripos($param, '.') === false) {
        $module = 'config';
    } else {
        list($module, $param) = explode(".", $param);
    }
    
    if( !isset($adverts_namespace[$module]) ) {
        trigger_error('Incorrect module name ['.$module.']', E_USER_ERROR);
    }
    
    if( !empty($param) ) {
        return $adverts_namespace[$module]['default'][$param];
    } else {
        return $adverts_namespace[$module]['default'];
    }
}

/**
 * Sets config value
 * 
 * Note this function does NOT save config in DB.
 * 
 * @global array $adverts_config
 * @global array $adverts_namespace
 * @param string $param
 * @param mixed $value
 * @since 0.1
 * @return void
 */
function adverts_config_set($param, $value) {
    global $adverts_config, $adverts_namespace;
    
    if(stripos($param, '.') === false) {
        $module = 'config';
    } else {
        list($module, $param) = explode(".", $param);
    }
    
    if( !isset($adverts_namespace[$module]) ) {
        trigger_error('Incorrect module name ['.$module.']', E_USER_ERROR);
    }
    
    $default = $adverts_namespace[$module]['default'];
    $option_name = $adverts_namespace[$module]['option_name'];
    
    $adverts_config[$module][$param] = $value;
}

/**
 * Saves config in DB 
 * 
 * @uses update_option()
 * 
 * @global array $adverts_config
 * @global array $adverts_namespace
 * @param string $module
 * @since 0.1
 * @return void
 */
function adverts_config_save( $module = null ) {
    global $adverts_config, $adverts_namespace;
    
    if( $module === null ) {
        $module = "config";
    }
    
    if( !isset($adverts_namespace[$module]) ) {
        trigger_error('Incorrect module name ['.$module.']', E_USER_ERROR);
    }
    
    $default = $adverts_namespace[$module]['default'];
    $option_name = $adverts_namespace[$module]['option_name'];
    
    update_option( $option_name, $adverts_config[$module] );
}

/**
 * Returns taxonomy meta value.
 * 
 * This is a basic implementation of terms meta data. The terms meta is being stored
 * in wp_options table.
 * 
 * @param string $taxonomy Taxonomy name (usually advert_category)
 * @param int $term_id Term ID
 * @param string $meta_key Meta field name
 * @param mixed $default Default value if not value is found in DB
 * @since 0.3
 * @return mixed Saved data in DB (probably string | int or array)
 */
function adverts_taxonomy_get($taxonomy, $term_id, $meta_key, $default = null) {
    
    $option = get_option($taxonomy);
    
    if(!isset($option[$term_id])) {
        return $default;
    }
    
    if(!isset($option[$term_id][$meta_key])) {
        return $default;
    }
    
    return $option[$term_id][$meta_key];
}

/**
 * Saves taxonomy meta value
 * 
 * This is a basic implementation of terms meta data. The terms meta is being stored
 * in wp_options table.
 * 
 * @param string $taxonomy Taxonomy name (usually advert_category)
 * @param int $term_id Term ID
 * @param string $meta_key Meta field name
 * @param mixed $value Value that will be saved in DB
 * @since 0.3
 * @return void
 */
function adverts_taxonomy_update($taxonomy, $term_id, $meta_key, $value) {
    
    $option = get_option($taxonomy);
    
    if(!is_array($option)) {
        $option = array();
    }
    
    if(!isset($option[$term_id])) {
        $option[$term_id] = array();
    }
    
    $option[$term_id][$meta_key] = $value;
    
    update_option($taxonomy, $option);
}

/**
 * Returns default temporary status for posts that are being submitted
 * via frontend.
 * 
 * Note that the status is applied to ads that user did not complete adding (yet).
 * 
 * @since 0.1
 * @return string
 */
function adverts_tmp_post_status() {
    return apply_filters("adverts_tmp_post_status", "advert_tmp");
}

/**
 * Returns value from $_POST or $_GET table by $key.
 * 
 * If the $key does not exist in neither of global tables $default value
 * is returned instead.
 * 
 * @param string $key
 * @param mixed $default
 * @since 0.1
 * @return mixed Array or string
 */
function adverts_request($key, $default = null) {
    if(isset($_POST[$key])) {
        return stripslashes_deep($_POST[$key]);
    } elseif(isset($_GET[$key])) {
        return stripslashes_deep($_GET[$key]);
    } else {
        return $default;
    }
}

/**
 * Checks if uploaded file is an image
 * 
 * The $file variable should be an item from $_FILES array.
 * 
 * @param array $file Item from $_FILES array
 * @since 0.1
 * @return array
 */
function adverts_file_is_image( $file ) {

    if ( !isset($file["name"]) || !isset($file["type"]) ) {
        return $file;
    }

    $ext = preg_match('/\.([^.]+)$/', $file["name"], $matches) ? strtolower($matches[1]) : false;

    $image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );

    if ( 'image/' == substr($file["type"], 0, 6) || $ext && 'import' == $file["type"] && in_array($ext, $image_exts) ) {
        return $file;
    }
    
    $file["error"] = __("Uploaded file is NOT an image", "wpadverts" );
    
    return $file;
}

/**
 * Formats float as a currency
 * 
 * Functions uses currency information to format the number.
 * 
 * @param string $price Price as float
 * @since 0.1
 * @return string
 */
function adverts_price( $price ) {
    
    if( empty($price) ) {
        return null;
    }
    
    $c = Adverts::instance()->get("currency");
    $number = number_format( (float)$price, $c['decimals'], $c['char_decimal'], $c['char_thousand']);
    
    if( empty($c['sign'] ) ) {
        $sign = $c['code'];
    } else {
        $sign = $c['sign'];
    }
    
    if( $c['sign_type'] == 'p' ) {
        return $sign.$number;
    } else {
        return $number.$sign;
    }
    
}

/**
 * Returns formatted price.
 * 
 * @param float     $post_id    Price to format
 * @param int       price
 * @return string               Formatted Price
 */
function adverts_get_the_price( $post_id = null, $price = null ) {
    
    if( $post_id === null ) {
        $post_id = get_the_ID();
    }
    
    if( $price === null ) {
        $price = get_post_meta( $post_id, "adverts_price", true);
    }
    
    return apply_filters( "adverts_get_the_price", adverts_price( $price ), $price, $post_id );
}

/**
 * Returns image that will be displayed on adverts list.
 * 
 * Function returns either the main image or first image on the list if the main
 * image was not selected.
 * 
 * @since 0.1
 * @since 1.2.1 - The main image is a first image on the list if Featured image is not selected.
 * 
 * @param   int     $id     Post ID
 * @return  mixed           Image URL or NULL
 */
function adverts_get_main_image( $id ) {
    
    $thumb_id =  adverts_get_main_image_id( $id );
    
    if( $thumb_id !== null ) {
        $image = wp_get_attachment_image_src( $thumb_id, 'adverts-list' );
        
        if(isset( $image[0] ) ) {
            return $image[0];
        }
    }
    
    return null;
}

/**
 * Returns ID of the image that will be displayed on adverts list.
 * 
 * @since   1.3.3
 * @param   int     $id     Advert ID
 * @return  int             Either main image Attachment ID or null
 */
function adverts_get_main_image_id( $id ) {
    
    $thumb_id = get_post_thumbnail_id( $id );   
    
    if($thumb_id) {
        $image = wp_get_attachment_image_src( $thumb_id, 'adverts-list' );
        
        if(isset( $image[0] ) ) {
            return $thumb_id;
        }
    } 
    
    $children = get_children( array( 'post_parent' => $id ) );
    $attach = array();

    if( empty( $children ) ) {
        return null;
    }

    if( isset( $children[$thumb_id] ) ) {
        $attach[$thumb_id] = $children[$thumb_id];
        unset($children[$thumb_id]);
    }

    $attach += $children;
    $images = adverts_sort_images($attach, $id);
    
    foreach($images as $tmp_post) {
        $image = wp_get_attachment_image_src( $tmp_post->ID , 'adverts-list' ); 
        
        if(isset( $image[0] ) ) {
            return $tmp_post->ID;
        }
        
    }
    
    return null;
}

/**
 * Dynamically replace post content with Advert template.
 * 
 * This function is applied to the_content filter.
 * 
 * @global WP_Query $wp_query
 * @param string $content
 * @since 0.1
 * @return string
 */
function adverts_the_content($content) {
    global $wp_query;
    
    if ( is_singular( wpadverts_get_post_types() ) && in_the_loop() ) {
        ob_start();
        $post_id = get_the_ID();
        
        $post_content = get_post( $post_id )->post_content;
        $post_content = wp_kses($post_content, wp_kses_allowed_html( 'post' ) );
        $post_content = apply_filters( "adverts_the_content", $post_content );
        
        include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/single.php' );
        $content = ob_get_clean();
    }

    return $content;
}

/**
 * Replaces Main Query objects.
 * 
 * When browsing by category by default WP will display list of categories 
 * (depending on the theme), we do not want that, instead we want to take control
 * over the page content. In order to do that this function removes main query
 * list of terms and replaces them with post that holds adverts list.
 * 
 * This functionality was replaced with Adverts_Taxonomies class
 * 
 * @deprecated since version 1.4.0
 * 
 * @param array $posts
 * @param WP_Query $query
 * @return array Post objects
 */
function adverts_posts_results( $posts, $query ) {
    
    _deprecated_function(__FUNCTION__, "1.4.0" );
    
    if( $query->is_main_query() && $query->is_tax("advert_category") ) {
        
        $title = sprintf( __("Category: %s", "wpadverts"), $query->get_queried_object()->name );
        $post = get_post( adverts_config( 'config.ads_list_id' ) );
        
        if( ! is_null( $post ) ) {
            $post->post_title = apply_filters( "adverts_category_the_title", $title);
            return array($post);
        } else {
            return array();
        }
        
    } else {
        return $posts;
    }
}


/**
 * Change Advert Category tax archive template to page template.
 * 
 * When browsing by advert category page template we do not want to use default
 * archive template, we want to use page template in order to use [adverts_list]
 * shortcode.
 * 
 * This additionally requires updating page title {@see adverts_category_the_title()}.
 * 
 * @global WP_Query $wp_query
 * @param string $template Page template path
 * @return string Page template path
 */
function adverts_template_include( $template ) {
    
    $possible_templates = array(
        'page',
        'single',
        'singular',
        'index',
    );

    foreach ( $possible_templates as $possible_template ) {
        $path = get_query_template( $possible_template );
        if ( $path ) {
            return $path;
        }
    }

    return $template;
}





/**
 * Shows Term description in [adverts_list] if not empty.
 * 
 * This function is executed using adverts_sh_list_before action in
 * /wpadverts/templates/index.php file.
 * 
 * @since 1.1.3
 * @global WP_Query $wp_query   Main WP Query
 * @param array $params         [adverts_list] shortcode params.
 * @return void
 */
function adverts_list_show_term_description( $params ) {
    global $wp_query;
    
    $term = $wp_query->get_queried_object();
    
    if( ! $term instanceof WP_Term ) {
        return;
    }
    
    echo term_description($term, $term->taxonomy);
}


/**
 * Remove post thumbnail for Adverts
 * 
 * @global WP_Post $post
 * @param string $html
 * @since 0.1
 * @return string
 */
function adverts_post_thumbnail_html($html) {
    global $post;
    
    if( is_object( $post ) && 'advert' == $post->post_type && in_the_loop() ) {
        $html = '';
    }
    
    return $html;
    
}

/**
 * Check if field has errors
 * 
 * This function is mainly used in templates when generating form layout.
 * 
 * @param array $field
 * @since 0.1
 * @return boolean
 */
function adverts_field_has_errors( $field ) {
    if( isset($field["error"]) && is_array($field["error"]) ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if Adverts_Form field has $validator
 * 
 * This function is mainly used in templates when generating form layout.
 * 
 * @param array $field
 * @param string $validator
 * @since 0.1
 * @return boolean
 */
function adverts_field_has_validator( $field, $validator ) {
    if( !isset($field["validator"]) || !is_array($field["validator"]) ) {
        return false;
    }
    
    foreach($field["validator"] as $v) {
        if($v["name"] == $validator) {
            return true;
        }
    }
    
    return false;
}

/**
 * Checks if Adverts_Form field is required
 * 
 * The required field is a field with "is_required" or "upload_limit:min>0" 
 * validator.
 * 
 * @param array $field
 * @param string $validator
 * @since 0.1
 * @return boolean
 */
function adverts_field_is_required( $field ) {
    
    if( adverts_field_has_validator( $field, "is_required") ) {
        return true;
    }
    
    if( adverts_field_has_validator( $field, "upload_limit" ) ) {
        foreach($field["validator"] as $v) {
            if($v["name"] == "upload_limit" && isset( $v["params"]["min"] ) && $v["params"]["min"] > 0 ) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * Returns form field rendering function
 * 
 * This function is mainly used in templates when generating form layout.
 * 
 * @param array $field
 * @since 0.1
 * @return string
 */
function adverts_field_get_renderer( $field ) {
    $f = Adverts::instance()->get("form_field");
    $f = $f[$field["type"]];

    return $f["renderer"];
}

/**
 * Registers form field
 * 
 * This function is mainly used in templates when generating form layout.
 * 
 * @param string $name
 * @param mixed $params
 * @since 0.1
 * @return void
 */
function adverts_form_add_field( $name, $params ) {
    $field = Adverts::instance()->get("form_field", array());
    $field[$name] = $params;
    
    Adverts::instance()->set("form_field", $field);
}

/**
 * Registers form filter
 * 
 * @param string $name
 * @param array $params
 * @since 0.1
 * @return void
 */
function adverts_form_add_filter( $name, $params ) {
    $field_filter = Adverts::instance()->get("field_filter", array());
    $field_filter[$name] = $params;
    
    Adverts::instance()->set("field_filter", $field_filter);
}

/**
 * Registers form validator
 * 
 * @param string $name
 * @param array $params
 * @since 0.1
 * @return void
 */
function adverts_form_add_validator( $name, $params ) {
    $field_validator = Adverts::instance()->get("field_validator", array());
    $field_validator[$name] = $params;
    
    Adverts::instance()->set("field_validator", $field_validator);
}

/**
 * Is Required VALIDATOR
 * 
 * The function checks if $data is empty
 * 
 * @param mixed $data
 * @return string|boolean
 */
function adverts_is_required( $data ) {

    if( empty($data) && !is_numeric($data) ) {
        return "empty";
    } else {
        return true;
    }
}

/**
 * Is Email VALIDATOR
 * 
 * Checks if $email is valid email address
 * 
 * @uses is_email()
 * @param string $email
 * @return boolean|string
 */
function adverts_is_email( $email ) {
    if( is_email( $email ) ) {
        return true;
    } else {
        return "invalid";
    }
}

/**
 * Is Email Registered VALIDATOR
 * 
 * Checks if $email is already being used by registered user. That is validator
 * checks in DB wp_users.user_email column for matching email address and returns
 * "invalid" error if found.
 * 
 * @param string $email
 * @return boolean|string
 */
function adverts_is_email_registered( $email ) {
    
    // Run this validator only from [adverts_add] shortcode with "Create account .."
    // checkbox checked.
    if( is_admin() || !adverts_request("_adverts_account") ) {
        return true;
    }
    
    if( get_user_by( "email", $email ) === false ) {
        return true;
    } else {
        return "invalid";
    }
}

/**
 * Is URL VALIDATOR
 * 
 * Checks if $url is valid URL
 * 
 * @since 1.0.12
 * @uses is_email()
 * @param string $url
 * @return boolean|string
 */
function adverts_is_url( $url ) {
    if( preg_match( "#^(http|https)://[A-z0-9\-\_\./\?&;=,\#\!\%\+]+$#i" , $url ) ) {
        return true;
    } else {
        return "invalid";
    }
}

/**
 * Is Integer VALIDATOR
 * 
 * Checks if $value is integer 0 or greater.
 * 
 * @param string $value
 * @since 0.1
 * @return boolean|string
 */
function adverts_is_integer( $value ) {
    
    if( filter_var( $value, FILTER_VALIDATE_INT ) !== false ) {
        return true;
    } else {
        return "invalid";
    }
}

/**
 * Is Number VALIDATOR
 * 
 * Checks if $value is a number.
 * 
 * @param string $value
 * @since 1.0.12
 * @return boolean|string
 */
function adverts_is_number( $value ) {
    
    if( filter_var( $value, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) !== false ) {
        return true;
    } else {
        return "invalid";
    }
}

/**
 * String Length VALIDATOR
 * 
 * @param mixed $data
 * @param array $params Validation parameters (min and max length values)
 * @since 0.1
 * @return string|boolean
 */
function adverts_string_length( $data, $params = null ) {

    if( isset( $params["min"] ) && strlen( $data ) < $params["min"] ) {
        return "to_short";
    } 
    
    if( isset( $params["max"] ) && strlen( $data ) > $params["max"] ) {
        return "to_long";
    } 
    
    return true;
}

/**
 * Max choices VALIDATOR
 * 
 * This validator is applicable only to dropdown and checkbox fields.
 * 
 * @param mixed $data
 * @param array $params Validation parameters (min and max length values)
 * @since 1.1.2
 * @return string|boolean
 */
function adverts_max_choices( $data, $params = null ) {
    if( ! is_array( $data ) ) {
        $data = array( $data );
    }
    
    if( count( $data ) > $params["max_choices"] ) {
        return "invalid";
    } else {
        return true;
    }
}

/**
 * Choices VALIDATOR
 * 
 * This validator checks if the selected values in checkbox, select or radio
 * are in a valid values range. 
 * 
 * In other words it checks if the values were selected from available options
 * in the form.
 * 
 * @since 1.2.8
 * 
 * @param mixed $data
 * @param array $params Validation parameters
 * @param array $field  Field definition
 * @return string|boolean
 */
function adverts_verify_choices( $data, $params = null, $field = null ) {
    $allowed = array();
    
    foreach( $field["options"] as $option ) {
        if( ! isset( $option["disabled"] ) || ! $option["disabled"] ) {
            $allowed[] = $option["value"];
        }
    }
    
    if( ! is_array( $data ) ) {
        $data = array( $data );
    }
    
    foreach( $data as $input ) {
        if( ! in_array( $input, $allowed ) ) {
            return "invalid";
        }
    }
    
    return true;
}

/**
 * Upload Limit VALIDATOR
 * 
 * This validator checks if user have reached maximum files upload limit.
 * 
 * @param array $file       An item from $_FILES array
 * @param array $params     Validation parameters (integer files)
 * @since 1.2.0
 * @return string|boolean
 */
function adverts_validate_upload_limit( $file, $params = null ) {
   
    $post_id = intval( adverts_request( "post_id" ) );
    
    if( $post_id === 0 ) {
        $post_id = intval( adverts_request( "_post_id" ) );
    } 
    
    if( $post_id === 0 ) {
        $post_id = intval( adverts_request( "advert_id" ) );
    }

    if( defined( "DOING_AJAX" ) && DOING_AJAX ) {
        $ignore_min_limit = true;
    } else {
        $ignore_min_limit = false;
    }
    
    if( $post_id > 0 ) {
        $attachments = get_children( array( 'post_parent' => $post_id ) );
        $images = count( $attachments );
    } else {
        $images = 0;
    }
    
    if( $file === null ) {
        $i = 0;
    } else {
        $i = 1;
    }

    if( isset( $params["max"] ) && $images + $i > $params["max"] ) {
        return "max_limit";
    }
    
    if( $ignore_min_limit ) {
        return true;
    }
    
    if( $file === null && isset( $params["min"] ) && $images < $params["min"] ) {
        return "min_limit";
    }
    
    return true;
}

/**
 * File Size Upload VALIDATOR
 * 
 * Checks if uploaded file size is allowed
 * 
 * @param array $file       An item from $_FILES array
 * @param array $params     Validation parameters (integer files)
 * @since 1.2.0
 * @return string|boolean
 */
function adverts_validate_upload_size( $file, $params = null ) {
    
    if ( !isset($file["name"]) || !isset($file["size"]) ) {
        return true;
    }
    
    $arr = array( "too_big" => "max", "too_small" => "min" );
    
    foreach( $arr as $err => $prop ) {
        
        if( ! isset( $params[$prop] ) || empty( $params[$prop] ) ) {
            continue;
        }
        
        $size = str_replace( array( " " ), array( "" ), $params[$prop] );
        $size = trim( $size );

        $number = substr( $size, 0, -2 );

        switch( strtoupper( substr( $size, -2 ) ) ) {
            case "KB":
                $fsize = $number*1024;
                break;
            case "MB":
                $fsize = $number*pow(1024,2);
                break;
            case "GB":
                $fsize = $number*pow(1024,3);
                break;
            case "TB":
                $fsize = $number*pow(1024,4);
                break;
            case "PB":
                $fsize = $number*pow(1024,5);
                break;
            default:
                $fsize = $size;
                break;
        }

        if( $prop == "max" && $file["size"] > $fsize ) {
            return $err;
        }
        if( $prop == "min" && $file["size"] < $fsize ) {
            return $err;
        }
    }
    
    return true;
    

}

/**
 * File Type Upload VALIDATOR
 * 
 * Checks if uploaded file extensions is allowed in the configuration
 * 
 * @param array $file       An item from $_FILES array
 * @param array $params     Validation parameters (integer files)
 * @since 1.2.0
 * @return string|boolean
 */
function adverts_validate_upload_type( $file, $params = null ) {
    
    if ( !isset($file["name"]) || !isset($file["type"]) ) {
        return true;
    }
    
    if( isset( $params["allowed"] ) ) {
        $a = $params["allowed"];
    } else {
        $a = array();
    }
    
    $ext = strtolower( pathinfo($file["name"], PATHINFO_EXTENSION) );
    $type = strtolower( $file["type"] );

    if( isset( $params["extensions"] ) && is_array( $params["extensions"] ) ) {
        $allowed_ext = array_map( "trim", $params["extensions"] );
    } else {
        $allowed_ext = array();
    }
    
    if( isset( $params["types"] ) && is_array( $params["types"] ) ) {
        $allowed_types = array_map( "trim", $params["types"] );
        $has_types = true;
    } else {
        $allowed_types = array();
        $has_types = false;
    }
    
    if( in_array( "video", $a ) ) {
        $allowed_types = array_merge( $allowed_types, array( "video/webm", "video/mp4", "video/ogv" ) );
        $allowed_ext = array_merge( $allowed_ext, array( "webm", "mp4", "ogv" ) );
    }
    
    if( in_array( "audio", $a ) ) {
        $allowed_types = array_merge( $allowed_types, array( "audio/webm", "audio/mp4", "audio/ogg") );
        $allowed_ext = array_merge( $allowed_ext, array( "webm", "mp4", "ogg" ) );
    }
    
    if( in_array( "image", $a ) ) {
        $allowed_types = array_merge( $allowed_types, array( "image/jpeg", "image/jpe", "image/jpg", "image/gif", "image/png", "image/webp" ) );
        $allowed_ext = array_merge( $allowed_ext, array( "jpeg", "jpe", "jpg", "gif", "png", "webp" ) );
    }

    if( in_array( $ext, $allowed_ext) && ( in_array( $type, $allowed_types ) || ! $has_types ) ) {
        return true;
    } else {
        return "invalid";
    }
}

/**
 * File (image) Dimensions VALIDATOR
 * 
 * Checks if uploaded image size (as in width and height) is within allowed
 * image dimensions
 * 
 * $params
 * - strict (boolean) - if true the validation will fail if image size cannot be checked
 * - min_width (int) - minimum image width
 * - min_height (int) - minimum image height
 * - max_width (int) - maximum image width
 * - max_height (int) - maximum image height
 * 
 * @param array $file       An item from $_FILES array
 * @param array $params     Validation parameters (integer files)
 * @since 1.2.0
 * @return string|boolean
 */
function adverts_validate_upload_dimensions( $file, $params = null ) {
    if( ! isset( $file["type"]) || stripos( $file["type"], "image/" ) !== 0 ) {
        // this validator is applied to images only for other files it returns true
        return true;
    } 
    
    $imagesize = getimagesize( $file["tmp_name"] );
    
    if( $imagesize === false ) {
        // cannot check image size, if size check is required return error
        // otherwise accept the uploaded image
        if( isset( $params["strict"] ) && $params["strict"] ) {
            return "cannot_check";
        } else {
            return true;
        }
    }
    
    list( $width, $height ) = $imagesize;
    
    if( isset( $params["min_width"] ) && $params["min_width"] && $width < $params["min_width"] ) {
        return "incorrect_min_width";
    }
    
    if( isset( $params["max_width"] ) && $params["max_width"] && $width > $params["max_width"] ) {
        return "incorrect_max_width";
    }

    if( isset( $params["min_height"] ) && $params["min_height"] && $height < $params["min_height"] ) {
        return "incorrect_min_height";
    }
    
    if( isset( $params["max_height"] ) && $params["max_height"] && $height > $params["max_height"] ) {
        return "incorrect_max_height";
    }
    
    return true;
}

/**
 * Money To Float FILTER
 * 
 * Filters currency and returns it as a float
 * 
 * @param type $data
 * @since 0.1
 * @return type
 */
function adverts_filter_money( $data ) {
    
    $cleanString = preg_replace('/([^0-9\.,])/i', '', $data);
    $onlyNumbersString = preg_replace('/([^0-9])/i', '', $data);

    $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

    $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
    $removedThousendSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);

    return (float) str_replace(',', '.', $removedThousendSeparator);
}

/**
 * URL FILTER
 * 
 * Makes sure the provided $data is a valid URL (if possible)
 * 
 * @param string $data
 * @since 1.0.12
 * @return string
 */
function adverts_filter_url( $data ) {
    return esc_url_raw( $data );
}

/**
 * Integer FILTER
 * 
 * Makes sure the provided $data is an absolute integer.
 * 
 * @param string $data
 * @since 1.0.12
 * @return string
 */
function adverts_filter_int( $data ) {
    return absint( $data );
}

/**
 * Float FILTER
 * 
 * Makes sure the provided $data is a floating point number.
 * 
 * @param string $data
 * @since 1.0.12
 * @return float
 */
function adverts_filter_float( $data ) {
    return filter_var( $data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
}

/**
 * Float FILTER
 * 
 * Alias of adverts_filter_float() function
 * 
 * @see adverts_filter_float()
 * 
 * @param string $data
 * @since 1.1.3
 * @return float
 */
function adverts_filter_number( $data ) {
    return adverts_filter_float( $data );
}

/**
 * Form hidden input renderer
 * 
 * Prints (to browser) HTML for <input type="hidden" /> input
 * 
 * $field params:
 * - name: string
 * - value: mixed (scalar or array)
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function adverts_field_hidden( $field ) {
    $html = new Adverts_Html("input", array(
        "type" => "hidden",
        "name" => $field["name"],
        "id" => $field["name"],
        "class" => isset($field["class"]) ? $field["class"] : null,
        "value" => isset($field["value"]) ? $field["value"] : "",
    ));
    
    echo $html->render();
}

/**
 * Form text/paragraph renderer
 * 
 * Prints (to browser) HTML for <span></span> input
 * 
 * $field params:
 * - content: string (text to display)
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function adverts_field_label( $field ) {
    $html = new Adverts_Html("span", array(
        "class" => "adverts-flash adverts-flash-info"
    ), $field["content"]);
    
    echo $html->render();
}

/**
 * Form input text renderer
 * 
 * Prints (to browser) HTML for <input type="text" /> input
 * 
 * $field params:
 * - name: string
 * - value: mixed (scalar or array)
 * - class: string (HTML class attribute)
 * - placeholder: string
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function adverts_field_text( $field ) {
    
    $attr = array(
        "type" => isset( $field["subtype"] ) ? $field["subtype"] : "text",
        "name" => $field["name"],
        "id" => $field["name"],
        "value" => isset($field["value"]) ? $field["value"] : "",
        "placeholder" => isset($field["placeholder"]) ? $field["placeholder"] : null,
        "class" => isset($field["class"]) ? $field["class"] : null
    );
    
    if( isset( $field["attr"] ) && is_array( $field["attr"] ) ) {
        foreach( $field["attr"] as $key => $value ) {
            if( $value !== null && is_scalar( $value ) ) {
                $attr[$key] = $value;
            }
        }
    }
    
    $html = new Adverts_Html( "input", $attr );
    
    echo $html->render();
}


/**
 * Form input text renderer
 * 
 * Prints (to browser) HTML for <input type="text" /> input
 * 
 * $field params:
 * - name: string
 * - value: mixed (scalar or array)
 * - class: string (HTML class attribute)
 * - placeholder: string
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function adverts_field_password( $field ) {
    
    $attr = array(
        "type" => "password",
        "name" => $field["name"],
        "id" => $field["name"],
        "value" => isset($field["value"]) ? $field["value"] : "",
        "placeholder" => isset($field["placeholder"]) ? $field["placeholder"] : null,
        "class" => isset($field["class"]) ? $field["class"] : null
    );
    
    if( isset( $field["attr"] ) && is_array( $field["attr"] ) ) {
        foreach( $field["attr"] as $key => $value ) {
            if( $value !== null && is_scalar( $value ) ) {
                $attr[$key] = $value;
            }
        }
    }
    
    $html = new Adverts_Html( "input", $attr );
    
    echo $html->render();
}

/**
 * Form dropdown renderer
 * 
 * Prints (to browser) HTML for <select>...</select> input
 * 
 * $field params:
 * - name: string
 * - value: mixed (scalar or array)
 * - class: string (HTML class attribute)
 * - max_choices: integer
 * - attr: array (list of additional HTML attributes)
 * - empty_option: boolean (true if you want to add epty option at the beginning)
 * - empty_option_text: string
 * - options_callback: mixed
 * - options: array (for example array(array("value"=>1, "text"=>"title")) )
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function adverts_field_select( $field ) {
    
    $i = 1;
    $html = "";
    $name = $field["name"];
    $multiple = false;
    
    if(isset($field["class"]) && $field["class"]) {
        $classes = $field["class"];
    } else {
        $classes = null;
    }

    if(isset($field["max_choices"]) && $field["max_choices"]>1) {
        $max = $field["max_choices"];
        $name .= "[]";
        $multiple = "multiple";
        $classes = "$classes adverts-multiselect adverts-max-choices[$max]";
        
        wp_enqueue_script( 'adverts-multiselect' );
    }

    $options = array(
        "id" => $field["name"],
        "name" => $name,
        "class" => $classes,
        "multiple" => $multiple
    );

    if( isset( $field["attr"] ) && is_array( $field["attr"] ) ) {
        foreach( $field["attr"] as $key => $value ) {
            if( $value !== null && is_scalar( $value ) ) {
                $options[$key] = $value;
            }
        }
    }
    
    if($multiple && isset($field["empty_option_text"])) {
        $options["data-empty-option-text"] = $field["empty_option_text"];
    }
    
    if(!$multiple && isset($field["empty_option"]) && $field["empty_option"]) {
        if(isset($field["empty_option_text"]) && !empty($field["empty_option_text"])) {
            $html .= '<option value="">'.esc_html($field["empty_option_text"]).'</options>';
        } else {
            $html .= '<option value="">&nbsp;</option>'; 
        }
    }

    if(isset($field["options_callback"]) && !empty($field["options_callback"])) {
        $opt = call_user_func( $field["options_callback"], $field );
    } elseif(isset($field["options"])) {
        $opt = $field["options"];
    } else {
        trigger_error("You need to specify options source for field [{$field['name']}].", E_USER_ERROR);
        $opt = array();
    }
    
    if(!is_array($opt)) {
        $opt = array();
    }
    
    foreach($opt as $k => $v) {
        
        $selected = null;
        $depth = null;
        $disabled = null;

        
        if( isset( $v["id"] ) ) {
            $id = $id.'_'.$i;
        } else {
            $id = null;
        }
        
        $id = apply_filters( "adverts_form_field_option_id", $id, $v, $field, $i );
        
        if( isset( $v["disabled"] ) ) {
            $disabled = $v["disabled"];
        }
        
        if(in_array($v["value"], (array)$field["value"])) {
            $selected = "selected";
        }
        
        if(isset($v["depth"])) {
            $depth = $v["depth"];
        }
        
        if(!$multiple) {
            $padding = str_repeat("&nbsp;", $depth * 2);
        } else {
            $padding = "";
        }
        
        $o = new Adverts_Html("option", array(
            "id" => $id,
            "value" => $v["value"],
            "data-depth" => $depth,
            "selected" => $selected,
            "disabled" => $disabled
        ), $padding . $v["text"]);

        $html .= $o->render();
        
        $i++;
    }

    $input = new Adverts_Html("select", $options, $html);
    $input->forceLongClosing();
    
    echo $input->render();
}

/**
 * Form textarea renderer
 * 
 * Prints (to browser) HTML for <textarea></textarea> input
 * 
 * $field params:
 * - value: string
 * - mode: plain-text | tinymce-mini | tinymce-full
 * - placeholder: string (for plain-text only)
 * - name: string
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function adverts_field_textarea( $field ) {
    
    $value = '';
    
    if(isset($field["value"])) {
        $value = $field["value"];
    }
    
    if($field["mode"] == "plain-text") {
        $html = new Adverts_Html("textarea", array(
            "name" => $field["name"],
            "rows" => 10,
            "cols" => 50,
            "placeholder" => isset($field["placeholder"]) ? $field["placeholder"] : null,
            "class" => isset($field["class"]) ? $field["class"] : null,
        ), $value);
        $html->forceLongClosing();
        
        echo $html->render();
        
    } elseif($field["mode"] == "tinymce-mini") {
        $params = apply_filters( "adverts_field_textarea_tinymce_params", array(
            "wpautop" => true,
            "quicktags" => false, 
            "media_buttons" => false, 
            "teeny" => false,
            "textarea_rows" => 8,
            'tinymce' => array(
                'toolbar1' => 'bold,italic,strikethrough,bullist,numlist,blockquote,justifyleft,justifycenter,justifyright,link,unlink,spellchecker,wp_adv',
                'theme_advanced_buttons2' => 'formatselect,justifyfull,forecolor,pastetext,pasteword,removeformat,charmap,outdent,indent,undo,redo',

                'theme_advanced_buttons1' => 'bold,italic,strikethrough,bullist,numlist,blockquote,justifyleft,justifycenter,justifyright,link,unlink,spellchecker,wp_adv',
                'theme_advanced_buttons2' => 'formatselect,justifyfull,forecolor,pastetext,pasteword,removeformat,charmap,outdent,indent,undo,redo',
             )
        ), $field );
        wp_editor($field["value"], $field["name"], $params);
    } elseif($field["mode"] == "tinymce-full") {
        $params = apply_filters( "adverts_field_textarea_tinymce_params", array(), $field );
        wp_editor($field["value"], $field["name"], $params );
    } else {
        echo "Parameter [mode] is missing in the form!";
    }
}

/**
 * Form checkbox input(s) renderer
 * 
 * Prints (to browser) HTML for <input type="checkox" /> input
 * 
 * $field params:
 * - name: string
 * - value: mixed (scalar or array)
 * - options: array (for example array(array("value"=>1, "text"=>"title")) )
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function adverts_field_checkbox( $field ) {
    
    $opts = "";
    $i = 1;
    
    if( !isset( $field["rows"] ) ) {
        $field["rows"] = 1;
    }
    
    if( !isset( $field["value"] ) ) {
        $value = array();
    } elseif( !is_array( $field["value"] ) ) {
        $value = (array)$field["value"];
    } else {
        $value = $field["value"];
    }
    
    if(isset($field["options_callback"]) && !empty($field["options_callback"])) {
        $opt = call_user_func( $field["options_callback"], $field );
    } elseif(isset($field["options"])) {
        $opt = $field["options"];
    } else {
        trigger_error("You need to specify options source for field [{$field['name']}].", E_USER_ERROR);
        $opt = array();
    }

    foreach($opt as $v) {
        
        if( isset( $v["id"] ) ) {
            $id = $v["id"];
        } else {
            $id = $field["name"];
        }
        
        $id = apply_filters( "adverts_form_field_option_id", $id.'_'.$i, $v, $field, $i );
        
        $checkbox = new Adverts_Html("input", array(
            "type" => "checkbox",
            "name" => $field["name"].'[]',
            "id" => $id,
            "value" => $v["value"],
            "checked" => in_array($v["value"], $value) ? "checked" : null
        ));

        $label = new Adverts_Html("label", array(
            "for" => $id
        ), $checkbox->render() . ' ' . $v["text"]);
        
        if( isset( $field["class"] ) ) {
            $class = $field["class"];
        } else {
            $class = null;
        }
        
        if( isset( $v["depth"] ) ) {
            $depth = $v["depth"];
        } else {
            $depth = 0;
        }

        if( $field["rows"] == 1 ) {
            $padding = str_repeat("&nbsp; &nbsp;", $depth * 2);
        } else {
            $padding = "";
        }
        
        $wrap = new Adverts_Html("div", array(
            "class" => $class,
        ), $padding . $label->render() );
        
        $opts .= $wrap->render();
        
        $i++;
    }
    
    $wrap_classes = array(
        "adverts-form-input-group",
        "adverts-form-input-group-checkbox",
        "adverts-field-rows-" . absint( $field["rows"] )
    );
    
    echo Adverts_Html::build("div", array("class"=> join( " ", $wrap_classes ) ), $opts);
}

/**
 * Form radio input(s) renderer
 * 
 * Prints (to browser) HTML for <input type="radio" /> input
 * 
 * $field params:
 * - name: string
 * - value: mixed (scalar or array)
 * - options: array (for example array(array("value"=>1, "text"=>"title")) )
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function adverts_field_radio( $field ) {
    
    $opts = "";
    $i = 1;
    
    if( !isset( $field["rows"] ) ) {
        $field["rows"] = 1;
    }
    
    if( !isset( $field["value"] ) ) {
        $value = null;
    } else {
        $value = $field["value"];
    }
    
    if(isset($field["options_callback"]) && !empty($field["options_callback"])) {
        $opt = call_user_func( $field["options_callback"], $field );
    } elseif(isset($field["options"])) {
        $opt = $field["options"];
    } else {
        trigger_error("You need to specify options source for field [{$field['name']}].", E_USER_ERROR);
        $opt = array();
    }
    
    foreach($opt as $v) {
        
        $id = $field["name"];
        
        if( isset( $v["id"] ) ) {
            $id = $v["id"];
        } 
        
        $id = apply_filters( "adverts_form_field_option_id", $id.'_'.$i, $v, $field, $i );
        
        
        $checkbox = new Adverts_Html("input", array(
            "type" => "radio",
            "name" => $field["name"],
            "id" => $id,
            "value" => $v["value"],
            "checked" => $v["value"] == $value ? "checked" : null
        ));

        $label = new Adverts_Html("label", array(
            "for" => $id
        ), $checkbox->render() . ' ' . $v["text"]);
        
        $opts .= "<div>".$label->render()."</div>";
        
        $i++;
    }
    
    $wrap_classes = array(
        "adverts-form-input-group",
        "adverts-form-input-group-radio",
        "adverts-field-rows-" . absint( $field["rows"] )
    );
    
    echo Adverts_Html::build("div", array( "class" => join( " ", $wrap_classes ) ), $opts);
}

/**
 * Form special field account input renderer
 * 
 * Prints (to browser) HTML for for dynamic input field, the field contents depends
 * on user state (that is if user is logged in or not).
 * 
 * @param array $field Should be an epty array
 * @since 0.1
 * @return void
 */
function adverts_field_account( $field ) {
    
    $fa = $field;
    
    if(is_user_logged_in() ) {
        
        $text = __('You are posting as <strong>%1$s</strong>. <br/>If you want to use a different account, please <a href="%2$s">logout</a>.', 'wpadverts');
        printf( '<div>'.$text.'</div>', wp_get_current_user()->display_name, wp_logout_url() );
        
    } else {
        
        $text = __('Create an account for me so I can manage all my ads from one place (password will be emailed to you) or <a href="%s">Sign In</a>', 'wpadverts');
        $text = sprintf( $text, wp_login_url( get_permalink() ) );
        
        $fa["options"] = array(
            array(
                "value" => "1", 
                "text" => $text
            )
        );
        
        adverts_field_checkbox($fa);
    }
    
}

/**
 * Form gallery field renderer
 * 
 * Prints (to browser) HTML for for gallery field.
 * 
 * @since 0.1
 * @since 1.3.7 $form param added
 * 
 * @param array         $field      Should be an empty array
 * @param Adverts_Form  $form       Form object
 * @return void
 */
function adverts_field_gallery($field, $form = null ) {
    include_once ADVERTS_PATH . "includes/gallery.php";
    
    wp_enqueue_script( 'adverts-gallery' );
    $save = null;
    
    if( ! is_null( $form ) ) {
        $form_name = $form->get_scheme( "name" );
        $checksum = $form->get_value( "_wpadverts_checksum" );
        $checksum_nonce = $form->get_value( "_wpadverts_checksum_nonce" );
        $post_id = $form->get_value( "_post_id" );
        $post_id_nonce = $form->get_value( "_post_id_nonce" );
        
        if( isset( $field["save"] ) ) {
            $save = $field["save"];
        }
        
    } else {
        include_once ADVERTS_PATH . "includes/class-checksum.php";

        $integrity = new Adverts_Checksum();
        $checksum_args = $integrity->get_integrity_keys( array( "backward-compat" => 1 ) );
        
        $form_name = "advert";
        $checksum = $checksum_args["checksum"];
        $checksum_nonce = $checksum_args["nonce"];
        $post_id = adverts_request( "_post_id", adverts_request( "advert_id" ) );
        $post_id_nonce = wp_create_nonce( sprintf( "wpadverts-publish-%d", $post_id ) );
    }
    
    $post = $post_id > 0 ? get_post( $post_id ) : null;
    
    $conf = array( 
        "button_class" => "adverts-button",
        "input_post_id" => "#_post_id",
        "input_post_id_nonce" => "#_post_id_nonce",
        "_wpadverts_checksum" => $checksum,
        "_wpadverts_checksum_nonce" => $checksum_nonce,
        "_post_id" => $post_id,
        "_post_id_nonce" => $post_id_nonce,
        "form_name" => $form_name,
        "field_name" => $field["name"]
    );
    
    if( $save !== null ) {
        $conf["save"] = $save;
    }
    
    adverts_gallery_content($post, $conf);
}

/**
 * Saves single Adverts_Form value in post meta table.
 * 
 * This function is used on scalar form elements, that is elements that return only
 * one value (<input type="text" />, <textarea />, <input type="radio" />)
 * 
 * @uses delete_post_meta()
 * @uses add_post_meta()
 * 
 * @since 1.0
 * @access public
 * @param int $post_id Advert ID
 * @param string $key Meta name
 * @param string $value Meta value
 * @return void
 */
function adverts_save_single( $post_id, $key, $value ) {
    if( $value == '' ) {
        delete_post_meta( $post_id, $key );
    } else {
        update_post_meta( $post_id, $key, $value );
    }
}

/**
 * Saves files from a temporary directory in a "final" directory.
 * 
 * This function is used to save files added to the file upload / gallery field.
 * 
 * @since 1.5.0
 * @access public
 * @param int $post_id Advert ID
 * @param string $name Field name
 * @param array $field File save options
 * @param string $uniqid Uniqid  
 * @return void
 */
function adverts_save_files( $post_id, $name, $field, $uniqid ) {
    
    include_once ADVERTS_PATH . '/includes/class-upload-helper.php';
    include_once ADVERTS_PATH . "includes/class-checksum.php";

    $checksum = new Adverts_Checksum();
    $args = $checksum->get_args_from_checksum();
    
    $v = new Adverts_Upload_Helper;
    $v->set_field( $field );
    $v->set_form_name( $args["form_name"] );
    $v->set_uniquid( $uniqid );
    $v->set_post_id( $post_id );
    $v->move_files();
}

/**
 * Saves single Adverts_Form value in post meta table.
 * 
 * This function is used on scalar form elements, that is elements that return
 * array of values (<input type="checkbox" />, <select />)
 * 
 * @uses delete_post_meta()
 * @uses add_post_meta()
 * 
 * @since 1.0
 * @access public
 * @param int $post_id Advert ID
 * @param string $key Meta name
 * @param string $value Meta value
 * @return void
 */
function adverts_save_multi( $post_id, $key, $value ) {
    if( !is_array( $value ) ) {
        $value = array( $value );
    }

    $post_meta = get_post_meta( $post_id, $key, false);

    $to_insert = array_diff($value, $post_meta);
    $to_delete = array_diff($post_meta, $value);

    foreach( $to_delete as $meta_value ) {
        delete_post_meta( $post_id, $key, $meta_value );
    }
    foreach( $to_insert as $meta_value ) {
        add_post_meta( $post_id, $key, $meta_value );
    } 
}

/**
 * Binding function for scalar values
 * 
 * This function is used in Adverts_Form class filter and set values
 * for form fields which are using this function for binding.
 * 
 * @see Adverts_Form
 * @see adverts_form_add_field()
 * @see includes/default.php
 * 
 * @since 1.0
 * @access public
 * @param array $field Information about form field
 * @param string $value Value submitted via form
 * @return string Filtered value
 */
function adverts_bind_single($field, $value) {
    
    $filters = Adverts::instance()->get("field_filter", array());

    if( isset( $field["filter"] ) ) {
        foreach( $field["filter"] as $filter ) {
            if( isset( $filters[$filter["name"]] ) ) {
                $f = $filters[$filter["name"]];
                $value = call_user_func_array( $f["callback"], array($value) );
            } // end if;
        } // end foreach;
    } // end if;
    
    return $value;
}

/**
 * Binding function for array values
 * 
 * This function is used in Adverts_Form class filter and set values
 * for form fields which are using this function for binding (by default 
 * <select> and <input type="checkbox" /> are using it).
 * 
 * @see Adverts_Form
 * @see adverts_form_add_field()
 * @see includes/default.php
 * 
 * @since 1.0
 * @access public
 * @param array $field Information about form field
 * @param mixed $value Array or NULL value submitted via form
 * @return mixed
 */
function adverts_bind_multi($field, $value) {

    $filters = Adverts::instance()->get("field_filter", array());
    $key = $field["name"];
    
    if( $value === NULL || empty( $value ) ) {
        $value = array();
    } elseif( ! is_array( $value ) ) {
        $value = array( $value );
    }
    
    $result = array();

    foreach( $value as $v ) {
        $result[] = adverts_bind_single( $field, $v );
    }
    
    if( !isset( $field["max_choices"] ) || $field["max_choices"] == 1) {
        if( isset( $result[0] ) ) {
            return $result[0];
        } else {
            return "";
        }
    } else {
        return $result;
    }
}

/**
 * Display flash messages in wp-admin
 * 
 * This function is being used mainly in Adverts wp-admin template files
 * 
 * @since 0.1
 * @return void
 */
function adverts_admin_flash() {
    $flash = Adverts_Flash::instance();
    ?>

    <?php foreach($flash->get_info() as $info): ?>
    <div class="updated fade">
        <p><?php echo $info; ?></p>
    </div>
    <?php endforeach; ?>

    <?php foreach($flash->get_error() as $error): ?>
    <div class="error">
        <p><?php echo $error; ?></p>
    </div>
    <?php endforeach; ?>

    <?php $flash->dispose() ?>
    <?php $flash->save() ?>
<?php
}

/**
 * Displays JavaScript based redirect code
 * 
 * This function is being used in wp-admin when some content is already displayed
 * in the browser, but Adverts needs to redirect user.
 * 
 * @param string $url
 * @since 0.1
 * @return void 
 */
function adverts_admin_js_redirect( $url ) {
    ?>

    <h3><?php _e("Redirecting", "wpadverts") ?></h3>
    <p><?php printf(__('Your are being redirected to Edit page. <a href="%s">Click here</a> if it is taking to long. ', 'wpadverts'), $url) ?></p>
    
    <script type="text/javascript">
        window.location.href = "<?php echo ($url) ?>"
    </script>

    <?php
}

/**
 * Layout for forms generated by Adverts in wp-admin panel.
 * 
 * @param Adverts_Form $form
 * @param array $options
 * @since 0.1
 * @return void
 */
function adverts_form_layout_config(Adverts_Form $form, $options = array()) {
   
    $a = array();
    
?>

    <?php foreach($form->get_fields( array( "type" => array( "adverts_field_hidden" ) ) ) as $field): ?>
    <?php call_user_func( adverts_field_get_renderer($field), $field, $form ) ?>
    <?php endforeach; ?>
    
    <?php foreach($form->get_fields( $options ) as $field): ?>
        <?php if($field["type"] == "adverts_field_header"): ?>
        <tr valign="top">
            <th colspan="2" style="padding-bottom:0px">
                <?php if(isset($field["title"])): ?>
                <h3 style="border-bottom:1px solid #dfdfdf; line-height:1.4em; font-size:15px"><?php esc_html_e($field["title"]) ?></h3>
                <?php elseif(isset($field["label"])): ?>
                <h3 style="border-bottom:1px solid #dfdfdf; line-height:1.4em; font-size:15px"><?php esc_html_e($field["label"]) ?></h3>
                <?php endif; ?>
            </th>
        </tr>
        <?php else: ?>
        <tr valign="top" class="<?php if(adverts_field_has_errors($field)): ?>adverts-field-error<?php endif; ?>">
            <th scope="row">
                <?php $label_for = isset( $field["attr"]["id"] ) ? $field["attr"]["id"] : $field["name"] ?>
                <label <?php if(!in_array($field['type'], $a)): ?>for="<?php echo esc_attr( $label_for ) ?>"<?php endif; ?>>
                    <?php esc_html_e($field["label"]) ?>
                    <?php if(adverts_field_is_required($field)): ?><span class="adverts-red">&nbsp;*</span><?php endif; ?>
                </label>
            </th>
            <td class="">
                
                <?php
                    switch($field["type"]) {
                        case "adverts_field_text": 
                            $field["class"] = (isset($field["class"]) ? $field["class"] : '') . ' regular-text';
                            break;
                    }
                ?>
                
                <?php call_user_func( adverts_field_get_renderer($field), $field, $form ) ?>

                <?php if(isset($field['hint']) && !empty($field['hint'])): ?>
                <br/><span class="description"><?php echo $field['hint'] ?></span>
                <?php endif; ?>

                <?php if(adverts_field_has_errors($field)): ?>
                <ul class="updated adverts-error-list">
                    <?php foreach($field["error"] as $k => $v): ?>
                    <li><?php esc_html_e($v) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>

<?php


}

/**
 * Retrieve dropdown data for advert_category list.
 *
 * @uses Adverts_Walker_CategoryDropdown to create HTML dropdown content.
 * @since 1.0
 * @see Walker_CategoryDropdown::walk() for parameters and return description.
 */
function adverts_walk_category_dropdown_tree() {
    $args = func_get_args();
    // the user's options are the third parameter
    if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') ) {
        include_once ADVERTS_PATH . '/includes/class-walker-category-options.php';
        $walker = new Adverts_Walker_Category_Dropdown;
    } else {
        $walker = $args[2]['walker'];
    }

    return call_user_func_array(array( &$walker, 'walk' ), $args );
}

/**
 * Returns options for category field
 * 
 * This function is being used when generating category field in the (for example 
 * "post ad" form).
 * 
 * @uses adverts_walk_category_dropdown_tree()
 * @since 0.1
 * @return array
 */
function adverts_taxonomies( $taxonomy = 'advert_category' ) {
    
    if( is_array( $taxonomy ) ) {
        // DUMB backward compatibility for forms
        $taxonomy = 'advert_category';
    }
    
    $args = array(
        'taxonomy'     => $taxonomy,
        'hierarchical' => true,
        'orderby'       => 'name',
        'order'         => 'ASC',
        'hide_empty'   => false,
        'depth'         => 0,
        'selected' => 0,
        'show_count' => 0,
        
    );

    include_once ADVERTS_PATH . '/includes/class-walker-category-options.php';
    
    $walker = new Adverts_Walker_Category_Options;
    $params = array(
        get_terms( $taxonomy, $args ),
        0,
        $args
    );
    
    return call_user_func_array(array( &$walker, 'walk' ), $params );
}

/**
 * Returns current user IP address
 * 
 * Based on Easy Digital Downloads get ip function.
 * 
 * @since 1.0
 * @return string
 */
function adverts_get_ip() {
    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return apply_filters( 'adverts_get_ip', $ip );
}

/**
 * Returns currency data
 * 
 * It can return either all currencies (if $currency = null), all information
 * about one currenct (if $get = null).
 * 
 * @param mixed $currency Either NULL or string
 * @param string $get Either 'code', 'sign', 'label' or NULL
 * @return array
 */
function adverts_currency_list( $currency = null, $get = null ) {
    
    $list = apply_filters("adverts_currency_list", array(
        array("code"=>"USD", "sign"=>"$", "label"=>__("US Dollars", "wpadverts")),
        array("code"=>"EUR", "sign"=>"€", "label"=>__("Euros", "wpadverts")),
        array("code"=>"GBP", "sign"=>"£", "label"=>__("Pounds Sterling", "wpadverts")),
        array("code"=>"AUD", "sign"=>"$", "label"=>__("Australian Dollars", "wpadverts")),
        array("code"=>"BRL", "sign"=>"R$", "label"=>__("Brazilian Real", "wpadverts")),
        array("code"=>"CAD", "sign"=>"$", "label"=>__("Canadian Dollars", "wpadverts")),
        array("code"=>"CZK", "sign"=>"Kč", "label"=>__("Czech Koruna", "wpadverts")),
        array("code"=>"DKK", "sign"=>"", "label"=>__("Danish Krone", "wpadverts")),
        array("code"=>"HKD", "sign"=>"$", "label"=>__("Hong Kong Dollar", "wpadverts")),
        array("code"=>"HUF", "sign"=>"Ft", "label"=>__("Hungarian Forint", "wpadverts")),
        array("code"=>"ILS", "sign"=>"₪", "label"=>__("Israeli Shekel", "wpadverts")),
        array("code"=>"INR", "sign"=>"₹", "label"=>__("Indian Rupees", "wpadverts")),
        array("code"=>"JPY", "sign"=>"¥", "label"=>__("Japanese Yen", "wpadverts")),
        array("code"=>"MYR", "sign"=>"", "label"=>__("Malaysian Ringgits", "wpadverts")),
        array("code"=>"MXN", "sign"=>"$", "label"=>__("Mexican Peso", "wpadverts")),
        array("code"=>"NGN", "sign"=>"₦", "label"=>__("Nigerian Naira", "wpadverts")),
        array("code"=>"NZD", "sign"=>"$", "label"=>__("New Zealand Dollar", "wpadverts")),
        array("code"=>"NOK", "sign"=>"kr", "label"=>__("Norwegian Krone", "wpadverts")),
        array("code"=>"PHP", "sign"=>"₱", "label"=>__("Philippine Pesos", "wpadverts")),
        array("code"=>"PLN", "sign"=>"zł", "label"=>__("Polish Zloty", "wpadverts")),
        array("code"=>"SGD", "sign"=>"$", "label"=>__("Singapore Dollar", "wpadverts")),
        array("code"=>"SEK", "sign"=>"kr", "label"=>__("Swedish Krona", "wpadverts")),
        array("code"=>"CHF", "sign"=>"", "label"=>__("Swiss Franc", "wpadverts")),
        array("code"=>"TWD", "sign"=>"", "label"=>__("Taiwan New Dollars", "wpadverts")),
        array("code"=>"THB", "sign"=>"฿", "label"=>__("Thai Baht", "wpadverts")),
        array("code"=>"TRY", "sign"=>"", "label"=>__("Turkish Lira", "wpadverts")),
        array("code"=>"RIAL", "sign"=>"", "label"=>__("Iranian Rial", "wpadverts")),
        array("code"=>"RUB", "sign"=>"", "label"=>__("Russian Rubles", "wpadverts")),
        array("code"=>"ZAR", "sign"=>"R", "label"=>__("South African Rand", "wpadverts")),
        array("code"=>"LKR", "sign"=>"Rs. ", "label"=>__("Sri Lankan Rupees", "wpadverts")),
    ));
    
    if( $currency == null ) {
        return $list;
    }
    
    $currency_data = null;
    
    foreach($list as $curr) {
        if($curr["code"] == $currency) {
            $currency_data = $curr;
            break;
        }
    }
    
    if( $currency_data === null ) {
        trigger_error("Currency [$currency] does not exist.");
        return null;
    }
    
    if($get && isset($currency_data[$get])) {
        return $currency_data[$get];
    } else {
        return $currency_data;
    }
} 

/**
 * Returns path to the provided $term
 * 
 * The path consists of parent/child term text names only.
 * 
 * @param stdClass $term WP Term object
 * @param string $taxonomy Taxonomy name
 * @since 1.0.5
 * @return array Term path
 */
function advert_term_path( $term, $taxonomy = null ) {
    $cpath = array();

    if( $taxonomy === null ) {
        $taxonomy = $term->taxonomy;
    }
    
    do {
        $cpath[$term->term_id] = $term->name;
        $term = get_term( $term->parent, $taxonomy );
    } while( !$term instanceof WP_Error );
    
    return array_reverse( $cpath, true );
}

/**
 * Returns path to the provided $term
 * 
 * The path consists of parent/child term text names only.
 * @uses advert_term_path
 * 
 * @param stdClass $term WP Term object
 * @since 0.2
 * @updated 1.0.5
 * @return array Term path
 */
function advert_category_path( $term ) {
    return advert_term_path( $term, 'advert_category' );
}

/**
 * Returns number of items in this category and all sub categories.
 * 
 * @since 1.1.6     adverts_category_post_count filter
 * @since 0.3
 * 
 * @param stdClass $term Term object
 * @return int Number of posts in this cantegory and sub-categories
 */
function adverts_category_post_count( $term ) {
    $cat = $term;
    $count = (int) $cat->count;
    $taxonomy = 'advert_category';
    $args = array(
      'child_of' => $term->term_id,
    );
    $tax_terms = get_terms($taxonomy,$args);
    foreach ($tax_terms as $tax_term) {
        $count +=$tax_term->count;
    }
    
    return apply_filters( "adverts_category_post_count", $count, $term );
}

/**
 * Fixes random changing font size.
 * 
 * This is a fix for a problem described here https://www.wp-code.com/wordpress-snippets/how-to-stop-chrome-using-a-large-font-size-after-refreshing/
 * by default it is applied to Twentytwelve theme only but you can apply it to your theme 
 * if you need to by adding following code add_action('wp_head', 'adverts_css_rem_fix');
 * 
 * @since 0.2
 * @return void
 */
function adverts_css_rem_fix() {
    echo '<style type="text/css">'.PHP_EOL;
    echo 'body { font-size: 1em !important }'.PHP_EOL;
    echo '</style>'.PHP_EOL;
}

/**
 * Disables Adverts archive page.
 * 
 * We do not want to disaply adverts archive page because it is not possible
 * to control displayed conent there, instead we redirect users to default ads list page
 * 
 * @access public
 * @since 1.0
 * @return void
 */
function adverts_disable_default_archive() {
    if( is_post_type_archive( "advert" ) && ! is_feed() ) {
        wp_redirect( get_permalink( adverts_config( "ads_list_id" ) ) );
        exit;
    }
}

/**
 * Checks if plugin is uploaded to wp-content/plugins directory.
 * 
 * This functions checks if plugin is uploaded to plugins directory, note that
 * as a $basename you need to pass plugin-dir/plugin-file-name.php
 * 
 * @access public
 * @since 1.0
 * @param string $basename Plugin basename
 * @return boolean
 * 
 */
function adverts_plugin_uploaded( $basename ) {
    $is_uploaded = is_file( dirname( ADVERTS_PATH ) . "/" . ltrim( $basename, "/") );
    return apply_filters( "adverts_plugin_uploaded", $is_uploaded, $basename );
}

/**
 * Creates a user based on data in Ad
 * 
 * This functions is used to automatically create user, if when posting an Ad
 * (using [adverts_add] shortcode) user selected that he wants to have an account created.
 * 
 * @uses adverts_create_user_from_post_id filter to register user
 * @see shortcode_adverts_add()
 * 
 * @access public
 * @since 1.0
 * @param int $ID Ad Post ID
 * @param boolean $update_post True if you want created user to be assigned to post with $ID.
 * @return int Created user ID
 */
function adverts_create_user_from_post_id( $ID, $update_post = false ) {
    
    $email_address = get_post_meta( $ID, "adverts_email", true );
    $full_name = get_post_meta( $ID, "adverts_person", true );
    $user_id = null;
    
    if( null == username_exists( $email_address ) ) {

        $user_id = apply_filters( "adverts_create_user_from_post_id", null, $ID );
        
        if($update_post && is_int( $user_id ) ) {
            wp_update_post( array( 
                "ID" => $ID,
                "post_author" => $user_id
            ) );
        }
        
        if( $user_id ) {
            do_action( "wpadverts_user_saved", $user_id, $ID );
        }

    } // end if
    
    return $user_id;
}

/**
 * Registers user using WordPress registration
 * 
 * User data for registration (email, name, etc.) is derived from posted Advert.
 * The user is registered using wp_create_user() function
 * 
 * @see wp_create_user()
 * 
 * @access protected
 * @since 1.0
 * @param int $user_id      Integer if user was already registered or NULL
 * @param int $post_id      ID of a post from which user will be created
 * @return int              Newly created user ID
 */
function _adverts_create_user_from_post_id( $user_id, $post_id ) {
    
    if( $user_id !== null ) {
        // some other filter already registered user, skip registration then
        return $user_id;
    }
    
    $email_address = get_post_meta( $post_id, "adverts_email", true );
    $full_name = get_post_meta( $post_id, "adverts_person", true );
    // Generate the password and create the user
    $password = wp_generate_password( 12, false );
    $user_id = wp_create_user( $email_address, $password, $email_address );
    
    // Set the nickname
    wp_update_user(
        array(
            'ID'          =>    $user_id,
            'nickname'    =>    $full_name,
            'display_name'=>    $full_name
        )
    );
    
    // Set the role
    $user = new WP_User( $user_id );
    $user->set_role( 'subscriber' );

    // Email the user
    do_action( "adverts_new_user_notification", $user_id, null, "both", $password );
    
    return $user_id;
}

/**
 * Appends classes to single advert on ads list.
 * 
 * This function is used in wpadverts/templates/list-item.php file.
 * 
 * @param string $classes List of CSS classes
 * @param integer $post_id WP_Post ID
 * @return string Updated list of CSS classes
 */
function adverts_css_classes( $classes, $post_id ) {

    $post = get_post( $post_id );    
    $classes = trim($classes) . " " . "advert-id-" . $post_id;
    
    return apply_filters( "adverts_css_classes", $classes, $post_id );
}

/**
 * Sorts array of images, if a custom order has been recorded for this post (i.e. a
 * user has 'drag and dropped' images into a custom order previously).
 *
 * Checks wp_postmeta for the customised order of images, which is stored as a JSON
 * string under the meta_key '_adverts_attachments_order'.
 *
 * If there is no custom order recorded against this post, then this function just
 * returns the unsorted array of $images, which will remain in the order given by
 * WP's get_children().
 *
 * @see functions.php/adverts_single_rslides() & gallery.php/adverts_gallery_content()
 *
 * @since 1.1.0
 *
 * @param images[] $images  Unsorted array of images (technically an associative array of posts)
 * @param integer $post_id  WP_Post ID
 * @return images[]         Sorted array of images (or default order if none defined)
 */
function adverts_sort_images($images, $post_id, $field_name = null) {
    $meta_key = '_adverts_attachments_order';
    
    if( $field_name !== null && $field_name != "gallery" ) {
        $meta_key .= "__" . $field_name;
    }
    
    $images_order = json_decode(get_post_meta($post_id, $meta_key, true));

    if ( !is_null($images_order) ) {
        include_once ADVERTS_PATH . 'includes/class-sort-images.php';
        uksort( $images, array( new Adverts_Sort_Images( $images_order ), "sort" ) );
    }

    return $images;
}

/**
 * Returns post mime type
 * 
 * Possible return values are
 * - image: jpg, gif or png that is an image which can be displayed in browser
 * - video: webm, mp4 or ogv that is any video which can be played in the browser
 * - other: any other mime type
 * 
 * @since 1.2.0
 * @param WP_Post $attach   Post for which we want to find mime
 * @return string           Mime type (one of video, image, other)
 */
function adverts_get_attachment_mime( $attach ) {

    $known = array(
        "video" => array( "video/webm", "video/mp4", "video/ogv" ),
        "image" => array( "image/jpeg", "image/jpe", "image/jpg", "image/gif", "image/png", "image/webp" )
    );
    
    foreach( $known as $key => $mimes ) {
        if( in_array( $attach->post_mime_type, $mimes ) ) {
            return $key;
        }
    }
    
    return "other";
}

/**
 * Returns a Font-Awesome icon for WP_Post::post_mime_type
 * 
 * If the mime type is not in a list of known mime types a default icon is returned
 * 
 * @since 1.2.0
 * @param WP_Post   $attach     Attachment for which icon should be returned
 * @return string               Font-Awesome icon code
 */
function adverts_get_attachment_icon( $attach ) {
    
    $mime = adverts_get_attachment_mime( $attach );
    
    if( $mime === "video" ) {
        return "adverts-icon-file-video";
    } else if( $mime === "image" ) {
        return "adverts-icon-file-image";
    }
    
    $known = array(
        "adverts-icon-file-pdf" => array( "application/x-pdf", "application/pdf" ),
        "adverts-icon-file-archive" => array( "application/zip", "application/octet-stream" )
    );
    
    foreach( $known as $icon => $mimes ) {
        if( in_array( $attach->post_mime_type, $mimes ) ) {
            return $icon;
        }
    }

    return "adverts-icon-doc-text";
}

/**
 * Returns an image with selected size
 * 
 * The size can be an array then the function will iterate over the array and
 * will return an URL to the first existing image
 * 
 * Allowed sizes are: full, adverts_gallery, adverts_upload_thumbnail
 * 
 * @since   1.2.0
 * @param   WP_Post|int     $post_id    Advert object or Advert ID
 * @param   array           $sizes      List of sizes to check
 * @return  array                       Keys [width, height, is_intermidiate, orient]
 */
function adverts_get_post_img( $post_id, $sizes ) {
    if( $post_id instanceof WP_Post ) {
        $post_id = $post_id->ID;
    }
    
    $upload = adverts_upload_item_data( $post_id );
    
    foreach( $sizes as $size ) {
        if( isset( $upload["sizes"][$size] ) ) {
            $img = $upload["sizes"][$size];
            
            if($img["width"] == $img["height"]) {
                $img["orient"] = "square";
            } else if($img["width"] > $img["height"] ) {
                $img["orient"] = "landscape";
            } else {
                $img["orient"] = "portrait";
            }
            
            return $img;
        }
    }
    
    return null;
}

/**
 * Return an URL to image with selected size
 * 
 * The size can be an array then the function will iterate over the array and
 * will return an URL to the first existing image
 * 
 * Allowed sizes are: full, adverts_gallery, adverts_upload_thumbnail
 * 
 * @since   1.2.0
 * @param   WP_Post|int     $post_id    Advert object or Advert ID
 * @param   array           $sizes      List of sizes to check
 * @return  string                      URL to the image
 */
function adverts_get_post_img_url( $post_id, $sizes ) {
    $img = adverts_get_post_img( $post_id, $sizes );
    
    if($img === null) {
        return null;
    } else {
        return $img["url"];
    }
}

/**
 * Renders slider on Ad details page.
 * 
 * This function is called by adverts_tpl_single_top action in
 * wpadverts/templates/single.php
 * 
 * @see adverts_tpl_single_top action
 * 
 * @since 1.0.7
 * @param int $post_id Post ID
 * @return void
 */
function adverts_single_rslides( $post_id ) {

    include_once ADVERTS_PATH . "/includes/class-gallery-helper.php";
    
    $gallery_helper = new Adverts_Gallery_Helper( $post_id );
    $gallery_helper->render_gallery();
}

/**
 * Renders contact box on Ad details page
 * 
 * This function is called by adverts_tpl_single_bottom action in
 * wpadverts/templates/single.php
 * 
 * @see adverts_tpl_single_bottom action
 * 
 * @since 1.0.7
 * @access public
 * @param int $post_id Post ID
 * @return void
 */
function adverts_single_contact_information( $post_id ) {
    ?>
    <div class="adverts-single-actions">
        <a href="#" class="adverts-button adverts-show-contact" data-id="<?php echo $post_id ?>">
            <?php esc_html_e("Show Contact Information", "wpadverts") ?>
            <span class="adverts-icon-down-open"></span>
        </a>
        <span class="adverts-loader adverts-icon-spinner animate-spin"></span>
    </div>
    <?php

    add_action( "adverts_tpl_single_bottom", "adverts_single_contact_information_box", 2000 );
}

/**
 * Renders contact box
 * 
 * This function is called by adverts_tpl_single_bottom filter registered in
 * adverts_single_contact_information function 
 * 
 * @see adverts_single_contact_information() function
 * @see adverts_tpl_single_bottom filter
 * 
 * @since   1.3.2
 * @param   int   $post_id
 * @return  void
 */
function adverts_single_contact_information_box( $post_id ) {

    ?>
    <div class="adverts-contact-box adverts-contact-box-toggle">
        <p class="adverts-contact-method">
            <span class="adverts-icon-phone adverts-contact-icon" title="<?php _e("Phone", "wpadverts") ?>"></span>
            <span class="adverts-contact-phone"></span>
        </p>

        <p class="adverts-contact-method">
           <span class="adverts-icon-mail-alt adverts-contact-icon" title="<?php _e("Email", "wpadverts") ?>"></span>
           <span class="adverts-contact-email"></span>
        </p>
    </div>
    <?php
}

/**
 * Adds 'reveal_hidden' input to search form.
 * 
 * This function add <input type="hidden" name="reveal_hidden" value="1" /> 
 * to search form if $_GET["reveal_hidden"] == 1. 
 * 
 * Function is being called by [adverts_list] shortcode.
 * 
 * @since 1.0.7
 * @access public
 * @param array $form Form scheme
 * @return array Updated form scheme
 */
function adverts_form_search_reveal_hidden( $form ) {
    if( $form['name'] != 'search' ) {
        return $form;
    }
    
    $form["field"][] = array(
        "name" => "reveal_hidden",
        "type" => "adverts_field_hidden",
        "order" => 20,
        "value" => 1
    );
    
    return $form;
}

/**
 * Adds 'display' input to search form.
 * 
 * This function add <input type="hidden" name="display" value="1" /> 
 * to search form if $_GET["display"] == 1. 
 * 
 * Function is being called by [adverts_list] shortcode.
 * 
 * @since 1.0.8
 * @access public
 * @param array $form Form scheme
 * @return array Updated form scheme
 */
function adverts_form_search_display_hidden( $form ) {
    if( $form['name'] != 'search' ) {
        return $form;
    }
    
    $form["field"][] = array(
        "name" => "display",
        "type" => "adverts_field_hidden",
        "order" => 20,
        "value" => adverts_request( "display" )
    );
    
    return $form;
}

/**
 * Remove "account" field from Advers Add form
 * 
 * This function is being called in [adverts_manage] shortcode, to hide the
 * account field when editing an Ad.
 * 
 * @since 1.0
 * @access public
 * @param array $form   Form Scheme
 * @return array        Modified form scheme
 */
function adverts_remove_account_field( $form ) {
    if( $form['name'] != "advert" ) {
        return $form;
    }

    foreach( $form["field"] as $key => $field ) {
        if( $field["name"] == "_adverts_account" ) {
            unset( $form["field"][$key] );
        }
    }
    
    return $form;
}

/**
 * Replaces 'advert' class with 'classified' class.
 * 
 * This function prevents hiding Ad detail pages by AdBlock or other ad blocking
 * browser plugins
 * 
 * This function is applied using 'post_class' filter.
 * 
 * @since 1.0.10
 * @param array $classes List of classes
 * @return array
 */
function adverts_post_class( $classes ) {
    foreach( $classes as $key => $cl ) {
        if( $cl == "advert" ) {
            $classes[$key] = "classified";
            break;
        }
    }
    return $classes;
}

/**
 * Delete Permanently or Tash Post
 * 
 * This function allows to determine if posts should be Trashed or Deleted when
 * deleting an Advert in the frontend.
 * 
 * The $skip_trash variable can be overwritten using adverts_skip_trash filter.
 * 
 * @uses adverts_skip_trash filter
 * 
 * @access public
 * @since 1.0.11
 * @param int $post_id          ID of a post to delete
 * @param boolean $skip_trash   True if items should be deleted permanently, false if item should go to trash
 * @return mixed                Information about deleted (or not) object.
 */
function adverts_delete_post( $post_id, $skip_trash = true ) {
    
    $skip_trash = apply_filters( "adverts_skip_trash", $skip_trash, $post_id );
    
    if( $skip_trash ) {
        $result = wp_delete_post( $post_id );
    } else {
        $result = wp_trash_post( $post_id );
    }
    
    return $result;
}

/**
 * Adds number of pending Ads next to Classifieds link
 * 
 * This function is executed using add_menu_classes filter, it adds amount
 * of Ads with "pending" status next to the Classifieds menu in wp-admin
 * left sidebar.
 * 
 * @see add_menu_classes
 * 
 * @since 1.1.5
 * 
 * @param array $menu   wp-admin left menu
 * @return array        Updated menu items
 */
function adverts_add_menu_classes( $menu ) {

    $adverts = wp_count_posts( 'advert' );

    if(!is_object($adverts) || !isset($adverts->pending)) {
        return $menu;
    }
    
    $pending = $adverts->pending;
    
    if($pending < 1) {
        return $menu;
    }
    
    $wrap = " <span class=\"awaiting-mod count-$pending\" title=\"%s\"><span class=\"pending-count\">$pending</span></span>";
    
    foreach(array_keys($menu) as $key) {
        if(isset($menu[$key][5]) && $menu[$key][5] == "menu-posts-advert") {
            $menu[$key][0] .= sprintf( $wrap, __( "Pending Ads", "wpadverts" ) );
            break;
        }
    }
    
    
    return $menu;
}

/**
 * Checks if current user can edit images in gallery
 * 
 * Returns true if current user has the capability set in wp-admin / Classifieds 
 * / Options / Core / Gallery panel.
 * 
 * @since 1.2
 * @return boolean
 */
function adverts_user_can_edit_image( ) {
    $cap = adverts_config( "gallery.image_edit_cap" );
    
    if( empty( $cap ) ) {
        return true;
    }
    
    return current_user_can( $cap );
}

/**
 * Handle 200 status for expired Ads.
 * 
 * This function disables all contact options on expired Ads details pages
 * and at the top of the Ad (above gallery) shows an error message.
 * 
 * The function is executed by "wp" action and is registered in adverts_init_frontend().
 * 
 * @see wp filter
 * @see adverts_init_frontend()
 * 
 * @since 1.2.3
 * @return void
 */
function adverts_handle_expired_ads() {
    if( is_singular( 'advert' ) && get_post( get_the_ID() )->post_status == "expired" ) {
        remove_action( 'adverts_tpl_single_bottom', 'adverts_single_contact_information' );
        remove_action( 'adverts_tpl_single_bottom', 'adext_contact_form' );
        remove_action( 'adverts_tpl_single_bottom', 'adext_bp_send_private_message_button', 50 );

        add_action( 'adverts_tpl_single_top', "adverts_handle_expired_ads_notification", 1 );
    }
}

/**
 * Displays Ad expiration notification.
 * 
 * This function is registered in adverts_tpl_single_top action.
 * 
 * @see adverts_handle_expired_ads()
 * @see adverts_tpl_single_top filter
 * 
 * @param   int   $post_id  Post ID
 * @return  void
 */
function adverts_handle_expired_ads_notification( $post_id ) {
    if( get_post( $post_id )->post_status == "expired" ) {
        
        if( adverts_config( 'expired_ad_status' ) !== "200" && current_user_can( adverts_config( 'expired_ad_public_cap' ) ) ) {
            $icon = "adverts-icon-eye-off";
            $m = __( "<strong>Visible To Administrators Only</strong><br/>This Ad expired, but as a user with <em>%s</em> capability you can see this page.", "wpadverts" );
            $message = sprintf( $m, adverts_config( 'expired_ad_public_cap' ) );
            
        } else {
            $icon = "adverts-icon-block";
            $m = __( "<strong>This Ad expired and is no longer available.</strong><br/>See our other active <a href=\"%s\">classified ads</a>.", "wpadverts" );
            $message = sprintf( $m, get_permalink( adverts_config( 'ads_list_id' ) ) );
            
        }
        
        $flash = array( "error" => array(), "info" => array(), "warn" => array() );
        $flash["error"][] = array(
            "message" => $message,
            "icon" => $icon
        );
        adverts_flash( $flash );
    } 
}

/**
 * Handle 301 status for expired Ads.
 * 
 * This function redirects user (with a 301 redirect) to some other page 
 * if the Ad is expired.
 * 
 * The function is executed by "template_redirect" action and is registered in adverts_init_frontend().
 * 
 * @see template_redirect action
 * @see adverts_init_frontend()
 * 
 * @since 1.2.3
 * @return void
 */
function adverts_template_redirect_expired() {
    if( is_singular( 'advert' ) && get_post( get_the_ID() )->post_status == "expired" ) {
        $redirect_url = adverts_config( 'expired_ad_redirect_url' );
        
        if( empty( $redirect_url ) ) {
            $redirect_url = get_site_url();
        }
        
        $redirect_url = apply_filters( "adverts_redirect_expired_url", $redirect_url );
        $status = apply_filters( "adverts_redirect_expired_status", 301 );
        
        wp_redirect( $redirect_url, $status );
        exit;
    }
}

/**
 * Returns URL to AJAX request page
 * 
 * @since 1.2.6
 * @return string
 */
function adverts_ajax_url() {
    return apply_filters( "adverts_ajax_url", admin_url( "admin-ajax.php") );
}

function adverts_empty_price( $post_id ) {
    return apply_filters( "adverts_empty_price", adverts_config( 'empty_price' ), $post_id );
}

/**
 * Genenrate Advert frontend hash
 * 
 * The unique hash can be used to complete payment or allow annonymous Ad edition.
 * 
 * @since   1.3.0
 * @param int       $post_id    Post ID
 * @param WP_Post   $post       Post obect
 * @param boolean   $update     Whether this is an existing post being updated or not
 * @return  string              Advert frontend hash
 */
function adverts_create_hash( $post_id, $post, $update ) {
    
    $ehash = get_post_meta( $post_id, "_adverts_frontend_hash", true );

    if( empty( $ehash ) ) {
        $ehash = sprintf( "%s-%s", md5( uniqid() ), str_pad( $post_id, 6, "0", STR_PAD_LEFT ) );
        $ehash = apply_filters( "adverts_frontend_hash", $ehash, $post_id, $post, $update );
        update_post_meta( $post_id, "_adverts_frontend_hash", $ehash );
        
        $ecan = apply_filters( "adverts_frontend_hash_enabled", 1, $post_id, $post_id, $post, $update );
        update_post_meta( $post_id, "_adverts_frontend_hash_enabled", $ecan );
    }
    
    return $ehash;
}

/**
 * Returns post_id based on _adverts_frontend_hash meta.
 * 
 * @since   1.3.0
 * @global  wpdb    $wpdb   wpdb object
 * @param   string  $hash   Frontend hash
 * @return  int             Post ID
 */
function adverts_get_post_id_from_hash( $hash ) {
    global $wpdb;

    $sql = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value = %s AND meta_key = '_adverts_frontend_hash' LIMIT 1";
    $post_id = $wpdb->get_var( $wpdb->prepare( $sql, $hash) );

    return absint( $post_id );
}

/**
 * Returns upload nonce action based on attachment ID
 * 
 * The function generates a nonce action in format "wpadverts-upload-{post_type}-{form_name}-{field_name}"
 * 
 * @since   1.4.0                            $attach_id      Attachment ID
 * @return  Adverts_Shortcode_Adverts_Add                    Upload nonce action
 */
function _adverts_ajax_verify_checksum( ) {
    
    include_once ADVERTS_PATH . "includes/class-checksum.php";

    $checksum = new Adverts_Checksum();

    $args = $checksum->get_args_from_checksum();
    
    if( ! is_array( $args ) ) {
        
        if( $args == -1 ) {
            $error = __( "Could not verify the request checksum. Please refresh the page and try again.", "wpadverts" );
        } else {
            $error = __( "Checksum does not exist. Please refresh the page and try again.", "wpadverts" );
        }
        
        echo json_encode( array( 
            "result" => 0, 
            "error" => $error
        ) );
        exit;
    }
    
    return $args;
}

/**
 * Checks if the post_id param is required
 * 
 * The post_id is required if $args is an array and the key "requires-post-id"
 * or "is-wp-admin" is set.
 * 
 * This function usually uses as $args the value returned by _adverts_ajax_verify_checksum() 
 * 
 * @see     _adverts_ajax_verify_checksum() 
 * 
 * @param   array     $args
 * @return  boolean             True if _post_id is required
 */
function _adverts_ajax_requires_post_id( $args ) {
    
    if( isset( $args["requires-post-id"] ) && $args["requires-post-id"] == "1" ) {
        return true;
    }
    
    if( isset( $args["is-wp-admin"] ) && $args["is-wp-admin"] == "1" ) {
        return true;
    }
    
    return false;
}

/**
 * Verifies post ID
 * 
 * This function is usually used by AJAX to verify post ID using _post_id_nonce
 * 
 * @since   1.4.0
 * @param   boolean     $is_required
 * @return  int                         Post ID
 */
function _adverts_ajax_verify_post_id( $is_required = false ) {
    
    $post_id = adverts_request( "_post_id" );
    $post_id_nonce = adverts_request( "_post_id_nonce" );

    if( $post_id > 0 && ! wp_verify_nonce( $post_id_nonce, "wpadverts-publish-" . $post_id ) ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => sprintf( __( "It seems you are not the post %d author. Please refresh the page and try again.", "wpadverts" ), $post_id ) 
        ) );
        exit;
    }
    
    if( $is_required && $post_id < 1 ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "Post ID not provided.", "wpadverts" )
        ) );
        exit;
    }
    
    return $post_id;
}

/**
 * Checks if the current user is owner of the $advert_id
 * 
 * The function can additionally return true if the current user is an
 * editor or administrator (that is a user with "edit_pages" capability).
 * 
 * The required capability can be customized using adverts_ajax_ownership_cap filter.
 * 
 * @since 1.3.2
 * @param int       $advert_id      ID of an Advert
 * @param boolean   $return         True to return result as object;
 * @return void
 */
function _adverts_ajax_check_post_ownership( $advert_id, $return = false ) {
    
    $post = get_post( $advert_id );
    $result = null;
    
    // check if post exists
    if( ! is_object( $post ) ) {
        $result = array( 
            "result" => -1, 
            "error" => __( "This post does not exist.", "wpadverts" ) 
        );
        
        if( $return !== false ) {
            echo json_encode( $result );
            exit;
        } else {
            return $result;
        }
    }

    $required_cap = apply_filters( "adverts_ajax_ownership_cap", "edit_pages" );
    $has_required_cap = current_user_can( $required_cap );
    
    if( get_current_user_id() === 0 ) {
        if( absint( $post->post_author ) !== 0 || $post->post_status !== adverts_tmp_post_status() ) {
            // annonymous user
            $result = array( 
                "result" => -4, 
                "error" => __( "This post cannot be edited by annonymous users.", "wpadverts" ) 
            );
        }
    } else {
        if( $post->post_author != get_current_user_id() && ! $has_required_cap ) {
            // currently logged in user is not a post author
            $result = array( 
                "result" => -2, 
                "error" => __( "This post does not belong to you.", "wpadverts" ) 
            );
        }
    }

    if( $result !== null ) {
        if( $return !== false ) {
            echo json_encode( $result );
            exit;
        } else {
            return $result;
        }
    }
    
    // check if post is an advert
    if( $post->post_type != 'advert') {
        $result = array( 
            "result" => -3, 
            "error" => __( "This post is not an Advert.", "wpadverts" ) 
        );
    } 

    if( $return !== false ) {
        echo json_encode( $result );
        exit;
    } else {
        return $result;
    }
}

/**
 * Hides the Advert attachments in Media Library
 * 
 * This function is executed by ajax_query_attachments_args action registered
 * in adverts_init() function.
 * 
 * It modifies the Media Library query so the attachments added to WPAdverts forms
 * are not visible in Media Library.
 * 
 * @see     ajax_query_attachments_args action
 * @see     adverts_init() function
 * 
 * @since   1.3.6
 * @param   array $args   WP_Query arguments
 * @return  array
 */
function adverts_query_attachments_args( $args ) {
    
    if( ! isset( $args["meta_query"] ) || ! is_array( $args["meta_query"] ) ) {
        $args["meta_query"] = array();
    }

    $args["meta_query"][] = array(
        "key" => "wpadverts_form",
        "compare" => "NOT EXISTS"
    );

    return $args;
}

function adverts_skip_preview( $tpl ) {
    
    if( adverts_request( "_adverts_action" ) != "save-ff" ) {
        return $tpl;
    }
    
    include_once ADVERTS_PATH . "includes/class-shortcode-adverts-add.php";
    
    $shortcode = new Adverts_Shortcode_Adverts_Add();
    $shortcode->load_args_from_checksum();
    $shortcode->init();
    if( $shortcode->action_preview() === true ) {
        wp_redirect( add_query_arg( array(
            "_adverts_action" => "save",
            "_post_id" => $shortcode->get_post_id(),
            "_post_id_nonce" => $shortcode->get_post_id_nonce()
        ) ) );
        exit;
    }
    
    return $tpl;
}

function adverts_form_load_checksum_fields( $form ) {
    
    if( $form["name"] == "advert" ) {
        $checksum_fields = array( "_wpadverts_checksum", "_wpadverts_checksum_nonce", "_post_id_nonce" );
    } else if( $form["name"] == "contact" ) {
        $checksum_fields = array( "_wpadverts_checksum", "_wpadverts_checksum_nonce" );
    } else {
        return $form;
    }
    
    foreach( $checksum_fields as $field_name ) {
        $form["field"][] = array(
            "name" => $field_name,
            "type" => "adverts_field_hidden",
            "order" => 0,
            "label" => ""
        );
    }
    
    return $form;
}

/**
 * Sets featured image for $post_id
 * 
 * The function will select the first image on the list as featured 
 * unless some featured image is already selected.
 * 
 * @since   1.4.2
 * @param   int     $post_id    ID of a post for which we wish to force featured image
 * @return  int                 1 if success less or equal to 0 on failure
 */
function adverts_force_featured_image( $post_id ) {
    if( $post_id < 1 ) {
        // No images uploaded
        return -1;
    } else if( $post_id > 0 && get_post_thumbnail_id( $post_id ) ) {
        // Has main image selected
        return -2;
    } 
    
    $keys = get_post_meta( $post_id, '_adverts_attachments_order', true );
    
    if( $keys ) {
        $keys = json_decode( $keys );
    }
    
    if( is_array( $keys ) && isset( $keys[0] ) ) {
        $forced_thumbnail_id = apply_filters( "adverts_force_featured_image", $keys[0], $post_id );
        update_post_meta( $post_id, '_thumbnail_id', $forced_thumbnail_id );
        return 1;
    }
    
    $children = get_children( array( 
        'post_parent' => $post_id,
        'meta_query' => array(
            "relation" => "OR",
            array(
                array( 'key' => 'wpadverts_form', 'value' => 'advert' ),
                array( 'key' => 'wpadverts_form_field', 'value' => 'gallery' )
            ),
            array(
                array( 'key' => 'wpadverts_form', 'compare' => 'NOT EXISTS' ),
                array( 'key' => 'wpadverts_form_field', 'compare' => 'NOT EXISTS' )
            )
        )
    ) );
    
    foreach( $children as $child ) {
        $forced_thumbnail_id = apply_filters( "adverts_force_featured_image", $child->ID, $post_id );
        update_post_meta( $post_id, '_thumbnail_id', $forced_thumbnail_id );
        return 1;
    }
    
    return 0;
}

/**
 * Checks if passed $post is supported WPAdverts post type
 * 
 * @since   1.4.2
 * @param   mixed   $post   Either WP_Post object or post type string
 * @return  boolean         True if the $post is supported WPAdverts post type, false otherwise
 */
function wpadverts_post_type( $post ) {
    
    $post_type = null;
    
    if( $post instanceof WP_Post ) {
        $post_type = $post->post_type;
    } elseif( is_string( $post ) ) {
        $post_type = $post;
    }
    
    if( in_array( $post_type, wpadverts_get_post_types() ) ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Returns list of WPAdverts supported post types
 * 
 * @uses wpadverts_get_post_types filter
 * 
 * @since   1.4.2
 * @return  array    List of supported WPAdverts post types
 */
function wpadverts_get_post_types() {
    return apply_filters( "wpadverts_get_post_types", array( "advert" ) );
}

/**
 * Preserve Ad author
 * 
 * Function is executed via wp_insert_post_data filter. 
 * 
 * Checks if filter is run when using Quick Edit save on an WPAdverts supported
 * post type and if so prevents the change of author.
 * 
 * @since   1.4.5
 * @param   array   $data   Data to be saved in the database
 * @param   array   $post   Post data
 * @return  array           Data to be saved in the database
 */
function wpadverts_qe_preserve_author( $data, $post ) {
    if( ! defined( "DOING_AJAX" ) || ! DOING_AJAX ) {
        return $data;
    }
    if( adverts_request( "action" ) != "inline-save" ) {
        return $data;
    }
    if( ! check_admin_referer( 'inlineeditnonce', '_inline_edit' ) ) {
        return $data;
    }
    if( in_array( $data["post_type"], wpadverts_get_post_types() ) ) {
        $data["post_author"] = get_post( $post["ID"] )->post_author;
    }
    return $data; 
}

/**
 * Hides Author field in Quick Edit
 * 
 * The function adds a CSS rule to hide Author field in Quick Edit on WPAdverts
 * supported custom post types
 * 
 * @since   1.4.5
 * @return  void
 */
function wpadverts_qe_hide_author_field() {
    if( in_array( adverts_request( "post_type" ), wpadverts_get_post_types() ) ) {
        $tpl = '<style type="text/css">.inline-edit-%s .inline-edit-author {display: none !important; }</style>';
        printf( $tpl, esc_attr( adverts_request( "post_type" ) ) );
    }
}

/**
 * Renames directory
 * 
 * The function is using default 'rename' function with a fallback to recurisve
 * copy and delete in case the 'rename' function usage is limited on the server.
 * 
 * @see wpadverts_recursive_copy()
 * @see wpadverts_recursive_delete()
 * 
 * @since   1.5.0 
 * 
 * @param   string      $old    Folder which will be moved
 * @param   string      $new    New folder name
 * @return  boolean
 */
function wpadverts_rename_dir( $old, $new ) {
    
    $old = rtrim( $old, "/" );
    $new = rtrim( $new, "/" );
    
    if( ! is_dir( $old ) ) {
        return false;
    }
    
    $wpupload = wp_upload_dir();
    $stat = @stat( $wpupload["basedir"] );
    $perms = $stat['mode'] & 0007777;

    $moved = @rename( $old, $new );
    
    if ( ! $moved ) {
        if( ! wpadverts_recursive_copy( $old, $new, $perms ) ) {
            wpadverts_recursive_delete( $new );
            return false;
        }
        wpadverts_recursive_delete( $old );
    } 
        
    chmod($new, $perms);
    
    $new_list = glob( $new );
    
    if( is_array( $new_list ) ) {
        foreach( $new_list as $sub ) {
            chmod( $sub, $perms );
        }
    }
    
    return $moved;
}

/**
 * Recursively copies $source to $dest
 * 
 * The function will use RecursiveDirectoryIterator to copy the whole 
 * $source folder (with all files and sub-folders inside) to $dest.
 * 
 * @since   1.5.0
 * 
 * @param   string    $source   Folder to copy
 * @param   string    $dest     Destination folder
 * @param   mixed     $perms    CHMOD to set on the created folders
 * @return  boolean
 */
function wpadverts_recursive_copy( $source, $dest, $perms = 0755 ) {

    if ( ! is_dir( $dest ) ) {
        if ( ! mkdir( $dest, $perms, true ) ) {
            return false;
        }
    }
    $directoryIterator = new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS);
    $recursiveIterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::SELF_FIRST);
    foreach ( $recursiveIterator as $item ) {
        if ($item->isDir()) {
            if ( ! mkdir($dest . DIRECTORY_SEPARATOR . $recursiveIterator->getSubPathName(), $perms) ) {
                return false;
            }
        } else {
            if ( ! copy($item, $dest . DIRECTORY_SEPARATOR . $recursiveIterator->getSubPathName()) ) {
                return false;
            }
        }
    }
    return true;
}

/**
 * Recusrively deletes a directory.
 * 
 * The function will delete a $dirname and all files and sub-directoris inside it.
 * 
 * @since   1.5.0
 * 
 * @param   string      $dirname    Absolute path to directory to be deleted
 * @return  boolean
 */
function wpadverts_recursive_delete( $dirname ) { 
    if(is_dir($dirname)) {
        $dir_handle = opendir($dirname);
    } else {
        return true;
    }
    
    while($file = readdir($dir_handle)) {
        if($file!="." && $file!="..") {
            if(!is_dir($dirname."/".$file)) {
                unlink ($dirname."/".$file);
            } else {
                wpadverts_recursive_delete($dirname."/".$file);
            }
        }
    }
    
    closedir($dir_handle);
    rmdir($dirname);
    
    return true;
}

/**
 * Returns form scheme based on form name
 * 
 * The function searches in the forms added to Adverts::instance()->set()
 * 
 * @since   1.5.0
 * @param   string      $form_name      Form name passed in $form["name"]
 * @return  array                       Form scheme
 */
function wpadverts_get_form( $form_name ) {
    
    foreach( Adverts::instance()->get_all() as $k => $data ) {
        if( is_array( $data ) && isset( $data["name"] ) && $data["name"] == $form_name && isset( $data["field"] ) ) {
            return $data;
        }
    }
    
    return null;
}

/**
 * Deletes the post files (other than the ones in Media Library)
 * 
 * This function is executed by "deleted_post" action
 * 
 * @see deleted_post action
 * 
 * @since   1.5.0
 * @param   int         $post_id
 * @param   WP_Post     $post
 * @return  void
 */
function adverts_deleted_post( $post_id, $post = null ) {
    
    if( $post === null ) {
        $post = get_post( $post_id );
    }
    
    if( ! wpadverts_post_type( $post ) ) {
        return;
    }
    
    $form_name = "advert";
    
    include_once ADVERTS_PATH . '/includes/class-upload-helper.php';
    
    $v = new Adverts_Upload_Helper;
    $v->set_form_name( $form_name );
    $v->set_post_id( $post_id );
    
    $form_params = array(
        "form_scheme_id" => get_post_meta( $post_id, "_wpacf_form_scheme_id", true )
    );

    $form_scheme = apply_filters( "adverts_form_scheme", wpadverts_get_form( $form_name ), $form_params );
    $form_scheme = apply_filters( "adverts_form_load", $form_scheme );

    foreach($form_scheme["field"] as $key => $field) {
        
        if( $field["type"] != "adverts_field_gallery" ) {
            continue;
        }
        
        if( ! isset( $field["save"] ) || $field["save"]["method"] != "file" ) {
            continue;
        }
        
        $v->set_field( $field );
            
        $files_path = $v->get_path_dest() . "/*";
        $files_all = glob( $files_path );
        
        if( ! is_array( $files_all ) ) {
            $files_all = array();
        }
        
        foreach( $files_all as $file ) {
            
            if( ! file_exists( $file ) ) {
                continue;
            }
            
            do {
                if( is_dir( $file ) ) {
                    rmdir( $file );
                } else {
                    wp_delete_file( $file );
                }
                $file = dirname( $file );
                $files = glob( $file . "/*" );
            } while( empty( $files ) );
            
        }
    }
}

/**
 * Returns all files assigned to the $post
 * 
 * The function searches for file fields in the form_scheme and finds all files
 * and media library items assigned to this $post
 * 
 * @since   1.5.0
 * @param   mixed       $post   WP_Post|int Post id or WP_Post 
 * @return  array               List of files assigned to this $post
 */
function adverts_get_post_files( $post ) {
    $filtered = array();
    
    foreach( adverts_get_post_files_data( $post ) as $k => $files ) {
        $filtered[$k] = array();
        foreach( $files as $file ) {
            $filtered[$k][] = $file["file"];
        }
    }

    return $filtered;
}

/**
 * Returns all files assigned to the $post
 * 
 * The function searches for file fields in the form_scheme and finds all files
 * and media library items assigned to this $post
 * 
 * The returned array contains an associative array
 * array( "file" => "/path/to/file.png", "url" => "https://example.com/file.png" )
 * 
 * 
 * @since   1.5.0
 * @param   mixed       $post   WP_Post|int Post id or WP_Post 
 * @return  array               List of files assigned to this $post
 */
function adverts_get_post_files_data( $post ) {
    $post_id = $post;

    if( $post instanceof WP_Post ) {
        $post_id = $post->ID;
    }

    include_once ADVERTS_PATH . '/includes/class-form.php';
    include_once ADVERTS_PATH . '/includes/class-upload-helper.php';

    $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get("form"), array( "post_id" => $post_id ) );
    $form = new Adverts_Form( $form_scheme );
    
    $files = array();

    foreach( $form->get_fields() as $field ) {

        if( $field["type"] != "adverts_field_gallery" ) {
            continue;
        }

        if( isset( $field["save"]["method"] ) && $field["save"]["method"] == "file" ) {
            $v = new Adverts_Upload_Helper;
            $v->set_field( $field );
            $v->set_form_name( $form->get_scheme( "name" ) );
            $v->set_post_id( $post_id );

            $files[ $field["name"] ] = array();

            $all_files = glob( $v->get_path_dest() . "/*" ) ;

            if( ! is_array( $all_files ) ) {
                $all_files = array();
            }

            foreach( $all_files as $f ) {
                $files[ $field["name"] ][] = array(
                    "file" => $f,
                    "url" => $v->get_uri_dest() . "/" . basename( $f ),
                );
            }
        } else if( ! isset( $field["save"]["method"] ) || $field["save"]["method"] == "media-library" ) {
            include_once ADVERTS_PATH . "/includes/class-gallery-helper.php";

            $gh = new Adverts_Gallery_Helper( $post_id );
            $att = $gh->load_attachments();

            $files[ $field["name"] ] = array();

            foreach( $att as $at ) {
                $files[ $field["name"] ][] = array(
                    "file" => get_attached_file( $at->ID ),
                    "url" => wp_get_attachment_url( $at->ID )
                );
            }
        }

    }

    return $files;
}

/**
 * Returns path to WPAdverts tmp directory
 * 
 * By default the tmp directory is WP_UPLOAD_DIR/wpadverts-tmp/
 * 
 * In the tmp directory WPAdverts is storing files which should be deleted 
 * if the user will leave the form.
 * 
 * @since   1.5.0
 * @return  string
 */
function adverts_get_tmp_dir() {
    $dirs = wp_upload_dir();
    $basedir = $dirs["basedir"];
    $tmpdir = rtrim( $basedir, "/") . "/wpadverts-tmp";
    
    return apply_filters( "adverts_get_tmp_dir", $tmpdir );
}

/**
 * Returns URL to WPAdverts tmp directory
 * 
 * By default the tmp directory is wp_upload_dir()[baseurl]/wpadverts-tmp/
 * 
 * In the tmp directory WPAdverts is storing files which should be deleted 
 * if the user will leave the form.
 * 
 * @since   1.5.1
 * @return  string
 */
function adverts_get_tmp_url() {
    $dirs = wp_upload_dir();
    $baseurl = $dirs["baseurl"];
    $tmpurl = rtrim( $baseurl, "/") . "/wpadverts-tmp";
    
    return apply_filters( "adverts_get_tmp_url", $tmpurl );
}