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
    
    $file["error"] = __("Uploaded file is NOT an image", "adverts" );
    
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
 * 
 * @param float $price  Price to format
 * @param int $post_id
 * @return string       Formatted Price
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
 * @param int $id Post ID
 * @since 0.1
 * @return mixed Image URL or NULL
 */
function adverts_get_main_image( $id ) {
    
    $thumb_id = get_post_thumbnail_id( $id );
    
    if($thumb_id) {
        $image = wp_get_attachment_image_src( $thumb_id, 'adverts-list' );
    } else {
        foreach(get_children(array('post_parent'=>$id, 'numberposts'=>1)) as $tmp_post) {
            $image = wp_get_attachment_image_src( $tmp_post->ID , 'adverts-list' ); 
        }
    }
    
    if(isset($image[0])) {
        return $image[0];
    } else {
        return null;
    }
    
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
    
    if (is_singular('advert') && in_the_loop() ) {
        ob_start();
        $post_id = get_the_ID();
        
        $post_content = get_post( $post_id )->post_content;
        $post_content = wp_kses($post_content, wp_kses_allowed_html( 'post' ) );
        $post_content = apply_filters( "adverts_the_content", $post_content );
        
        include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/single.php' );
        $content = ob_get_clean();
    } elseif( is_tax( 'advert_category' ) && in_the_loop() ) {
        add_action( 'adverts_sh_list_before', 'adverts_list_show_term_description' );
        $content = shortcode_adverts_list(array(
            "category" => $wp_query->get_queried_object_id()
        ));
        remove_action( 'adverts_sh_list_before', 'adverts_list_show_term_description' );
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
 * @param array $posts
 * @param WP_Query $query
 * @return array Post objects
 */
function adverts_posts_results( $posts, $query ) {
    if( $query->is_main_query() && $query->is_tax("advert_category") ) {
        
        $title = sprintf( __("Category: %s", "adverts"), $query->get_queried_object()->name );
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

    if( is_tax( 'advert_category' ) ) {
        return @get_page_template();
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
    
    if( 'advert'==$post->post_type && in_the_loop()) {
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
    if( count( $data ) > $params["max_choices"] ) {
        return "invalid";
    } else {
        return true;
    }
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
        "type" => "text",
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

    if(isset($field["attr"])) {
        $options += $field["attr"];
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

    if(isset($field["options_callback"])) {
        $opt = call_user_func( $field["options_callback"] );
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
            "value" => $v["value"],
            "data-depth" => $depth,
            "selected" => $selected,
        ), $padding . $v["text"]);

        $html .= $o->render();
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
    
        $params = array(
            "quicktags"=>false, 
            "media_buttons"=>false, 
            "teeny"=>false,
            "textarea_rows" => 8,
            'tinymce' => array(
                'toolbar1' => 'bold,italic,strikethrough,bullist,numlist,blockquote,justifyleft,justifycenter,justifyright,link,unlink,spellchecker,wp_adv',
                'theme_advanced_buttons2' => 'formatselect,justifyfull,forecolor,pastetext,pasteword,removeformat,charmap,outdent,indent,undo,redo',

                'theme_advanced_buttons1' => 'bold,italic,strikethrough,bullist,numlist,blockquote,justifyleft,justifycenter,justifyright,link,unlink,spellchecker,wp_adv',
                'theme_advanced_buttons2' => 'formatselect,justifyfull,forecolor,pastetext,pasteword,removeformat,charmap,outdent,indent,undo,redo',
             )
        );

        wp_editor($field["value"], $field["name"], $params);
    } elseif($field["mode"] == "tinymce-full") {
        wp_editor($field["value"], $field["name"]);
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
    
    if(isset($field["options_callback"])) {
        $opt = call_user_func( $field["options_callback"] );
    } elseif(isset($field["options"])) {
        $opt = $field["options"];
    } else {
        trigger_error("You need to specify options source for field [{$field['name']}].", E_USER_ERROR);
        $opt = array();
    }

    foreach($opt as $v) {
        $checkbox = new Adverts_Html("input", array(
            "type" => "checkbox",
            "name" => $field["name"].'[]',
            "id" => $field["name"].'_'.$i,
            "value" => $v["value"],
            "checked" => in_array($v["value"], $value) ? "checked" : null
        ));

        $label = new Adverts_Html("label", array(
            "for" => $field["name"].'_'.$i
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
    
    if( !isset( $field["value"] ) ) {
        $value = null;
    } else {
        $value = $field["value"];
    }
    
    foreach($field["options"] as $opt) {
        $checkbox = new Adverts_Html("input", array(
            "type" => "radio",
            "name" => $field["name"],
            "id" => $field["name"].'_'.$i,
            "value" => $opt["value"],
            "checked" => $opt["value"] == $value ? "checked" : null
        ));

        $label = new Adverts_Html("label", array(
            "for" => $field["name"].'_'.$i
        ), $checkbox->render() . ' ' . $opt["text"]);
        
        $opts .= "<div>".$label->render()."</div>";
        
        $i++;
    }
    
    echo Adverts_Html::build("div", array("class"=>"adverts-form-input-group adverts-form-input-group-radio"), $opts);
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
        
        $text = __('You are posting as <strong>%1$s</strong>. <br/>If you want to use a different account, please <a href="%2$s">logout</a>.', 'adverts');
        printf( '<div>'.$text.'</div>', wp_get_current_user()->display_name, wp_logout_url() );
        
    } else {
        
        $text = __('Create an account for me so I can manage all my ads from one place (password will be emailed to you) or <a href="%s">Sign In</a>', 'adverts');
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
 * @param array $field Should be an empty array
 * @since 0.1
 * @return void
 */
function adverts_field_gallery($field) {
    include_once ADVERTS_PATH . "includes/gallery.php";
    
    wp_enqueue_script( 'adverts-gallery' );
    
    $post_id = adverts_request("_post_id", adverts_request("advert_id"));
    $post = $post_id>0 ? get_post( $post_id ) : null;
    
    adverts_gallery_content($post, array( 
        "button_class" => "adverts-button",
        "post_id_input" => "#_post_id"
    ));
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

    <h3><?php _e("Redirecting", "adverts") ?></h3>
    <p><?php printf(__('Your are being redirected to Edit page. <a href="%s">Click here</a> if it is taking to long. ', 'adverts'), $url) ?></p>
    
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
    <?php call_user_func( adverts_field_get_renderer($field), $field) ?>
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
                <label <?php if(!in_array($field['type'], $a)): ?>for="<?php esc_attr_e($field["name"]) ?>"<?php endif; ?>>
                    <?php esc_html_e($field["label"]) ?>
                    <?php if(adverts_field_has_validator($field, "is_required")): ?><span class="adverts-red">&nbsp;*</span><?php endif; ?>
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
                
                <?php call_user_func( adverts_field_get_renderer($field), $field) ?>

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
        array("code"=>"USD", "sign"=>"$", "label"=>__("US Dollars", "adverts")),
        array("code"=>"EUR", "sign"=>"€", "label"=>__("Euros", "adverts")),
        array("code"=>"GBP", "sign"=>"£", "label"=>__("Pounds Sterling", "adverts")),
        array("code"=>"AUD", "sign"=>"$", "label"=>__("Australian Dollars", "adverts")),
        array("code"=>"BRL", "sign"=>"R$", "label"=>__("Brazilian Real", "adverts")),
        array("code"=>"CAD", "sign"=>"$", "label"=>__("Canadian Dollars", "adverts")),
        array("code"=>"CZK", "sign"=>"", "label"=>__("Czech Koruna", "adverts")),
        array("code"=>"DKK", "sign"=>"", "label"=>__("Danish Krone", "adverts")),
        array("code"=>"HKD", "sign"=>"$", "label"=>__("Hong Kong Dollar", "adverts")),
        array("code"=>"HUF", "sign"=>"", "label"=>__("Hungarian Forint", "adverts")),
        array("code"=>"ILS", "sign"=>"₪", "label"=>__("Israeli Shekel", "adverts")),
        array("code"=>"JPY", "sign"=>"¥", "label"=>__("Japanese Yen", "adverts")),
        array("code"=>"MYR", "sign"=>"", "label"=>__("Malaysian Ringgits", "adverts")),
        array("code"=>"MXN", "sign"=>"$", "label"=>__("Mexican Peso", "adverts")),
        array("code"=>"NZD", "sign"=>"$", "label"=>__("New Zealand Dollar", "adverts")),
        array("code"=>"NOK", "sign"=>"", "label"=>__("Norwegian Krone", "adverts")),
        array("code"=>"PHP", "sign"=>"", "label"=>__("Philippine Pesos", "adverts")),
        array("code"=>"PLN", "sign"=>"zł", "label"=>__("Polish Zloty", "adverts")),
        array("code"=>"SGD", "sign"=>"$", "label"=>__("Singapore Dollar", "adverts")),
        array("code"=>"SEK", "sign"=>"", "label"=>__("Swedish Krona", "adverts")),
        array("code"=>"CHF", "sign"=>"", "label"=>__("Swiss Franc", "adverts")),
        array("code"=>"TWD", "sign"=>"", "label"=>__("Taiwan New Dollars", "adverts")),
        array("code"=>"THB", "sign"=>"฿", "label"=>__("Thai Baht", "adverts")),
        array("code"=>"INR", "sign"=>"", "label"=>__("Indian Rupee", "adverts")),
        array("code"=>"TRY", "sign"=>"", "label"=>__("Turkish Lira", "adverts")),
        array("code"=>"RIAL", "sign"=>"", "label"=>__("Iranian Rial", "adverts")),
        array("code"=>"RUB", "sign"=>"", "label"=>__("Russian Rubles", "adverts")),
        array("code"=>"ZAR", "sign"=>"R", "label"=>__("South African Rand", "adverts")),
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
 * @param stdClass $term Term object
 * @since 0.3
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
    return $count;
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
    if(is_post_type_archive( "advert" )) {
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
function adverts_sort_images($images, $post_id) {
    $images_order = json_decode(get_post_meta($post_id, '_adverts_attachments_order', true));

    if ( !is_null($images_order) ) {
        include_once ADVERTS_PATH . 'includes/class-sort-images.php';
        uksort( $images, array( new Adverts_Sort_Images( $images_order ), "sort" ) );
    }

    return $images;
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
    
    $children = get_children( array( 'post_parent' => $post_id ) );
    $thumb_id = get_post_thumbnail_id( $post_id );
    $images = array();
    
    if( empty( $children ) ) {
        return;
    }
    
    if( isset( $children[$thumb_id] ) ) {
        $images[$thumb_id] = $children[$thumb_id];
        unset($children[$thumb_id]);
    }
    
    $images += $children;
    $images = adverts_sort_images($images, $post_id);

    wp_enqueue_script( 'responsive-slides' );
    
    ?>
    <div class="rslides_container">
        <ul id="slides1" class="rslides rslides1">
            <?php foreach($images as $tmp_post): ?>
                <?php $image = wp_get_attachment_image_src( $tmp_post->ID, 'large' ) ?>
                <?php if(isset($image[0])): ?>
                <li>
                    <img src="<?php esc_attr_e($image[0]) ?>" alt="">

                    <?php if($tmp_post->post_excerpt || $tmp_post->post_content): ?>
                    <p class="caption">
                        <strong><?php esc_html_e($tmp_post->post_excerpt) ?></strong>
                        <?php esc_html_e($tmp_post->post_content) ?>
                    </p>
                    <?php endif; ?>
                </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>   
    <?php    
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
            <?php esc_html_e("Show Contact Information", "adverts") ?>
            <span class="adverts-icon-down-open"></span>
        </a>
        <span class="adverts-loader adverts-icon-spinner animate-spin"></span>
    </div>

    <div class="adverts-contact-box">
        <p class="adverts-contact-method">
            <span class="adverts-icon-phone adverts-contact-icon" title="<?php _e("Phone", "adverts") ?>"></span>
            <span class="adverts-contact-phone"></span>
        </p>

        <p class="adverts-contact-method">
           <span class="adverts-icon-mail-alt adverts-contact-icon" title="<?php _e("Email", "adverts") ?>"></span>
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
            $menu[$key][0] .= sprintf( $wrap, __( "Pending Ads", "adverts" ) );
            break;
        }
    }
    
    
    return $menu;
}