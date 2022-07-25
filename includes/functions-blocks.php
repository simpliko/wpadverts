<?php

function adverts_field_checkbox_block( $field, $form = null, $type = "checkbox" ) {
    
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
            "type" => $type,
            "name" => $field["name"].'[]',
            "id" => $id,
            "value" => $v["value"],
            "class" => " atw-flex-none atw-self-center",
            "checked" => in_array($v["value"], $value) ? "checked" : null
        ));

        $label = new Adverts_Html("label", array(
            "for" => $id,
            "class" => "atw-flex-1 atw-inline-block atw-py-0 atw-px-2 atw-text-gray-500 atw-align-baseline atw-truncate atw-cursor-pointer"
        ),  $v["text"]);
        
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
            "class" => "atw-flex atw-flex-row atw-grow atw-align-baseline",
        ), $padding . $checkbox->render() . $label->render() );
        
        $opts .= $wrap->render();
        
        $i++;
    }
    
    //$field["rows"] = 3;

    $wrap_classes = array();
    if( absint( $field["rows"] ) >= 1 ) {
        $wrap_classes[] = sprintf( "atw-grid atw-grid-cols-%d atw-gap-2", absint( $field["rows"] ) );
    } else {
        $wrap_classes[] = "atw-flex atw-flex-wrap atw-flex-row atw-content-evenly";
    }
    
    echo Adverts_Html::build("div", array("class"=> join( " ", $wrap_classes ) ), $opts);
}

function adverts_field_radio_block( $field, $form = null ) {
    adverts_field_checkbox_block($field, $form, "radio");
}

/**
 * Block based buttons
 * 
 * $args[
 *  "text"  => "button text",
 *  "html"  => "HTML do display instead of 'text' ",
 *  "icon"  => "fa-* icon",
 *  "type"  => "either primary or secondary",
 *  "class" => "custom class to add",
 *  "attr"  => array( 'key' => 'value / attributes to add to button' )
 * ]
 * 
 * $options[
 *  "border_radius"         => (default from config)
 *  "border_width"          => (default from config)
 *  "font_weight"           => (default from config)
 *  "color_text"            => (default from config)
 *  "color_bg"              => (default from config)
 *  "color_border"          => (default from config)
 *  "color_text_h"          => (default from config)
 *  "color_bg_h"            => (default from config)
 *  "color_border_h"        => (default from config)
 *  "desktop"               => text-and-icon
 *  "desktop_icon_size"     => "atw-text-base"
 *  "mobile"                => text-and-icon
 *  "mobile_icon_size"      => "atw-text-2xl"
 * ]
 * 
 * @since   2.0
 * @param   array   $args
 * @param   array   $options
 * @return  void
 */
function wpadverts_block_button( $args = array(), $options = array() ) {

    $atts= shortcode_atts( array(
        "text" => "",
        "icon" => "",
        "type" => "secondary",
        "class" => "",
        "attr" => array(),
        "action" => "button",
        "name" => "",
        "value" => ""
    ), $args );
    
    $_args = array(
        "classes_prepend" => "atw-w-full",
        "classes_append" => ""
    );

    $_customize = array(
        "primary" => "primary_button",
        "secondary" => "secondary_button"
    );

    //echo "<pre>";
    //print_r($args);
    //print_r($options);

    if( isset( $_customize[ $args["type"] ] ) ) {

        if( ! isset( $options["customize"] ) || ! $options["customize"] ) {
            $_options = adverts_config( sprintf( "blocks_styling.%s", $_customize[ $args["type"] ] ) );
        } else {
            $_options = array();
        }

        $_options["desktop"] = "text-and-icon";
        $_options["mobile"] = "text-and-icon";
        $_options["desktop_icon_size"] = "atw-text-base";
        $_options["mobile_icon_size"] = "atw-text-base";

        $options = array_merge( $_options, $options );

        foreach( $options as $k => $v ) {
            if( empty( $v ) && isset( $_options[$k] ) ) {
                $options[$k] = $_options[$k];
            }
        }
    }

    //print_r($_options);
    //print_r($options);
    //echo "</pre>";

    $button_class = trim( sprintf( "wpa-btn-%s %s ", $args["type"], $atts["class"] ) ) ." ";

    $defaults = array(
        "type"              => isset( $args["type"] ) ? $args["type"] : "",
        "classes"           => "atw-text-base atw-outline-none atw-bg-none atw-border atw-border-solid atw-font-semibold atw-px-4",
        "text"              => isset( $args["text"] ) ? $args["text"] : "",
        "html"              => isset( $args["html"] ) ? $args["html"] : "",
        "icon"              => isset( $args["icon"] ) ? $args["icon"] : "",
        "icon-position"     => "left"
    );

    $radius_options = array(
        0 => "atw-rounded-none",
        1 => "atw-rounded-sm",
        2 => "atw-rounded",
        3 => "atw-rounded-md",
        4 => "atw-rounded-lg",
        5 => "atw-rounded-xl",
        6 => "atw-rounded-full"
    );

    $width_options = array(
        0 => "atw-border-0",
        1 => "atw-border",
        2 => "atw-border-2",
        3 => "atw-border-4",
    );

    $leading_options = array(
        0 => "atw-leading-loose",
        1 => "atw-leading-loose",
        2 => "atw-leading-relaxed",
        3 => "atw-leading-relaxed"
    );

    if( isset( $options["border_radius"] ) && isset( $radius_options[ $options["border_radius"] ] ) ) {
        $border_radius = $radius_options[ $options["border_radius"] ];
    } else {
        $border_radius = $radius_options[0];
    }

    if( isset( $options["border_width"] ) && isset( $width_options[ $options["border_width"] ] ) ) {
        $border_width = $width_options[ $options["border_width"] ];
    } else {
        $border_width = $width_options[0];
    }

    $leading = "atw-leading-loose";

    $d_text = "";
    $d_icon = "";
    $m_text = "";
    $m_icon = "";

    switch( $options["desktop"] ) {
        case "text": 
            $d_text = "md:atw-inline"; 
            $d_icon = "md:atw-hidden"; 
            break;
        case "icon": 
            $d_text = "md:atw-hidden"; 
            $d_icon = "md:atw-inline";
            break;
        case "text-and-icon": 
            $d_text = "md:atw-inline";
            $d_icon = "md:atw-inline";
            break;
    }

    switch( $options["mobile"] ) {
        case "text": 
            $m_text = "atw-inline"; 
            $m_icon = "atw-hidden"; 
            break;
        case "icon": 
            $m_text = "atw-hidden"; 
            $m_icon = "atw-inline";
            break;
        case "text-and-icon": 
            $m_text = "atw-inline";
            $m_icon = "atw-inline";
            break;
    }

    $attr_list = array();
    foreach( $atts["attr"] as $k => $v) {
        $attr_list[] = sprintf( '%s="%s"', $k, $v );
    }

    $__load = array(
        "md:atw-text-sm",
        "md:atw-text-base",
        "md:atw-text-lg",
        "md:atw-text-xl",
        "md:atw-text-2xl",
        "md:atw-text-3xl"
    );

    ?>
    <button name="<?php echo esc_attr( $atts["name"] ) ?>" value="<?php echo esc_attr( $atts["value"] ) ?>" type="<?php echo esc_attr( $atts["action"] ) ?>" <?php echo join( " ", $attr_list ) ?> class="<?php echo $button_class ?> atw-flex hover:atw-bg-none atw-bg-none atw-w-full atw-text-base atw-outline-none atw-border-solid atw-font-semibold atw-px-6 atw-py-2 <?php echo "$border_radius $border_width $leading" ?>">
        <div class="atw-flex-grow">
        <?php if( isset( $atts["icon"] ) && $atts["icon"] ): ?>
        <span class="<?php echo join( " ", array( $m_icon, $d_icon ) ) ?> atw-px-0.5"><i class="<?php echo esc_attr( sprintf( "%s %s md:%s", $atts["icon"], $options["mobile_icon_size"], $options["desktop_icon_size"] ) ) ?>"></i></span> 
        <?php endif; ?>
        <span class="<?php echo join( " ", array( $m_text, $d_text ) ) ?> atw-px-0.5"><?php echo ( !empty( $args["html"] ) ? $args["html"] : esc_html( $args["text"] ) ) ?></span>
        </div>
        <span style="display:none" class="atw-flex-0 atw-pl-6 atw-border-l atw-border-gray-800/20 atw-border-solid"><i class="fas fa-angle-down atw-text-2xl -atw-leading-tight atw-align-middle"></i></span>
    </button>
    <?php
}

function wpadverts_block_button_css( $type, $args, $rule_prefix = "" ) {

    $_customize = array(
        "primary" => "primary_button",
        "secondary" => "secondary_button"
    );

    if( isset( $_customize[ $type ] ) && ( ! isset( $args["customize"] ) || ! $args["customize"] ) ) {
        $_options = adverts_config( sprintf( "blocks_styling.%s", $_customize[ $type ] ) );
        $args = array_merge( $args, $_options );
    }

    $color_text     = isset( $args["color_text"] )      ? $args["color_text"]       : null;
    $color_bg       = isset( $args["color_bg"] )        ? $args["color_bg"]         : null;
    $color_border   = isset( $args["color_border"] )    ? $args["color_border"]     : null;
    
    $color_text_h   = isset( $args["color_text_h"] )    ? $args["color_text_h"]     : null;
    $color_bg_h     = isset( $args["color_bg_h"] )      ? $args["color_bg_h"]       : null;
    $color_border_h = isset( $args["color_border_h"] )  ? $args["color_border_h"]   : null;
    
    $button_class = sprintf( "wpa-btn-%s", $type );

    ?>
    <?php echo sprintf( "%s .%s", $rule_prefix, $button_class ) ?> {
        color: <?php echo $color_text ?>;
        background-color: <?php echo $color_bg ?>;
        border-color: <?php echo $color_border ?>;
        --wpa-btn-shadow-color: <?php echo $color_border ?>;
    }
    <?php echo sprintf( "%s .%s", $rule_prefix, $button_class ) ?> > span > i.fas {
        color: <?php echo $color_text ?>;
    }
    <?php echo sprintf( "%s .%s:hover", $rule_prefix, $button_class ) ?> {
        color: <?php echo $color_text_h ?>;
        background-color: <?php echo $color_bg_h ?>;
        border-color: <?php echo $color_border_h ?>;
        --wpa-btn-shadow-color: <?php echo $color_border_h ?>;
    }
    <?php echo sprintf( "%s .%s:hover", $rule_prefix, $button_class ) ?> > span > i.fas {
        color: <?php echo $color_text_h ?>;
    }
    <?php
}

function wpadverts_get_grays_palette( $gray ) {
    $palettes = apply_filters( "wpadverts_grays_palette", array(
        "blue-gray" => array(
          50 => "#F8FAFC",  100 => "#F1F5F9", 200 => "#E2E8F0", 300 => "#CBD5E1", 400 => "#94A3B8", 500 => "#64748B", 600 => "#475569", 700 => "#334155", 800 => "#1E293B", 900 => "#0F172A"
        ),        
        "cool-gray" => array(
          50 => "#F9FAFB",  100 => "#F3F4F6", 200 => "#E5E7EB", 300 => "#D1D5DB", 400 => "#9CA3AF", 500 => "#6B7280", 600 => "#4B5563", 700 => "#374151", 800 => "#1F2937", 900 => "#111827"
        ),        
        "gray" => array(
          50 => "#FAFAFA",  100 => "#F4F4F5", 200 => "#E4E4E7", 300 => "#D4D4D8", 400 => "#A1A1AA", 500 => "#71717A", 600 => "#52525B", 700 => "#3F3F46", 800 => "#27272A", 900 => "#18181B"
        ),        
        "true-gray" => array(
          50 => "#FAFAFA",  100 => "#F5F5F5", 200 => "#E5E5E5", 300 => "#D4D4D4", 400 => "#A3A3A3", 500 => "#737373", 600 => "#525252", 700 => "#404040", 800 => "#262626", 900 => "#171717"
        ),        
        "warm-gray" => array(
          50 => "#FAFAF9",  100 => "#F5F5F4", 200 => "#E7E5E4", 300 => "#D6D3D1", 400 => "#A8A29E", 500 => "#78716C", 600 => "#57534E", 700 => "#44403C", 800 => "#292524", 900 => "#1C1917"
        ),
    ) );

    return $palettes[ $gray ];
};

function wpadverts_block_form_styles( $atts ) {

    if( ! isset( $atts["customize"] ) || $atts["customize"] != 1 ) {
        $atts = adverts_config( "blocks_styling.form" );
    }

    $form_border = array(
        0 => "wpa-border-none",
        1 => "wpa-border-thin",
        2 => "wpa-border-thick",
        3 => "wpa-border-thick-x2"
    );
    $form_rounded = array(
        0 => "wpa-rounded-none",
        1 => "wpa-rounded-sm",
        2 => "wpa-rounded",
        3 => "wpa-rounded-md",
        4 => "wpa-rounded-lg",
        5 => "wpa-rounded-xl",
        6 => "wpa-rounded-2xl",
    );

    $form_spacing = array(
        0 => "wpa-spacing-0",
        1 => "wpa-spacing-1",
        2 => ""
    );

    $atts = apply_filters( "wpadverts/block/form/styles/atts", $atts );

    $form_styles = join( " ", array(
        isset( $atts["style"] ) ? $atts["style"] : "",
        isset( $atts["shadow"] ) ? $atts["shadow"] : "",
        isset( $atts["border"] ) && $atts["border"] ? $form_border[ $atts["border"] ] : $form_border[0],
        isset( $atts["rounded"] ) && $atts["rounded"] ? $form_rounded[ $atts["rounded"] ] : $form_rounded[0],
        isset( $atts["spacing"] ) && $atts["spacing"] ? $form_spacing[ $atts["spacing"] ] : $form_spacing[0],
        isset( $atts["interline"] ) && $atts["interline"] ? "wpa-form-interline" : "",
        "wpa-padding-sm"
    ) );

    return $form_styles;
}

function wpadverts_print_grays_variables( $atts ) {

    if( ! isset( $atts["customize"] ) || $atts["customize"] != 1 ) {
        $atts = adverts_config( "blocks_styling.form" );
    }

    $gray = $atts["palette"];
    $grays = wpadverts_get_grays_palette( $gray );

    foreach( $grays as $k => $v ) {
        echo sprintf( "--wpa-color-gray-%d: %s;\r\n", $k, adverts_hex2rgba( $v ) );
    }
}

function wpadverts_get_object_pattern( $object_id, $pattern ) {
    $patterns = array(
        "pattern__price" => "wpadverts_block_list_price",
        "pattern__location" => null,
        "pattern__post_date" => "wpadverts_block_list_post_date"
    );

    if( isset( $patterns[$pattern] ) && is_callable( $patterns[$pattern] ) ) {
        return call_user_func( $patterns[$pattern], $object_id, $pattern );
    }

    return null;
}

function wpadverts_get_object_value( $object_id, $path ) {
    $advert = get_post( $object_id );

    list( $type, $name ) = explode( "__", $path );

    $value = null;

    if( $type == "default" ) {
        $value = $advert->$name;
    } else if( $type == "date" ) {
        $value = $advert->$name;
    } else if( $type == "meta" ) {
        $value = get_post_meta( $object_id, $name, true );
    } else if( $type == "pattern" ) {
        $value = wpadverts_get_object_pattern( $object_id, $path );
    }

    return $value;
}



function wpadverts_block_tpl_wrap( $post_id, $path, $classes = "") {
    $value = wpadverts_get_object_value( $post_id, $path );

    if( $value ) {
        return sprintf( '<div class="%s">%s</div>', $classes, $value );
    }
}

function wpadverts_block_list_price( $post_id ) {
    $price = get_post_meta( $post_id, "adverts_price", true );
    $price_f = null;

    if( $price ) {
        $price_f = adverts_get_the_price( $post_id, $price );
    } elseif( adverts_config( 'empty_price' ) ) {
        $price_f = adverts_empty_price( $post_id );
    }

    return $price_f;
}

function wpadverts_block_list_post_date( $post_id ) {
    return date_i18n( "d/m/Y", get_post_time( 'U', false, $post_id ) );
}

function wpadverts_block_tpl_field_width( $field ) {
    $arr = array(
        "full" => "atw-w-full",
        "half" => "atw-w-full md:atw-w-2/4",
        "third" => "atw-w-full md:atw-w-1/3",
        "fourth" => "atw-w-full md:atw-w-1/4"
    );

    if( ! isset( $field['meta'] ) || ! isset( $field['meta']['search_type'] ) ) {
        $field_type = "full";
    }  else {
        $field_type = $field['meta']['search_type'];
    }

    return $arr[ $field_type ] . " wpa-w-" . $field_type;
}

function wpadverts_block_img_options( $prop ) {
    $width = array(
        "w-1/12", 
        "w-2/12", 
        "w-3/12", 
        "w-4/12", 
        "w-5/12", 
        "w-6/12", 
        "w-7/12", 
        "w-8/12", 
        "w-9/12", 
        "w-10/12", 
        "w-11/12", 
        "w-12/12", 
    );
    $height = array(
        "atw-h-16", 
        "atw-h-20", 
        "atw-h-24", 
        "atw-h-28", 
        "atw-h-32", 
        "atw-h-36", 
        "atw-h-40", 
        "atw-h-44", 
        "atw-h-48", 
        "atw-h-52", 
        "atw-h-56", 
        "atw-h-60",
        "atw-h-64",
        "atw-h-72",
        "atw-h-80",
        "atw-h-96",
    );
    $fit = array(
        "contain" => "atw-object-contain",
        "cover" => "atw-object-cover",
        "fill" => "atw-object-fill",
        "none" => "atw-object-none",
        "scale-down" => "atw-object-scale-down"
    );

    $props = array(
        "height" => $height,
        "width" => $width,
        "fit" => $fit
    );

    return $props[ $prop ];
}

function wpadverts_block_list_image_list( $post_id, $atts ) {

    $result = new stdClass;
    $image_id = adverts_get_main_image_id( $post_id );
    $image_type = "adverts-list";
    $image = false;

    $classes = array();
    $classes_img = array();

    $default_image_url = null;

    if( isset( $atts["default_image_url"] ) && ! empty( $atts["default_image_url"] ) ) {
        $default_image_url = $atts["default_image_url"];
    }

    if( $image_id ) {
        $image = get_post( $image_id );
    }

    $widths = array( "atw-w-16", "atw-w-20", "atw-w-24", "atw-w-28", "atw-w-32", "atw-w-36", "atw-w-40", "atw-w-44", "atw-w-48", "atw-w-52", "atw-w-56", "atw-w-60" );
    $height = array( "atw-h-16", "atw-h-20", "atw-h-24", "atw-h-28", "atw-h-32", "atw-h-36", "atw-h-40", "atw-h-44", "atw-h-48", "atw-h-52", "atw-h-56", "atw-h-60" );
    $fits = array( 
        "contain" => "atw-object-contain",
        "cover" => "atw-object-cover",
        "fill" => "atw-object-fill",
        "none" => "atw-object-none",
        "scale-down" => "atw-object-scale-down"
    );

    $classes[] = $widths[ $atts["list_img_width"] ];
    $classes[] = $height[ $atts["list_img_height"] ];

    $classes_img[] = $fits[ $atts["list_img_fit"] ];

    $image_type = $atts["list_img_source"];

    $result->image = $image;
    $result->image_id = $image_id;
    $result->image_type = $image_type;
    $result->classes = $classes;
    $result->classes_img = $classes_img;
    $result->default_image_url = $default_image_url;

    return $result;
}

function wpadverts_block_list_image_grid( $post_id, $atts ) {
    $result = new stdClass;

    $image_id = adverts_get_main_image_id( $post_id );
    $image_type = "adverts-list";
    $image = false;

    $classes = array();
    $classes_img = array();

    $default_image_url = null;

    if( isset( $atts["default_image_url"] ) && ! empty( $atts["default_image_url"] ) ) {
        $default_image_url = $atts["default_image_url"];
    }

    if( $image_id ) {
        $image = get_post( $image_id );
    }

    $widths = wpadverts_block_img_options( "width" );
    $height = wpadverts_block_img_options( "height" );    
    $fits = wpadverts_block_img_options( "fit" );

    $classes[] = $height[ $atts["grid_img_height"] ];

    $classes_img[] = $fits[ $atts["grid_img_fit"] ];

    $image_type = $atts["grid_img_source"];

    
    $result->image = $image;
    $result->image_id = $image_id;
    $result->image_type = $image_type;
    $result->classes = $classes;
    $result->classes_img = $classes_img;
    $result->default_image_url = $default_image_url;

    return $result;
}

function wpadverts_block_flash( $data, $layout = "normal" ) {

    $data = apply_filters( "adverts_flash_data", $data );
    
    $types = array(
        "error" => array(
            "icon" => "fas fa-exclamation-circle"
        ),
        "success" => array(
            "icon" => "fas fa-check"
        ),
        "info" => array(
            "icon" => "fas fa-info-circle"
        )
    );

    $styles = array(
        "error" => "wpa-style-error",
        "success" => "wpa-style-success",
        "info" => "wpa-style-info"
    );

    $layouts = array(
        "normal" => "wpa-layout-normal",
        "big" => "wpa-layout-big"
    );

    $l = $layouts[$layout];

    ob_start();
    ?>

    <?php foreach(array_keys($data) as $key): ?>
        <?php if(isset($data[$key]) && is_array($data[$key]) && !empty($data[$key])): ?>
            <?php $t = $types[$key]; ?>
            <?php $s = $styles[$key]; ?>
            <div class="wpadverts-flash <?php echo esc_attr("$l $s") ?>">
            <?php foreach( $data[$key] as $key => $info): ?>
                
                <?php 
                    if(is_string($info)) {
                        $info = array( "message" => $info, "icon" => "", "link" => null);
                    } 
                    if( $info["icon"] && stripos( $info["icon"], "adverts-" ) === 0 ) {
                        $info["icon"] = $t["icon"];
                    }
                ?>
                
                <div class="wpa-flash-content atw-flex">

                    <?php if($info["icon"]): ?>
                        <span class="wpa-flash-icon"><i class="<?php echo esc_attr( $info["icon"] ) ?>"></i></span>
                    <?php endif; ?>

                    <div class="atw-flex-1 atw-flex atw-flex-col">
                        <div class="atw-flex atw-flex-col">
                            <span class="wpa-flash-message atw-flex-1"><?php echo $info["message"] ?></span>
                            <?php if( isset( $info["link"] ) && is_array( $info["link"] ) ): ?>
                                <div class="wpa-flash-link-wrap">
                                <?php foreach( $info["link"] as $link ): ?>
                                    <a href="<?php echo esc_attr( $link["url"] ) ?>" class="wpa-flash-link atw-flex-none"><?php echo $link["title"] ?></a>
                                <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php

    return ob_get_clean();
}

function wpadverts_block_modal() {
    include ADVERTS_PATH . '/templates/block-partials/modal.php';
}

/**
 * Returns registered block templates
 * 
 * @since   2.0
 * @return  array   An array of registered templates
 */
function wpadverts_get_block_templates() {
    $template_options = array(
        "draft" => array(),
        "template" => array()
    );

    $templates_draft = get_posts( array(
        "post_type" => "page",
        "post_status" => "draft"
    )); 
    
    foreach( $templates_draft as $tpl ) {
        $title = $tpl->post_title;
        if( empty( $title ) ) {
            $title = __( "~ no title ~", "wpadverts" );
        }
        $template_options["draft"][] = array(
            "value" => $tpl->ID,
            "text" => sprintf( "%s (ID: %d)", $title, $tpl->ID ),
            "depth" => 1
        );
    }

    $templates_wp = get_posts( array(
        "post_type" => "wp_template",
        "post_status" => "publish"
    )); 

    foreach( $templates_wp as $tpl ) {
        $title = $tpl->post_title;
        if( empty( $title ) ) {
            $title = __( "~ no title ~", "wpadverts" );
        }
        $template_options["template"][] = array(
            "value" => $tpl->ID,
            "text" => sprintf( "%s (ID: %d)", $title, $tpl->ID ),
            "depth" => 1
        );
    }

    return $template_options;
}

/**
 * Returns template options formatted for adverts_field_select
 * 
 * @since   2.0
 * @return  array   An array of registered templates options
 */
function wpadverts_get_block_templates_options() {

    $all_templates = wpadverts_get_block_templates();

    $template_options = array(
        array( 
            "value" => "block", 
            "text" => __( "Default Block Template", "wpadverts" ) 
        )
    );

    $templates_draft = $all_templates["draft"]; 

    if( ! empty( $templates_draft ) ) {
        $template_options[] = array( 
            "value" => "-1", 
            "text" => __( "Draft Templates", "wpadverts" ), 
            "depth" => 0,
            "disabled" => 1
        );

        $template_options = array_merge( $template_options, $templates_draft );
    }

    $templates_wp = $all_templates["template"]; 

    if( ! empty( $templates_wp ) ) {
        $template_options[] = array( 
            "value" => "-2", 
            "text" => 
            __( "WP Templates", "wpadverts" ), 
            "depth" => 0,
            "disabled" => 1
        );

        $template_options = array_merge( $template_options, $templates_wp );
    }


    return $template_options;
}

function wpadverts_block_tpl_field_type( $field ) {

    $classes = array();

    $types = array(
        "adverts_field_label" => "label",
        "adverts_field_text" => "text",
        "adverts_field_select" => "select",
        "adverts_field_textarea" => "textarea",
    );

    $has_margin = apply_filters( "wpadverts/block/field/with-margin", array(
        "adverts_field_label", "adverts_field_checkbox",  "adverts_field_radio"
    ), $field );

    if( in_array( $field["type"], $has_margin ) ) {
        $classes[] = "wpa-with-margin";
    }

    if( isset( $types[ $field["type"] ] ) ) {
        $type = $types[ $field["type"] ];
    } else {
        $type = "other";
    }

    $classes[] = sprintf( "wpadverts-input--%s", $type );

    return implode( " ", $classes );
}

/**
 * @since 2.0.1
 */
function wpadverts_load_assets_globally() {
    return apply_filters( "wpadverts/blocks/load-assets-globally", is_admin() );
}

function __wpadverts_load_tw_classes() {
    array(
        "atw-float-left", "atw-float-center", "atw-float-right"
    );
}