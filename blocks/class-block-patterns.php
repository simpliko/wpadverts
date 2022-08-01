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

}

?>