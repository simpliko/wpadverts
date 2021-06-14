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
            "class" => "atw-flex-1 atw-inline-block atw-py-0 atw-px-2 atw-text-sm atw-text-gray-700 atw-align-baseline atw-truncate atw-cursor-pointer"
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
            "class" => "atw-flex atw-flex-row atw-flex-grow atw-align-baseline",
        ), $padding . $checkbox->render() . $label->render() );
        
        $opts .= $wrap->render();
        
        $i++;
    }
    
    //$field["rows"] = 3;

    $wrap_classes = array();
    if( absint( $field["rows"] ) >= 1 ) {
        $wrap_classes[] = sprintf( "atw-grid atw-grid-cols-%d atw-gap-3", absint( $field["rows"] ) );
    } else {
        $wrap_classes[] = "atw-flex atw-flex-wrap atw-flex-row atw-content-evenly";
    }
    
    echo Adverts_Html::build("div", array("class"=> join( " ", $wrap_classes ) ), $opts);
}

function adverts_field_radio_block( $field, $form = null ) {
    adverts_field_checkbox_block($field, $form, "radio");
}
