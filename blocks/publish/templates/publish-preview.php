<?php wp_enqueue_script( 'adverts-frontend' ); ?>
<style type="text/css">
    .wpadverts-publish-preview form.wpadverts-form {
        <?php wpadverts_print_grays_variables( isset( $atts["form"] ) ? $atts["form"] : "" ) ?>
    }
    <?php wpadverts_block_button_css( "primary", isset( $atts["primary_button"] ) ? $atts["primary_button"] : array() ) ?>
    <?php wpadverts_block_button_css( "secondary", isset( $atts["secondary_button"] ) ? $atts["secondary_button"] : array() ) ?>
</style>

<div class="wpadverts-blocks wpadverts-publish-preview">

    <div class="md:atw-p-3 atw-mb-6 atw-flex md:atw-bg-gray-50 atw-rounded-md">

        <div class="atw-hidden md:atw-block atw-flex-1 atw-flex atw-items-center atw-self-center">
            <span class="atw-hidden md:atw-block atw-text-xl atw-font-bold ">
                <?php _e( "Preview", "wpadverts" ) ?>
            </span>
        </div>

        <div class="atw-flex-grow md:atw-flex-none atw-flex atw-flex-row atw-items-stretch">

            <form action="" method="post" class="atw-inline atw-flex-grow">
                <input type="hidden" name="_adverts_action" value="" />
                <input type="hidden" name="_post_id" id="_post_id" value="<?php echo esc_attr($post_id) ?>" />
                <input type="hidden" name="_post_id_nonce" id="_post_id_nonce" value="<?php echo esc_attr($post_id_nonce) ?>" />
                <?php echo wpadverts_block_button( array(
                    "html" => '<i class="fa fa-arrow-left atw-pr-3"></i>' . __( "Edit Listing", "wpadverts" ),
                    "type" => "secondary",
                    "action" => "submit",
                    "class" => "adverts-cancel-unload"
                ) ) ?>
            </form>

            <div><span>&nbsp;</span></div>

            <form action="" method="post" class="atw-inline atw-flex-grow">
                <input type="hidden" name="_adverts_action" value="save" />
                <input type="hidden" name="_post_id" id="_post_id" value="<?php echo esc_attr($post_id) ?>" />
                <input type="hidden" name="_post_id_nonce" id="_post_id_nonce" value="<?php echo esc_attr($post_id_nonce) ?>" />
                <?php echo wpadverts_block_button( array(
                    "html" => __( "Publish Listing", "wpadverts" ) . '<i class="fa fa-arrow-right atw-pl-3"></i>',
                    "type" => "primary",
                    "action" => "submit",
                    "class" => "adverts-cancel-unload"
                ) ) ?>
            </form>
        </div>

    </div>

    <div class="atw-flex atw-mb-3">
        <?php echo $ad_preview ?>
    </div>

    <!--div class="atw-mb-3 atw-p-6 atw-flex atw-bg-gray-50 atw-rounded-md">

        <span class="atw-flex atw-items-center atw-text-center">
            <i class="fa-solid fa-eye-slash atw-pr-3"></i>
            <?php _e( "Contact options are hidden in the preview.", "wpadverts" ) ?>
        </span>
    </div-->

    <div class="md:atw-p-3 atw-mb-6 atw-flex md:atw-bg-gray-50 atw-rounded-md atw-justify-end">


        <div class="atw-flex-grow md:atw-flex-none atw-flex atw-flex-row">

            <form action="" method="post" class="atw-inline atw-flex-grow">
                <input type="hidden" name="_adverts_action" value="" />
                <input type="hidden" name="_post_id" id="_post_id" value="<?php echo esc_attr($post_id) ?>" />
                <input type="hidden" name="_post_id_nonce" id="_post_id_nonce" value="<?php echo esc_attr($post_id_nonce) ?>" />
                <?php echo wpadverts_block_button( array(
                    "html" => '<i class="fa fa-arrow-left atw-pr-3"></i> '. __( "Edit Listing", "wpadverts" ),
                    "type" => "secondary",
                    "action" => "submit",
                    "class" => "adverts-cancel-unload"
                ) ) ?>
            </form>

            <div><span>&nbsp;</span></div>

            <form action="" method="post" class="atw-inline atw-flex-grow">
                <input type="hidden" name="_adverts_action" value="save" />
                <input type="hidden" name="_post_id" id="_post_id" value="<?php echo esc_attr($post_id) ?>" />
                <input type="hidden" name="_post_id_nonce" id="_post_id_nonce" value="<?php echo esc_attr($post_id_nonce) ?>" />
                <?php echo wpadverts_block_button( array(
                    "html" => __( "Publish Listing", "wpadverts" ) . '<i class="fa fa-arrow-right atw-pl-3"></i>',
                    "type" => "primary",
                    "action" => "submit",
                    "class" => "adverts-cancel-unload"
                ) ) ?>
            </form>
        </div>

    </div>

</div>

<div 