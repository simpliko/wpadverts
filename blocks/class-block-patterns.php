<?php

class Adverts_Block_Patterns {

    public function register_categories() {
        register_block_pattern_category(
            'classifieds',
            array( 'label' => __( 'Classifieds', 'wpadverts' ) )
        );
    }

    public function register_patterns() {
        register_block_pattern(
            'wpadverts/classifieds-with-sidebar',
            array(
                'title'         => __( 'Classifieds with a sidebar', 'wpadverts' ),
                'description'   => __( 'Classifieds list with a search form in the left sidebar. Best to use with full-width page template.', 'wpadverts' ),
                'content'       => $this->get_classifieds_with_sidebar(),
                'categories'    => array( 'classifieds' ),
                'keywords'      => array( ),
                'viewportWidth' => 800,
            )
        );

        register_block_pattern(
            'wpadverts/classifieds-details',
            array(
                'title'         => __( 'Classifieds details', 'wpadverts' ),
                'description'   => __( 'Default classifieds details page', 'wpadverts' ),
                'content'       => $this->get_classifieds_details(),
                'categories'    => array( 'classifieds' ),
                'keywords'      => array( ),
                'viewportWidth' => 800,
            )
        );
    }

    public function get_classifieds_with_sidebar( $post_type = "advert", $form_scheme = "" ) {
        $html = array();
        $html[] = '<!-- wp:columns -->';
        $html[] = '<div class="wp-block-columns"><!-- wp:column {"width":"25%","className":"wpadverts-sticky-sidebar"} -->';
        $html[] = sprintf( '<div class="wp-block-column wpadverts-sticky-sidebar" style="flex-basis:25%%"><!-- wp:wpadverts/search {"post_type":"%s",form_scheme:"%s","buttons_pos":"atw-flex-col"} /--></div>', $post_type, $form_scheme );
        $html[] = '<!-- /wp:column -->';
        $html[] = '';
        $html[] = '<!-- wp:column {"width":"75%"} -->';
        $html[] = sprintf( '<div class="wp-block-column" style="flex-basis:75%%"><!-- wp:wpadverts/list {"post_type":"%s","form_scheme":"%s"} /--></div>', $post_type, $form_scheme );
        $html[] = '<!-- /wp:column --></div>';
        $html[] = '<!-- /wp:columns -->';

        return implode( "\r\n", $html );
    }

    public function get_classifieds_details( $post_type = "advert", $form_scheme = "" ) {
        $v = apply_filters( "wpadverts/block-patterns/classifieds-details/version", "v2" );

        if( $v == "v2" ) {
            return $this->get_classifieds_details_v2( $post_type, $form_scheme );
        } else {
            return $this->get_classifieds_details_v1( $post_type );
        }
    }

    public function get_classifieds_details_v1( $post_type = "advert" ) {
        return sprintf( '<!-- wp:wpadverts/details {"post_type":"%s"} /-->', $post_type );
    }

    public function get_classifieds_details_v2( $post_type = "advert", $form_scheme = "" ) {
        $html = array();
        $html[] = '<!-- wp:wpadverts/single-notification /-->';
        $html[] = '';
        $html[] = sprintf('<!-- wp:wpadverts/single-gallery {"post_type":"%s"} /-->', $post_type );
        $html[] = '';
        $html[] = '<!-- wp:columns -->';
        $html[] = '<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":"66.66%"} -->';
        $html[] = sprintf( '<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:66.66%%"><!-- wp:wpadverts/single-author {"post_type":"%s","avatar_size":"64","avatar_radius":"atw-rounded-full","data_secondary":["published"]} /--></div>', $post_type );
        $html[] = '<!-- /wp:column -->';
        $html[] = '';
        $html[] = '<!-- wp:column {"verticalAlignment":"center","width":"33.33%","className":"atw-text-left"} -->';
        $html[] = '<div class="wp-block-column is-vertically-aligned-center atw-text-left" style="flex-basis:33.33%"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->';
        $html[] = sprintf('<div class="wp-block-group"><!-- wp:wpadverts/single-value {"post_type":"%s","data":["pattern__price"],"render_as":"html","text_size":"atw-text-4xl","type":"atw-inline-block","color":"#ffffff","color_bg":"#ab0000","margin_y":"atw-my-1.5","padding_x":"atw-px-6","padding_y":"atw-py-1.5","border_radius":"atw-rounded-lg","p_value":"$2,500"} /--></div>', $post_type);
        $html[] = '<!-- /wp:group --></div>';
        $html[] = '<!-- /wp:column --></div>';
        $html[] = '<!-- /wp:columns -->';
        $html[] = '';
        $html[] = sprintf('<!-- wp:wpadverts/single-data-table {"post_type":"%s","closed_top":true,"include_fields":[],"exclude_types":[{"name":"__builtin"},{"name":"adverts_field_textarea"}]} /-->', $post_type);
        $html[] = '';
        $html[] = sprintf('<!-- wp:wpadverts/single-data-table {"post_type":"%s","layout":"text","include_types":[{"name":"adverts_field_textarea"}]} /-->', $post_type );
        $html[] = '';
        $html[] = sprintf('<!-- wp:wpadverts/single-contact {"post_type":"%s","contact":[]} /-->', $post_type );
        
        return implode( "\r\n", $html );
    }

}

?>