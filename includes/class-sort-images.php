<?php

/**
 * This class is being used mainly for sorting images by adverts_sort_images()
 * function
 * 
 * @see adverts_sort_images()
 *
 * @package Adverts
 * @subpackage Classes
 * @since 1.1.2
 * @access public
 * 
 */

class Adverts_Sort_Images {
    
    /**
     * Array containing images order list
     *
     * @var array
     */
    protected $_images_order = null;
    
    /**
     * Constructor creates a new object
     * 
     * @since 1.1.2
     * @param array $images_order
     */
    public function __construct( $images_order ) {
        $this->_images_order = $images_order;
    }
    
    /**
     * Sorting function
     * 
     * Sort the $images array by keys (i.e. post IDs) by comparing to $images_order.
     * 
     * Solution courtesy: Nico & SquareCat: http://stackoverflow.com/a/22290151
     * 
     * @since 1.1.2
     * @param int $img_a    Image A order
     * @param int $img_b    Image B order
     * @return int
     */
    public function sort( $img_a, $img_b ) {
        if (in_array($img_a, $this->_images_order)) {
            if (in_array($img_b, $this->_images_order)) {
                return array_search($img_a, $this->_images_order) - array_search($img_b, $this->_images_order);
            } else {
                return -1;
            }
        } else if (in_array($img_b, $this->_images_order)) {
            return 1;
        } else {
            return 0;
        }
    }

}