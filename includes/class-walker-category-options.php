<?php

/**
 * Create Array dropdown list of Categories.
 *
 * @package Adverts
 * @since 1.0
 * @uses Walker
 */
class Adverts_Walker_Category_Options extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	public $tree_type = 'category';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this
	 * @var array
	 */
	public $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category Category data object.
	 * @param int    $depth    Depth of category. Used for padding.
	 * @param array  $args     Uses 'selected', 'show_count', and 'value_field' keys, if they exist.
	 *                         See {@see wp_dropdown_categories()}.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {

		/** This filter is documented in wp-includes/category-template.php */
		$cat_name = apply_filters( 'list_cats', $category->name, $category );

		if ( ! isset( $args['value_field'] ) || ! isset( $category->{$args['value_field']} ) ) {
			$args['value_field'] = 'term_id';
		}

                if( !is_array( $output ) ) {
                    $output = array();
                }
                
                $output[] = array(
                    "value" => esc_attr( $category->{$args['value_field']} ),
                    "text" => $cat_name,
                    "depth" => $depth
                );
                
                return $output;
	}
}
