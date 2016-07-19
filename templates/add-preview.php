<?php include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/single.php' ); ?>

<hr/>

<form action="" method="post" style="display:inline">
    <input type="hidden" name="_adverts_action" value="" />
    <input type="hidden" name="_post_id" id="_post_id" value="<?php esc_attr_e($post_id) ?>" />
    <input type="submit" value="<?php _e("Edit Listing", "adverts") ?>" style="font-size:1.2em" class="adverts-cancel-unload" />
</form>

<form action="" method="post" style="display:inline">
    <input type="hidden" name="_adverts_action" value="save" />
    <input type="hidden" name="_post_id" id="_post_id" value="<?php esc_attr_e($post_id) ?>" />
    <input type="submit" value="<?php _e("Publish Listing", "adverts") ?>" style="font-size:1.2em" class="adverts-cancel-unload" />
</form>