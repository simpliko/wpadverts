<div class="wpadverts-cpt <?php echo sprintf( "wpadverts-cpt-", $atts["post_type"] ) ?> atw-w-full atw-flex atw-flex-col">

    <div class="atw-flex atw-flex-col md:atw-flex-row">
        <div class="atw-flex atw-grow ">
            <div class="atw-flex-none atw-m-0 atw-pr-4 -adverts-single-author-avatar">
                <?php $id_or_email = get_post_field( 'post_author', $post_id ) ?>
                <?php $id_or_email = $id_or_email ? $id_or_email : get_post_meta($post_id, 'adverts_email', true) ?>
                <?php echo get_avatar( $id_or_email, $avatar_size, "", "", array( "class" => "atw-m-0 atw-p-0 atw-block $avatar_radius" ) ) ?>
            </div>
            <div class="atw-flex atw-flex-col atw-grow atw-justify-center -adverts-single-author-name">
                <div class="atw-block">
                    <span class="atw-font-bold atw-text-gray-700 atw-text-xl"><?php echo apply_filters( "adverts_tpl_single_posted_by", esc_html( get_post_meta($post_id, 'adverts_person', true) ), $post_id ) ?></span>
                </div>
                <div class="atw-block">
                    <?php if(in_array("published", $data_secondary)): ?>
                    <span class="atw-text-gray-500 atw-text-base atw-px-2 md:atw-px-0">
                    <?php printf( __('Published %1$s - %2$s ago', "wpadverts"), $published_date, $published_rel ) ?>
                    </span>
                    <?php endif; ?>

                    <?php if(in_array("reveal_phone", $data_secondary)): ?>
                    <span class="wpa-block-contact-reveal-phone atw-text-gray-500 atw-text-base atw-px-2 md:atw-px-0">
                        <span class="fas fa-phone"></span>

                        <a href="#" class="atw-text-sm wpa-reveal-btn" data-postid="<?php echo esc_attr(get_the_ID()) ?>">show phone number</a>

                        <svg class="wpa-reveal-spinner atw-hidden atw-animate-spin atw-transition-transform atw-h-4 atw-w-4 atw-ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="atw-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="atw-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>

                        <span class="atw-text-sm wpa-reveal-data atw-hidden">
                            <span class="atw-font-bold wpa-reveal-phone-number atw-pr-1"></span>
                            <a href="#" class="atw-text-sm wpa-phone-copy atw-pr-2"><?php _e("copy", "wpadverts") ?></a>
                            <a href="#" class="atw-text-sm wpa-phone-call"><?php _e("call", "wpadverts") ?></a>
                        </span>

                    </span>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>

    </div>
</div>