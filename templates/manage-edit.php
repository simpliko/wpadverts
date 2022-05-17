<p>
    <a href="<?php echo esc_attr($baseurl) ?>" class="adverts-button"><?php _e("Go Back", "wpadverts") ?></a>

    <?php if( is_post_publicly_viewable( $post_id ) ): ?>
    <a href="<?php echo esc_attr(get_post_permalink( $post_id )) ?>" class="adverts-button"><?php _e("View Ad", "wpadverts") ?></a>
    <?php else: ?>
    <a href="<?php echo esc_attr(add_query_arg( array( 'advert_id' => null, 'action' => 'preview', 'preview_id' => $post_id ))) ?>" class="adverts-button"><?php _e("Preview Ad", "wpadverts") ?></a>
    <?php endif; ?>
</p>

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