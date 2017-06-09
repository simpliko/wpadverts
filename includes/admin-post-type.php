<?php
/**
 * Admin Post Types
 * 
 * List of functions executed when 'adverts' post type is loaded in wp-admin
 *
 * @package     Adverts
 * @copyright   Copyright (c) 2015, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Save additional data for adverts custom post type
 * 
 * @uses Adverts_Form 
 * 
 * @param int $ID Post ID
 * @param WP_Post $post
 * @since 0.1
 * @return null
 */
function adverts_save_post($ID = false, $post = false) {
    
    global $pagenow;
    
    if ( !in_array($pagenow, array("post.php", "post-new.php") ) ) {
        return $ID;
    }
    
    $nonce = 'product_price_box_content_nonce';
    
    if ( !isset( $_POST[$nonce] ) || !wp_verify_nonce( $_POST[$nonce], basename( __FILE__ ) ) ) {
        //return $ID;
    }
    
    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $ID ) ) {
        return $ID;
    }
    
    if ( !$post->post_type == 'advert' ) {
        return $ID;
    }
    
    if ( defined( "DOING_AJAX" ) && DOING_AJAX ) {
        return $ID;
    }
 
    /* Save expiration date in DB */
    if( !empty( $_POST ) ) { 
        $current_exp_date = get_post_meta( $ID, "_expiration_date", true );
        $edit_date = empty( $current_exp_date );
    } else {
        $edit_date = false;
    }
    
    foreach ( array('aa', 'mm', 'jj', 'hh', 'mn') as $timeunit ) {
        if ( !empty( $_POST['adverts_hidden_' . $timeunit] ) && $_POST['adverts_hidden_' . $timeunit] != $_POST['adverts_' . $timeunit] ) {
            $edit_date = 1;
            break;
        }
    }

    if ( isset($_POST["never_expires"]) && $_POST["never_expires"]=="1") {
        // Advert does not expire, unset expiration date
        delete_post_meta( $ID, "_expiration_date" );
    } else if ( $edit_date ) {
        // Build expiration date, based on submitted $_POST data
        $aa = isset($_POST['adverts_aa']) ? $_POST['adverts_aa'] : null;
        $mm = isset($_POST['adverts_mm']) ? $_POST['adverts_mm'] : null;
        $jj = isset($_POST['adverts_jj']) ? $_POST['adverts_jj'] : null;
        $hh = isset($_POST['adverts_hh']) ? $_POST['adverts_hh'] : null;
        $mn = isset($_POST['adverts_mn']) ? $_POST['adverts_mn'] : null;
        $ss = isset($_POST['adverts_ss']) ? $_POST['adverts_ss'] : null;
        $aa = ($aa <= 0 ) ? date('Y') : $aa;
        $mm = ($mm <= 0 ) ? date('n') : $mm;
        $jj = ($jj > 31 ) ? 31 : $jj;
        $jj = ($jj <= 0 ) ? date('j') : $jj;
        $hh = ($hh > 23 ) ? $hh -24 : $hh;
        $mn = ($mn > 59 ) ? $mn -60 : $mn;
        $ss = ($ss > 59 ) ? $ss -60 : $ss;
        $exp_date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $aa, $mm, $jj, $hh, $mn, $ss );
        $valid_date = wp_checkdate( $mm, $jj, $aa, $exp_date );
        if ( !$valid_date ) {
            return new WP_Error( 'invalid_date', __( 'Whoops, the provided date is invalid.' ) );
        }
        //$exp_date_gmt = get_gmt_from_date( $exp_date );
        //
        // Save expiration date in DB
        update_post_meta( $ID, "_expiration_date", strtotime( $exp_date ) ); 
        
    }
    
    // Load form data
    $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get("form"), array() );

    $form = new Adverts_Form();
    $form->load( $form_scheme );
    $form->bind( stripslashes_deep( $_POST ) );
    
    $meta = array();
    
    // Find meta fields in the form
    foreach($form->get_fields() as $field) {
        
        $c1 = ! property_exists("WP_Post", $field["name"]);
        $c2 = ! taxonomy_exists($field["name"]);
        $c3 = isset( $field["value"] );
        
        if( $c1 && $c2 && $c3 ) {
            $meta[$field["name"]] = array("field"=>$field, "value"=>$field["value"]);
        }
    }  
    
    // Save meta data values
    $fields = Adverts::instance()->get("form_field");
    foreach($meta as $key => $data) {

        $field = $data["field"];
        $field_type = $field["type"];
        $value = $data["value"];

        $callback_save = $fields[$field_type]["callback_save"];

        if( is_callable( $callback_save ) ) {
            call_user_func( $callback_save, $ID, $key, $value );
        }
    }
    
    
}

/**
 * Makes sure that all required data is entered before publshing Advert.
 * 
 * This function is executed by 'save_post' filter.
 * 
 * @see save_post filter
 * 
 * @global wpdb $wpdb
 * @param int $ID
 * @param WP_Post $post
 * @since 0.1
 * @return type
 */
function adverts_save_post_validator( $ID, $post ) {
    
    global $pagenow;
    
    if ( !in_array($pagenow, array("post.php", "post-new.php") ) ) {
        return $ID;
    }
    
    // Don't do on autosave or when new posts are first created
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || $post->post_status == 'auto-draft' ) {
        return $ID;
    }
    
    // Abort if not Adverts custom type
    if ( $post->post_type != 'advert' ){
        return $ID;
    }

    // Load form data
    $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get("form"), array() );

    $form = new Adverts_Form();
    $form->load( $form_scheme );
    $form->bind( stripslashes_deep( $_POST ) );
    
    $valid = $form->validate();
    
    // on attempting to publish - check for completion and intervene if necessary
    if ( ( isset( $_POST['publish'] ) || isset( $_POST['save'] ) ) && $_POST['post_status'] == 'publish' ) {
        //  don't allow publishing while any of these are incomplete
        
        if ( !$valid ) {
            // filter the query URL to change the published message
            add_filter( 'redirect_post_location', create_function( '$location','return add_query_arg("message", "21", $location);' ) );
        } 
    }
}

/**
 * Register new error message
 * 
 * @see post_updated_messages filter
 * 
 * @param array $messages
 * @since 0.1
 * @return array
 */
function adverts_post_updated_messages( $messages ) {
    $messages["advert"][21] = __( "Post updated, but some required data is not filled properly.", "adverts" );

    return $messages;
}


/**
 * Render adverts post type inline scripts
 * 
 * @see admin_head action
 * 
 * @global string $post_type
 * @global WP_Post $post
 * @since 0.1
 * @return void
 */
function adverts_admin_head() {
    global $post_type, $post;

    // Make sure this is Adverts post type
    if (is_object($post) && ( ( isset($_GET['post_type']) && $_GET['post_type'] == 'advert') || ($post_type == 'advert') ) ):  
    ?>

    <script language="Javascript">
        var ADVERTS_POST_STATUS = '<?php echo esc_js($post->post_status) ?>';
    </script>
    <?php 
    
    endif; 
}

/**
 * Renders expiration date inputs
 * 
 * This function is beign used only by adverts_expiry_meta_box() function.
 * 
 * @see adverts_expiry_meta_box()
 * 
 * @global WP_Locale $wp_locale
 * @param string $post_date Date in Y-m-d H:i:s format
 * @param int $tab_index
 * @param boolean $never_expires
 */
function adverts_touch_time( $post_date, $tab_index = 0, $never_expires = false ) {
    global $wp_locale;
    
    $edit = true;

    $tab_index_attribute = '';
    if ( (int) $tab_index > 0 )
            $tab_index_attribute = " tabindex=\"$tab_index\"";

    $time_adj = current_time('timestamp');

    $jj = ($edit) ? mysql2date( 'd', $post_date, false ) : gmdate( 'd', $time_adj );
    $mm = ($edit) ? mysql2date( 'm', $post_date, false ) : gmdate( 'm', $time_adj );
    $aa = ($edit) ? mysql2date( 'Y', $post_date, false ) : gmdate( 'Y', $time_adj );
    $hh = ($edit) ? mysql2date( 'H', $post_date, false ) : gmdate( 'H', $time_adj );
    $mn = ($edit) ? mysql2date( 'i', $post_date, false ) : gmdate( 'i', $time_adj );
    $ss = ($edit) ? mysql2date( 's', $post_date, false ) : gmdate( 's', $time_adj );

    $cur_jj = gmdate( 'd', $time_adj );
    $cur_mm = gmdate( 'm', $time_adj );
    $cur_aa = gmdate( 'Y', $time_adj );
    $cur_hh = gmdate( 'H', $time_adj );
    $cur_mn = gmdate( 'i', $time_adj );

    $month = "<select id=\"adverts-mm\" name=\"adverts_mm\"$tab_index_attribute>\n";
    for ( $i = 1; $i < 13; $i = $i +1 ) {
            $monthnum = zeroise($i, 2);
            $month .= "\t\t\t" . '<option value="' . $monthnum . '" ' . selected( $monthnum, $mm, false ) . '>';
            /* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
            $month .= sprintf( __( '%1$s-%2$s' ), $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . "</option>\n";
    }
    $month .= '</select>';

    $day = '<input type="text" id="adverts-jj" name="adverts_jj" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
    $year = '<input type="text" id="adverts-aa" name="adverts_aa" value="' . $aa . '" size="4" maxlength="4"' . $tab_index_attribute . ' autocomplete="off" />';
    $hour = '<input type="text" id="adverts-hh" name="adverts_hh" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';
    $minute = '<input type="text" id="adverts-mn" name="adverts_mn" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" />';

    ?>
    <div class="timestamp-wrap">
        <div class="adverts-timestamp-wrap">
            <?php printf( __( '%1$s %2$s, %3$s @ %4$s : %5$s' ), $month, $day, $year, $hour, $minute ) ?>
            <div class="adverts-timestamp-disabler"></div>
        </div>
        <div>
            <input type="checkbox" name="never_expires" id="never_expires" value="1" <?php if($never_expires): ?>checked="checked"<?php endif; ?> />
            <label for="never_expires"><?php _e('Never Expires', 'adverts') ?></label>
        </div>
    </div>
    
    <input type="hidden" id="ss" name="ss" value="' . $ss . '" />

    <?php
    
    echo "\n\n";
    foreach ( array('mm', 'jj', 'aa', 'hh', 'mn') as $timeunit ) {
            echo '<input type="hidden" id="adverts_hidden_' . $timeunit . '" name="adverts_hidden_' . $timeunit . '" value="' . $$timeunit . '" />' . "\n";
            $cur_timeunit = 'cur_' . $timeunit;
            echo '<input type="hidden" id="adverts_' . $cur_timeunit . '" name="adverts_' . $cur_timeunit . '" value="' . $$cur_timeunit . '" />' . "\n";
    }
        
    ?>

    <p>
        <a href="#edit_timestamp" class="save-expiry-timestamp hide-if-no-js button"><?php _e('OK'); ?></a>
        <a href="#edit_timestamp" class="cancel-expiry-timestamp hide-if-no-js button-cancel"><?php _e('Cancel'); ?></a>
    </p>
<?php
}

/**
 * Adds Expiry date to 'Publish' meta box
 * 
 * @see post_submitbox_misc_actions action
 * 
 * @param WP_Post $post
 * @global string $pagenow
 */
function adverts_expiry_meta_box() {
    global $post, $pagenow;
    
    // Do this for adverts only.
    if($post->post_type != 'advert') {
        return;
    }
    
    $datef = __( 'M j, Y @ G:i' );
    $meta = get_post_meta( $post->ID, '_expiration_date', true);

    if ( $meta != '' ) {
        // Set expiration date based on _expiration_date meta
        $expiry = $meta;
        $date = date_i18n( $datef, $expiry );
        $touch_time = date( "Y-m-d H:i:s", $expiry );
        $never_expires = false;
    } else if( $pagenow != "post-new.php" ) {
        // _expiration_date meta not in DB, set expiration to 'never'
        $expiry = strtotime( current_time('mysql') . " +30 DAYS" );
        $date = __("Never Expires", "adverts");
        $touch_time = date( "Y-m-d H:i:s", $expiry );
        $never_expires = true;
    } else {
        // User is creating a new Advert, the expiration date is set to today+30 days.
        $duration = intval( adverts_config( "config.visibility" ) );
        $expiry = strtotime( current_time('mysql') . " +$duration DAYS" );
        $date = date_i18n( $datef, $expiry );
        $touch_time = date( "Y-m-d H:i:s", $expiry );
        $never_expires = false;
    }
    
    // Check if date is in the past or in the future and set correct label based on this.
    if( strtotime( date_i18n( "Y-m-d H:i:s" ) ) > $expiry ) {
        $stamp = __( "Expired: <b>%s</b>", "adverts");
    } else {
        $stamp = __( "Expires: <b>%s</b>", "adverts");
    }

    /* @todo: in a future select who can publish */
    $can_publish = true;
    
    // render expiration date section
    if ( $can_publish ):  ?>
    <div class="misc-pub-section curtime misc-pub-curtime">
        <span id="timestamp_expire">
        <?php printf($stamp, $date); ?></span>
        <a href="#edit_timestamp_expire" class="edit-timestamp hide-if-no-js"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit date and time' ); ?></span></a>
        <div id="timestamp-expire-div" class="hide-if-js"><?php adverts_touch_time( $touch_time, 2, $never_expires); ?></div>
    </div><?php // /misc-pub-section ?>
    <?php endif; 
}

/**
 * Adds meta box with additional advert information
 * 
 * @uses Adverts
 * @uses Adverts_Form
 * @see add_meta_box()
 * 
 * @param WP_Post $post
 * @since 0.1
 * @return void
 */
function adverts_data_box_content( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'product_price_box_content_nonce' );

    $exclude = apply_filters( "adverts_data_box_content_exclude", array("_adverts_account", "advert_category", "post_title", "gallery", "post_content") );

    // Load form data
    $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get("form"), array() );

    $form = new Adverts_Form();
    $form->load( $form_scheme );

    // Get list of fields from post meta table
    $bind = array();
    foreach( $form->get_fields( array( "exclude"=>$exclude ) ) as $f ) {
        $bind[$f["name"]] = get_post_meta( $post->ID, $f["name"], true );
    }
  
    foreach( $form->get_fields( array( "exclude"=>$exclude ) ) as $f ) {
        $value = get_post_meta( $post->ID, $f["name"], false );
        if( empty( $value ) ) {
            $bind[$f["name"]] = "";
        } else if( count( $value) == 1 ) {
            $bind[$f["name"]] = $value[0];
        } else {
            $bind[$f["name"]] = $value;
        }
    }
  
    // Bind data
    $form->bind( $bind );
  
    // Validate if message 21 will be displayed, that is if form already failed 
    // validation in adverts_save_post_validator() function
    if( isset($_GET['message']) && $_GET['message'] == 21 ) {
        $form->validate();
    }

  ?>

    <table class="form-table adverts-data-table">
	<tbody>
        <?php foreach($form->get_fields( array( "exclude"=>$exclude ) ) as $field): ?>
            <tr class="<?php if(adverts_field_has_errors($field)): ?>adverts-field-error<?php endif; ?>">
            <?php if($field["type"] == "adverts_field_header"): ?>
                <th scope="row" colspan="2" class="adverts-data-header">
                    <span class="adverts-data-header-title"><?php echo esc_html($field["label"]) ?></span>
                    <?php if( isset( $field["description"] ) && ! empty( $field["description"] ) ): ?>
                    <span class="adverts-data-header-description"><?php echo esc_html( $field["description"] ) ?></span>
                    <?php endif; ?>
                </th>
            <?php else: ?>
                <th scope="row" class="adverts-data-field-header">
                    <label for="<?php esc_attr_e($field["name"]) ?>">
                        <?php echo esc_html($field["label"]) ?>
                        <?php if( adverts_field_has_validator( $field, "is_required" ) ): ?>
                        <span class="adverts-form-required"><?php _e( "(required)", "adverts" ) ?></span>
                        <?php endif; ?>
                    </label>
                </th>
                <td>
                    <?php call_user_func( adverts_field_get_renderer($field), $field) ?>
                    <?php if( isset($field["error"]) && !empty($field["error"])): ?>
                    <ul class="adverts-error-list">
                        <?php foreach($field["error"] as $error): ?>
                        <li><?php echo esc_html($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </td>
            <?php endif; ?>
            </tr>
        <?php endforeach; ?>			
        </tbody>
    </table>
  
  <?php
}

/**
 * Display 'Expired' state on Classifieds list
 * 
 * This functions shows Expired state in the wp-admin / Classifieds panel
 * 
 * @global WP_Post $post
 * @param array $states
 * @return array
 */
function adverts_display_expired_state( $states ) {
     global $post;
     $arg = get_query_var( 'post_status' );
     if($arg != 'expired'){
          if($post->post_status == 'expired'){
               return array( __( 'Expired', 'adverts' ) );
          }
     }
    return $states;
}

/**
 * Allows to set post author to null
 * 
 * @see wp_insert_post_data filter
 * 
 * @param array $data
 * @since 0.1
 * @return array
 */
function adverts_insert_post_data($data) {
    if(isset($_POST["post_author"]) && $_POST["post_author"] == "0+") {
        $data["post_author"] = 0;
    }
    
    return $data;
}

/**
 * Register meta box with Additional Information (price, location, etc.)
 * 
 * @since 0.1
 * @return void
 */
function adverts_data_box() {
    add_meta_box( 
        'adverts_data_box',
        __( 'Additional Information', 'adverts' ),
        'adverts_data_box_content',
        'advert',
        'normal',
        'low'
    );
}   

/**
 * Register gallery meta box
 * 
 * @since 0.1
 * @return void
 */
function adverts_box_gallery() {
    add_meta_box( 
        'adverts_gallery',
        __( 'Gallery', 'adverts' ),
        'adverts_gallery_content',
        'advert',
        'normal',
        'high'
    );
}

/**
 * Set column headers on Adverts list in wp-admin / Classifieds panel
 * 
 * @see manage_edit-advert_columns filter
 * 
 * @param type $columns
 * @return type
 */
function adverts_edit_columns( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __( 'Title' ),
        'price' => __( 'Price' ),
        'author' => __( 'Author' ),
        'expires' => __( 'Expires', 'adverts' ),
        'date' => __( 'Date' )
    );

    return $columns;
}

/**
 * Insert custom columns on Adverts list in wp-admin / Classifieds panel.
 * 
 * @see manage_advert_posts_custom_column action
 * 
 * @global WP_Post $post
 * @global type $mode
 * @param string $column
 * @param int $post_id
 */
function adverts_manage_post_columns( $column, $post_id ) {
    global $post, $mode;

    switch( $column ) {

        case 'expires' :
            // Insert expires column
            $expires = get_post_meta( $post_id, '_expiration_date', true );
            
            // If expiry date not in DB, then ad never expires
            if( !$expires ) {
                echo __( 'Never', 'adverts' );
                break;
            }

            
            $t_time = date_i18n( __( 'Y/m/d g:i:s A' ), $expires );
            /* @var string $t_time Post creation date */
            $m_time = date_i18n( 'Y-m-d H:i:s', $expires );
            /* @var string $m_time Post expiration date */
            $time = $expires;
            /* @var string $time Post expiration timestamp */

            $time_diff = current_time( 'timestamp' ) - $time;
            $h_time = mysql2date( __( 'Y/m/d' ), $m_time );
            
            if ( $time_diff < 0 ) {
                $h_text = __( 'in %s', 'adverts' );
            } else {
                $h_text = __( '%s ago' );
            }
            
            echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post, $column, $mode ) . '</abbr>';
            echo '<br />';
            echo sprintf( $h_text , human_time_diff( $time, current_time( 'timestamp' ) ));
            
            break;
        case 'price' :

            /* Get the post meta. */
            $price = get_post_meta( $post_id, 'adverts_price', true );

            /* If empty then price is not set. */
            if ( empty( $price ) ) {
                echo __( 'None', 'adverts' );
            } else {
                echo adverts_get_the_price( $post_id, $price);
            }

            break;


        /* Just break out of the switch statement for everything else. */
        default :
            break;
    }
}

/**
 * Defines Adverts sortable columns
 * 
 * @see manage_edit-advert_sortable_columns filter
 * 
 * @param array $columns
 * @since 0.1
 * @return array
 */
function adverts_admin_sortable_columns( $columns ) {

    $columns['price'] = 'price';
    $columns['expires'] = 'expires';

    return $columns;
}

/**
 * Registers 'request' filter for Adverts list in wp-admin
 * 
 * @see adverts_admin_sort()
 * 
 * @since 0.1
 * @return void
 */
function adverts_admin_load() {
    add_filter( 'request', 'adverts_admin_sort' );
}

/**
 * Sort by adverts columns logic
 * 
 * @param array $vars
 * @since 0.1
 * @return array
 */
function adverts_admin_sort( $vars ) {

    /* Check if we're viewing the 'advert' post type. */
    if ( isset( $vars['post_type'] ) && 'advert' == $vars['post_type'] ) {

        /* Check if 'orderby' is set to 'duration'. */
        if ( isset( $vars['orderby'] ) && 'price' == $vars['orderby'] ) {

            $vars = array_merge(
                $vars,
                array(
                    'meta_key' => 'adverts_price',
                    'orderby' => 'meta_value_num'
                )
            );
        }
        
        /* Check if 'orderby' is set to 'expires'. */
        if ( isset( $vars['orderby'] ) && 'expires' == $vars['orderby'] ) {

            $vars = array_merge(
                $vars,
                array(
                    'meta_key' => '_expiration_date',
                    'orderby' => 'meta_value_num'
                )
            );
        }
    }

    return $vars;
}

/**
 * Enqueues Adverts script styles and JS localization
 * 
 * @global string $post_type
 * @since 0.1
 * @return void
 */
function adverts_admin_script() {
    global $post_type;
    
    if( 'advert' != $post_type ) {
        return;
    }
    
    wp_enqueue_style( 'adverts-admin' );
    wp_enqueue_style( 'adverts-icons' );
    wp_enqueue_style( 'adverts-icons-animate' );
    wp_enqueue_script( 'adverts-admin' );
    wp_enqueue_script( 'adverts-gallery' );
    wp_enqueue_script( 'adverts-auto-numeric' );
    wp_enqueue_script( 'plupload-all' );
    wp_enqueue_script( 'suggest' );
    
    wp_localize_script( 'adverts-admin', 'adverts_admin_lang', array(
        "expired" => __("Expired", "adverts"),
        "expires_on" => __("Expires:", "adverts" ),
        "expired_on" => __("Expired:", "adverts" ),
        "suggest_box_info" => __("Start typing user name, email or login below, some suggestions will appear.", "adverts")
    ) );
    
    include_once ADVERTS_PATH . 'includes/gallery.php';
}

