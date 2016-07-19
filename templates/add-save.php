<?php adverts_flash( $adverts_flash ) ?>

<div>
    <?php if($moderate == "1"): ?>
    <p><?php _e("Your ad has been put into moderation, please wait for admin to approve it.", "adverts") ?></p>
    <?php else: ?>
    <p><?php printf(__('Your ad has been published. You can view it here "<a href="%1$s">%2$s</a>".', 'adverts'), get_post_permalink( $post_id ), get_post( $post_id )->post_title ) ?></p>
    <?php endif; ?>
</div>