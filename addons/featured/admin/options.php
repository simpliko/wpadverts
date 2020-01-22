<?php
/**
 * Displays BFeatured Module Options Page
 * 
 * This file is a template for wp-admin / Classifieds / Options / Featured Ads panel. 
 * 
 * It is being loaded by adext_featured_page_options function.
 * 
 * @see adext_featured_page_options()
 * @since 1.0.3
 */
?>
<div class="wrap">
    <h2 class="">
        <?php _e("Featured Ads", "wpadverts") ?>
    </h2>

    <?php adverts_admin_flash() ?>

    <div class="updated fade">
        <p>
            <a href="<?php echo esc_attr( 'http://wpadverts.com/documentation/featured-ads/' ) ?>"><strong><?php _e( 'View Documentation', "wpadverts" ) ?></strong></a>
        </p>
    </div>
    
</div>
