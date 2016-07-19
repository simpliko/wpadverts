<?php
/**
 * Displays Bank Transfer Module Options Page
 * 
 * This file is a template for wp-admin / Classifieds / Options / Bank Transfer panel. 
 * 
 * It is being loaded by adext_bank_transfer_page_options function.
 * 
 * @see adext_bank_transfer_page_options()
 * @since 0.1
 */
?>
<div class="wrap">
    <h2 class="">
        <?php _e("Payment Gateway / Bank Transfer", "adverts") ?>
    </h2>

    <?php adverts_admin_flash() ?>

    <form action="" method="post" class="adverts-form">
        <table class="form-table">
            <tbody>
            <?php echo adverts_form_layout_config($form) ?>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" value="<?php esc_attr_e($button_text) ?>" class="button-primary" name="Submit"/>
        </p>

    </form>

</div>
