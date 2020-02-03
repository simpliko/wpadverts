<?php
/**
 * Generates and validates checksums.
 * 
 * This class generates a checksum based on (array) $args and saves it in the database.
 * 
 * Later the user can try to validate the checksum and restore params. This class is
 * being used in [adverts_add], [adverts_manage] and wp-admin to make sure
 * the user can execute an action.
 *
 * @package Adverts
 * @subpackage Classes
 * @since 1.4.0
 * @access public
 */

class Adverts_Checksum {

    /**
     * Generates a checksum based on $args
     * 
     * The generated checksum and paramas are saved in wp_options table
     * with key "wpadverts_checksums".
     * 
     * @since   1.4.0
     * @param   array   $args   Params used to generate checksum
     * @return  array           Checksum and nonce (array["checksum"=>"", "nonce"=>""])
     */
    public function get_integrity_keys( $args ) {

        $params = apply_filters( "wpadverts_checksum_params", $args );
        
        $checksum = md5( serialize( $params ) );
        $checksum_name = apply_filters( 'adverts_checksum_name', sprintf( 'wpadverts-cksum-%s', $checksum ), $checksum );
        
        $nonce = wp_create_nonce( $checksum_name );
        
        $checksums = get_option( 'wpadverts_checksums' );
        if( ! is_array( $checksums ) ) {
            $checksums = array();
        }
        if( ! isset( $checksums[ $checksum ] ) ) {
            $checksums[$checksum] = array( 't' => current_time( 'timestamp', 1 ), 'c' => $params );
            update_option( 'wpadverts_checksums', $checksums, false );
        }
        
        return array( "checksum" => $checksum, "nonce" => $nonce );
    }
    
    /**
     * Returns checksum args or negative int if checksum could not be validated.
     * 
     * Function tries to get checksum args based on $_REQUEST['_wpadverts_checksum']
     * and $_REQUEST['_wpadverts_checksum_nonce'].
     * 
     * @uses wp_verify_nonce() function to check if checksum is valid
     * 
     * @since  1.4.0
     * @return array    Checksum args
     */
    public function get_args_from_checksum() {
        
        $nonce = adverts_request( '_wpadverts_checksum_nonce' );
        $checksum = adverts_request( '_wpadverts_checksum' ); 
        
        $checksum_name = apply_filters( 'adverts_checksum_name', sprintf( 'wpadverts-cksum-%s', $checksum ), $checksum );
        
        if( ! wp_verify_nonce( $nonce, $checksum_name ) ) {
            return -1;
        }
        
        $checksums = get_option( 'wpadverts_checksums' );
        if( ! isset( $checksums[ $checksum ] ) ) {
            return -2;
            
        }
        
        return $checksums[ $checksum ]["c"];
    }
}
