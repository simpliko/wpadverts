<div class="wpadverts-cpt <?php echo sprintf( "wpadverts-cpt-", $atts["post_type"] ) ?> wpadverts-cpt-single-contact atw-w-full atw-flex atw-flex-col">

<?php if( $has_visible_contact_options ): ?>
    <div class="wpa-cpt-contact-details atw-my-6 atw--mx-1">

        <div class="atw-relative atw-flex atw-flex-col md:atw-flex-row atw--mx-1">

            <div class="atw-relative atw-flex atw-flex-1 atw-flex-col md:atw-flex-row md:atw-flex-none">
                <?php foreach( $contact_options as $contact_option ): ?>
                <?php if( $contact_option["is_active"] && $contact_option["is_visible"] ): ?>
                <div class="atw-flex-auto atw-mx-1 atw-mb-3">
                <?php 
                    echo wpadverts_block_button( 
                        $contact_option, 
                        $contact_option["options"]
                    ) 
                ?>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
        </div>

        <?php do_action( "wpadverts/block/details/tpl/contact-content", $post_id, $atts ) ?>

    </div>

    <div class="wpa-more-bg atw-z-30 atw-bg-black atw-inset-0 atw-absolute atw-opacity-60" style="display:none"></div>

<?php endif; ?>

</div>