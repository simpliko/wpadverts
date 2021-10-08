<?php

class Adverts_Rest_Blocks {

    public function __construct() {
        register_rest_route('wpadverts/v1', '/classifieds-types', [
            'method' => 'GET',
            'callback' => array( $this, "classifieds_types" ),
            'permission_callback' => array( $this, "classifieds_types_perms" )
        ]);
    }

    public function classifieds_types() {

        $data = array();
        $post_types = wpadverts_get_post_types();
        foreach( $post_types as $post_type ) {
            //print_r($post_type);
            $post_type_object = get_post_type_object( $post_type );
            
            $post_type_object->taxonomies; // !!!

            $item = array(
                "post_type" => $post_type,
                "label" => $post_type_object->label,
                "taxonomies" => array( 
                    0 => array(
                        "name" => "advert_category",
                        "label" => "Advert Category"
                    ),                    
                    1 => array(
                        "name" => "advert_location",
                        "label" => "Advert Location"
                    ),
                ),
                "form_schemes_default" => $this->get_form_schemes_default( $post_type ), 
                "form_schemes" => $this->get_form_schemes( $post_type )
            );

            $data[] = $item;
        }

        $form_schemes = array(
            "status" => "200",
            "data" => $data
        );
        //echo print_r($form_schemes);
        return rest_ensure_response( $form_schemes );
    }

    public function classifieds_types_perms() {
        return true;
        return current_user_can( 'edit_pages' );
    }

    public function get_form_schemes_default( $post_type ) {

        $form_scheme = array(
            "publish" => $this->_get_builtin_data( "wpad-form-add" ),
            "search" => $this->_get_builtin_data( "wpad-form-search" ),
            "contact" => $this->_get_builtin_data( "wpad-form-contact" )
        );

        return $form_scheme;
    }

    public function get_form_schemes( $post_type ) {

        $form_scheme = array(
            "publish" => array(),
            "search" => array(),
            "contact" => array()
            
        );

        $keys = array(
            "wpad-form-add" => "publish", 
            "wpad-form-search" => "search", 
            "wpad-form-contact" => "contact"
        );

        $loop = new WP_Query( array( 
            'post_type' => 'wpadverts-form',
            'post_status' => array( "wpad-form-add", "wpad-form-search" , "wpad-form-contact" ),
            'posts_per_page' => -1,
            'orderby' => array( 'title' => 'ASC' )
        ) );

        foreach( $loop->posts as $post ) {
            if( ! isset( $keys[ $post->post_status ] ) )  {
                continue;
            }

            $key = $keys[ $post->post_status ];
            $form_scheme[ $key ][] = array( 
                "label" => $post->post_title, 
                "value" => $post->post_name,
                "data" => $this->get_form_scheme_data( $post, $post_type )
            );
        }

        return $form_scheme;
    }

    public function get_form_scheme_data( $post, $ad_type ) {

        include_once ADVERTS_PATH . "./../wpadverts-custom-fields/includes/functions.php";

        //$meta_common = $this->_get_builtin_data( $post );
        $meta_unique = wpadverts_custom_fields_get_unique_metas( $post->post_status );

        //echo "<pre>";
        //echo ($post->post_title . " " . $post->post_status . " " . $post->post_type . " " . $ad_type);
        //echo "</pre>\r\n";
        //print_r($meta_common);
        //print_r($meta_unique);

        $meta_modified = array();

        foreach( $meta_unique as $key => $data ) {
            $meta_modified[$key] = array(
                "name" => "meta__" . $data["name"],
                "label" => $data["label"]
            );
        }

        return $meta_modified;
    }

    protected function _get_builtin_data( $form_type ) {

        $arr_add = array(
            array(
                "name" => "default__ID",
                "label" => __( "ID", "wpadverts" )
            ),            
            array(
                "name" => "default__post_title",
                "label" => __( "Title", "wpadverts" )
            ),
            array(
                "name" => "default__post_excerpt",
                "label" => __( "Excerpt", "wpadverts" )
            ),            
            array(
                "name" => "default__post_content",
                "label" => __( "Content", "wpadverts" )
            ),
            array(
                "name" => "date__post_date",
                "label" => __( "Post Date", "wpadverts" )
            ),            
            array(
                "name" => "date__post_date_gmt",
                "label" => __( "Post Date (GMT)", "wpadverts" )
            ),            
            array(
                "name" => "date__post_modified",
                "label" => __( "Modified Date", "wpadverts" )
            ),            
            array(
                "name" => "date__post_modified_gmt",
                "label" => __( "Modified Date (GMT)", "wpadverts" )
            ),
            array( 
                "name" => "meta__adverts_person", 
                "label" => __( "Contact Person", "wpadverts" ) 
            ),
            array( 
                "name" => "meta__adverts_email", 
                "label" => __( "Contact Email", "wpadverts" ) 
            ),
            array( 
                "name" => "meta__adverts_phone", 
                "label" => __( "Contact Phone", "wpadverts" ) 
            ),
            array( 
                "name" => "meta__adverts_price", 
                "label" => __( "Price", "wpadverts" ) 
            ),
            array( 
                "name" => "meta__adverts_location", 
                "label" => __( "Location", "wpadverts" ) 
            ),
            array( 
                "name" => "pattern__location", 
                "label" => __( "Location (Formatted)", "wpadverts" ) 
            ),            
            array( 
                "name" => "pattern__price", 
                "label" => __( "Price (Formatted)", "wpadverts" ) 
            ),            
            array( 
                "name" => "pattern__post_date", 
                "label" => __( "Post date (Formatted)", "wpadverts" ) 
            ),
        );

        $arr_contact = array(

        );
        
        $arr_search = array(

        );

        $arr = apply_filters( "wpadverts/rest/classifieds/builtin-meta", array(
            "wpad-form-add" => $arr_add,
            "wpad-form-contact" => $arr_contact,
            "wpad-form-search" => $arr_search
        ), $form_type );

        return $arr[ $form_type ];
    }

    protected function _get_formatted_data( $form_type ) {

        $arr_add = array(
            array( 
                "name" => "pattern__location", 
                "label" => __( "Location", "wpadverts" ) 
            ),            
            array( 
                "name" => "pattern__price", 
                "label" => __( "Price", "wpadverts" ) 
            ),
        );

        $arr_contact = array(

        );
        
        $arr_search = array(

        );

        $arr = apply_filters( "wpadverts/rest/classifieds/formatted", array(
            "wpad-form-add" => $arr_add,
            "wpad-form-contact" => $arr_contact,
            "wpad-form-search" => $arr_search
        ), $form_type );

        return $arr[ $form_type ];
    }
}