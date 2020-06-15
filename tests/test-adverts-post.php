<?php
/**
 * Class SampleTest
 *
 * @package Wpadverts
 */

/**
 * Sample test case.
 */
class Adverts_Post_Test extends WP_UnitTestCase {

    private $_advert_id = null;
    
    private $_category_terms = [];
    
    public function setUp() {
        parent::setUp();
        
        $t1 = wp_insert_term( "one", "advert_category" );
        $t2 = wp_insert_term( "two", "advert_category" );
        
        $this->_category_terms = [ $t1["term_id"], $t2["term_id"] ];
                
    }
    
    public function get_form() {
        include_once ADVERTS_PATH . 'includes/class-form.php';
        

        $bind = array(
            "adverts_person" => "John Doe",
            "adverts_email" => "john.doe@example.com",
            "adverts_phone" => "0100100",
            "post_title" => "Test Ad",
            "advert_category" => $this->_category_terms,
            "post_content" => "Lorem ipsum ...",
            "adverts_location" => "New York, NY",
            "adverts_price" => 199
        );
        
        $form = new Adverts_Form( Adverts::instance()->get( "form" ) );
        $form->bind( $bind );
        
        return $form;
    }
    
    public function get_form_custom_tax( $tax_name, $tax_ids, $tax_save = null ) {
        
        include_once ADVERTS_PATH . 'includes/class-form.php';
        
        $form_scheme = Adverts::instance()->get( "form" );
        $form_scheme["field"][] = array(
            "name" => $tax_name,
            "type" => "adverts_field_text",
            "order" => 5,
            "label" => $tax_name,
            "save" => $tax_save
        );

        $bind = array(
            "adverts_person" => "John Doe",
            "adverts_email" => "john.doe@example.com",
            "adverts_phone" => "0100100",
            "post_title" => "Test Ad",
            "post_content" => "Lorem ipsum ...",
            "adverts_location" => "New York, NY",
            "adverts_price" => 199,
            $tax_name => $tax_ids
        );
        
        $form = new Adverts_Form( $form_scheme );
        $form->bind( $bind );
        
        return $form;
    }
    
    public function get_post_init() {
        return array(
            "post" => array(
                "ID" => null,
                "post_name" => "lorem-ipsum",
                "post_type" => "advert",
                "post_author" => 0,
                "post_date" => current_time( 'mysql' ),
                "post_date_gmt" => current_time( 'mysql', 1 ),
                "post_status" => "publish",
                "guid" => ""
            ),
            "meta" => array()
        );
    }
    
    public function get_post_term_ids( $advert_id, $taxonomy_name ) {
        $terms = wp_get_post_terms( $advert_id, $taxonomy_name );
        $term_ids = [];

        foreach( $terms as $term ) {
            $term_ids[] = $term->term_id;
        }
        
        return $term_ids;
    }
    
    /**
     * A single example test.
     */
    public function test_insert() {
        // Replace this with some actual testing code.
        
        $form = $this->get_form();
        $this->_advert_id = Adverts_Post::save( $form, 0, $this->get_post_init() );
        $this->assertGreaterThan(0, $this->_advert_id);
    }
    
    public function test_insert_taxonomy() {
        
        $form = $this->get_form();
        $this->_advert_id = Adverts_Post::save( $form, 0, $this->get_post_init() );
        
        $term_ids = $this->get_post_term_ids( $this->_advert_id, "advert_category" );
        
        $this->assertEquals($this->_category_terms, $term_ids );
    }
    
    public function _test_update() {
        
    }
    
    public function test_custom_taxonomy() {
        register_taxonomy( "advert_tax", "advert" );
        
        $t1 = wp_insert_term( "term_1", "advert_tax" );
        $t2 = wp_insert_term( "term_2", "advert_tax" );
        
        $t = [ $t1["term_id"], $t2["term_id"] ];
        
        $form = $this->get_form_custom_tax( "advert_tax", $t );
        
        $advert_id = Adverts_Post::save( $form, 0, $this->get_post_init() );
        $term_ids = $this->get_post_term_ids( $advert_id, "advert_tax" );
        
        $this->assertEquals($t, $term_ids );
        
    }
    
    public function test_custom_taxonomy_2() {
        register_taxonomy( "advert_tax", "advert" );
        
        $t1 = wp_insert_term( "term_1", "advert_tax" );
        $t2 = wp_insert_term( "term_2", "advert_tax" );
        
        $t = [ $t1["term_id"], $t2["term_id"] ];
        
        $form = $this->get_form_custom_tax( "adverts_tax", $t, array(
            "method" => "taxonomy",
            "taxonomy" => "advert_tax"
        ) );
        
        $advert_id = Adverts_Post::save( $form, 0, $this->get_post_init() );
        $term_ids = $this->get_post_term_ids( $advert_id, "advert_tax" );
        
        $this->assertEquals($t, $term_ids );
        
    }
}
