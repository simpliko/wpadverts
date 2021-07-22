<?php
/**
 * Displays Core Options Page
 * 
 * This file is a template for wp-admin / Classifieds / Options / Core panel. 
 * 
 * It is being loaded by adext_core_page_options function.
 * 
 * @see adext_core_page_options()
 * @since 0.1
 */
?>
<div class="wrap">
    <h2 class="nav-tab-wrapper">
        <a href="<?php esc_attr_e( remove_query_arg( array( 'adaction' ) ) ) ?>" class="nav-tab nav-tab-active"><?php _e("Core Options", "wpadverts") ?></a>
        <a href="<?php esc_attr_e( add_query_arg( array('adaction' => 'gallery') ) ) ?>" class="nav-tab "><?php _e("Gallery", "wpadverts") ?></a>
        <a href="<?php esc_attr_e( add_query_arg( array('adaction' => 'types') ) ) ?>" class="nav-tab "><?php _e("Types", "wpadverts") ?></a>
        <a href="<?php esc_attr_e( add_query_arg( array('adaction' => 'moderate') ) ) ?>" class="nav-tab "><?php _e("Spam", "wpadverts") ?></a>
    </h2>

    <?php adverts_admin_flash() ?>

    <form action="" method="post" class="adverts-form">
        <table class="form-table">
            <tbody>
            <?php echo adverts_form_layout_config($form) ?>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" value="<?php echo esc_attr($button_text) ?>" class="button-primary" name="Submit" />
        </p>

    </form>

</div>
