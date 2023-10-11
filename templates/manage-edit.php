<?php 
    $back_button = '<a href="' . esc_attr($baseurl) . '" class="adverts-button">' . __("Go Back", "wpadverts") . '</a>';
    $back_button = apply_filters( 'adverts_manage_edit_back_button', $back_button, $post_id );
    if( is_post_publicly_viewable( $post_id ) ){
        $public_link = '<a href="' . esc_attr( get_post_permalink( $post_id ) ) . '" class="adverts-button">' . __("View Ad", "wpadverts") . '</a>';
    }
    else{
        $public_link = '<a href="' . esc_attr( add_query_arg( array( 'advert_id' => null, 'action' => 'preview', 'preview_id' => $post_id ) ) ) . '" class="adverts-button">' . __("Preview Ad", "wpadverts") . '</a>';
    }
    $public_link = apply_filters( 'adverts_manage_edit_public_link', $public_link, $post_id );

    if( !empty( $back_button ) || !empty( $public_link ) ){
        echo '<p>' . $back_button . $public_link . '</p>';
    }
?>

<?php adverts_flash( $adverts_flash ) ?>

<form action="" method="post" class="adverts-form <?php echo $form->get_layout() ?>">
    <fieldset>
        
        <?php foreach($form->get_fields( array( "type" => array( "adverts_field_hidden" ) ) ) as $field): ?>
        <?php call_user_func( adverts_field_get_renderer($field), $field, $form ) ?>
        <?php endforeach; ?>
        
        <?php foreach($form->get_fields( array( "exclude" => array( "account" ) ) ) as $field): ?>
        
        <div class="adverts-control-group <?php echo esc_attr( str_replace("_", "-", $field["type"] ) . " adverts-field-name-" . $field["name"] ) ?> <?php if(adverts_field_has_errors($field)): ?>adverts-field-error<?php endif; ?>">
            
            <?php if($field["type"] == "adverts_field_header"): ?>
            <div class="adverts-field-header">
                <span class="adverts-field-header-title"><?php echo esc_html($field["label"]) ?></span>
                <?php if( isset( $field["description"] ) ): ?>
                <span class="adverts-field-header-description"><?php echo esc_html( $field["description"] ) ?></span>
                <?php endif; ?>
            </div>
            <?php else: ?>
            
            <label for="<?php esc_attr_e($field["name"]) ?>">
                <?php esc_html_e($field["label"]) ?>
                <?php if(adverts_field_is_required($field)): ?>
                <span class="adverts-form-required">*</span>
                <?php endif; ?>
            </label>
            
            <?php call_user_func( adverts_field_get_renderer($field), $field, $form ) ?>

            <?php endif; ?>
            
            <?php if(adverts_field_has_errors($field)): ?>
            <ul class="adverts-field-error-list">
                <?php foreach($field["error"] as $k => $v): ?>
                <li><?php echo esc_html($v) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            
        </div>
        <?php endforeach; ?>
        

        
        <div  class="adverts-control-group <?php echo isset($actions_class) ? $actions_class : '' ?>">
            
            <?php if( adverts_config( "adverts_manage_moderate" ) == "1" ): ?>
            <div class="adverts-moderation-notice" style="width: 100%">
                <span class="adverts-icon-attention"></span>
                <span>
                <?php _e( "<strong>Important Note.</strong> After submitting changes your Ad will be held for moderation. It will become active again once the Administrator will approve it.", "wpadverts" ) ?>
                </span>
            </div>
            <?php endif; ?>
            
            <input type="submit" name="submit" value="<?php _e("Update", "wpadverts") ?>" style="font-size:1.2em" />
        </div>
        
    </fieldset>
</form>