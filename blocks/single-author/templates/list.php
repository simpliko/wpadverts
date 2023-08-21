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
                    <span class="atw-text-gray-500 atw-text-base">
                    <?php printf( __('Published %1$s - %2$s ago', "wpadverts"), $published_date, $published_rel ) ?>
                    </span>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>

    </div>
</div>