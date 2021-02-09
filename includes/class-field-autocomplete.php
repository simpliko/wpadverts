<?php

class Adverts_Field_Autocomplete {
    
    public function __construct() {
        adverts_form_add_field("adverts_field_autocomplete", array(
            "renderer" => array( $this, "renderer" ),
            "callback_save" => "adverts_save_multi",
            "callback_bind" => "adverts_bind_multi",
        ));

        add_action( "wp_ajax_wpadverts-taxonomy", array( $this, "ajax_taxonomy" ) );
        add_action( "wp_ajax_nopriv_wpadverts-taxonomy", array( $this, "ajax_taxonomy" ) );
    }

    public function renderer( $field ) {
        wp_enqueue_script( 'adverts-autocomplete' );
        wp_enqueue_style( 'wpadverts-autocomplete' );
        
        wp_enqueue_style('adverts-icons-animate');
        
        $class = "";

        if( isset( $field["class"] ) ) {
            $class = $field["class"];
        }

        $field["class"] = trim( $class . " " . "wpadverts-autocomplete" );

        if( !isset( $field["attr"] ) ) {
            $field["attr"] = array();
        }

        $ids = adverts_request( $field["name"] );
        if( empty( $ids ) ) {
            $ids = $field["value"];
        }
        if(! is_array( $ids ) ) {
            $ids = explode(",", $ids);
        }

        if( ! isset( $field["autocomplete"] ) || ! is_array( $field["autocomplete"] ) ) {
            $ac_args = array();
        } else {
            $ac_args = $field["autocomplete"];
        }
        
        $ac = shortcode_atts( array(
            "ajax_action" => "wpadverts-taxonomy",
            "taxonomy" => "advert_category",
            "multi" => "true",
            "hierarchical" => "true",
            "max_choices" => "100",
            "save_as" => "array",
            "separator" => ",",
            "leaf_only" => "false",
            "ignore_restricted" => "false",
            "checked" => json_encode( array() )
        ), $ac_args );
        
        $checked = array();
        
        if( $ac["taxonomy"] ) {
            $checked = $this->get_checked_ids( $ids, $ac["taxonomy"] );
            $ac["checked"] = json_encode( $checked );
        } 

        foreach( $ac as $k => $v ) {
            $field["attr"]["data-$k"] = $v;
        }

        $field["value"] = "";

        adverts_field_text( $field );
        
        foreach( $checked as $ck ) {
            adverts_field_hidden( array(
                "name" => $field["name"] . "[]",
                "id" => "wpadverts-ac-tmp--" . $field["name"] . "--" . $ck["value"],
                "class" => "wpadverts-ac-tmp--" . $field["name"] . "--" . $ck["value"],
                "value" => $ck["value"]
            ) );
        }
    } 

    public function get_checked_ids( $ids, $taxonomy ) {
        $response = array();
        
        if( ( is_string( $ids ) && $ids == "" ) || empty($ids) ) {
            return array();
        }
        
        if(!is_array($ids)) {
            $ids = array($ids);
        }

        foreach( $ids as $id ) {
            $term = get_term($id);
            
            if( ! $term instanceof WP_Term || $term->taxonomy !== $taxonomy ) {
                continue;
            }
            
            $children = get_term_children( $term->term_id, $taxonomy );
            
            if( ! empty( $children ) ) {
                $has_children = true;
            } else {
                $has_children = false;
            }
            
            $html = '<div class="wpadverts-ac-label">%s</div>';
            $html = apply_filters( "wpadverts_ac_row_html", sprintf( $html, $term->name ), $term );
            
            $path = array();
            
            foreach(advert_term_path( $term, $taxonomy ) as $k => $v) {
                $path[] = array("v"=>$k,"t"=>$v);
            }
            
            $response[] = apply_filters( "wpadverts_ac_row", array(
                "html" => "",
                "value" => $term->term_id,
                "label" => $term->name,
                "path" => $path,
                "has_children" => $has_children,
                "is_restricted" => get_term_meta( $term->term_id, "wpadvert_is_restricted", true )
            ), $term );
        }
        
        return $response;
    }

    public function ajax_taxonomy() {
        $taxonomy = adverts_request( "taxonomy" );
        $parent = adverts_request( "parent" );
        $search = trim( adverts_request( "text" ) );

        $response = array();
        
        $terms_args = array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        );
        
        if( strlen( trim( $parent ) ) ) {
            $terms_args["parent"] = absint( $parent );
        } else {
            $terms_args["number"] = 10;
        }
        
        if( strlen( $search ) > 0 ) {
            $terms_args["search"] = $search;
            $terms_args["order"] = "name";
        }
        
        if( isset( $_REQUEST["text"] ) && $search == "" ) {
            $terms_args["order"] = "count";
            $terms_args["orderby"] = "desc";
        }
        
        $terms = get_terms( apply_filters( "wpadverts_ac_get_terms_args", $terms_args ) );
        
        $response = array();
        
        foreach( $terms as $term ) {
            $children = get_term_children( $term->term_id, $taxonomy );
            
            if( ! empty( $children ) ) {
                $has_children = true;
            } else {
                $has_children = false;
            }
   
            $html = '<div class="wpadverts-mal-ac-label">%s</div>';
            $html = apply_filters( "wpadverts_ac_row_html", sprintf( $html, $term->name ), $term );
            
            $path = array();
            foreach(advert_term_path( $term, $taxonomy ) as $k => $v) {
                $path[] = array("v"=>$k,"t"=>$v);
            }
            
            $response[] = apply_filters( "wpadverts_ac_row", array(
                "html" => $html,
                "value" => $term->term_id,
                "label" => $term->name,
                "path" => $path,
                "has_children" => $has_children,
                "is_restricted" => get_term_meta( $term->term_id, "wpadvert_is_restricted", true )
            ), $term );
        }
        
        echo json_encode( $response );
        exit;
    }
}