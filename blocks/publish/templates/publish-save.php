<?php wp_enqueue_script( 'adverts-frontend' ); ?>
<style type="text/css">
    .wpadverts-publish-save form.wpadverts-form {
        <?php wpadverts_print_grays_variables( isset( $atts["form"] ) ? $atts["form"] : "" ) ?>
    }
    <?php wpadverts_block_button_css( "primary", isset( $atts["primary_button"] ) ? $atts["primary_button"] : array() ) ?>
    <?php wpadverts_block_button_css( "secondary", isset( $atts["secondary_button"] ) ? $atts["secondary_button"] : array() ) ?>
</style>

<?php


if( $moderate == "1" ) {
    $m = _e("Your ad has been put into moderation, please wait for admin to approve it.", "wpadverts");
} else {
    $m = sprintf(__('Your ad has been published. You can view it here "<a href="%1$s">%2$s</a>".', 'wpadverts'), get_post_permalink( $post_id ), get_post( $post_id )->post_title );
}

?>

<div class="wpadverts-blocks wpadverts-publish-save">
    <div class="wpadverts-flash wpa-style-info wpa-layout-big">
        <div class="wpa-flash-content atw-flex">

            <span class="wpa-flash-icon"><i class="fa-solid fa-circle-check"></i></span>

            <div class="atw-flex-1 atw-flex atw-flex-col atw-items-center">
                
                <span class="wpa-flash-message atw-flex-1 atw-font-bold atw-py-3"><?php echo $adverts_flash["success"][0]["message"] ?></span>
                <span class="wpa-flash-message atw-flex-1 atw-pb-3"><?php echo $m ?></span>  
                
                <span class="atw-flex atw-flex-row atw-pb-3 atw-w-full">

                    <form action="" class="atw-p-3 atw-flex-grow">
                        <?php wpadverts_block_button( array( "text" => "Publish another Ad", "type" => "secondary" )); ?>
                    </form>

                    <form action="" class="atw-p-3 atw-flex-grow">
                        <?php wpadverts_block_button( array( "text" => "View ads list", "type" => "secondary" )); ?>
                    </form>
                </span>
                
            </div>
        </div>
    </div>
</div>
