<?php
/**
 * General Form Template
 * 
 * This template is being used tot generate most of the frontend forms in Adverts
 * 
 * @since 1.0
 * @var $form Adverts_Form
 */

if( ! isset( $form_label_placement ) ) {
    $form_label_placement = "adverts-form-aligned";
}

?>

<?php if( isset( $adverts_flash ) ): ?>
<?php adverts_flash( $adverts_flash ) ?>
<?php endif; ?>

<form action="" method="post" class="adverts-form <?php echo esc_html( $form_label_placement ) ?>">
    <fieldset>
        
        <?php foreach($form->get_fields( array( "type" => array( "adverts_field_hidden" ) ) ) as $field): ?>
        <?php call_user_func( adverts_field_get_renderer($field), $field) ?>
        <?php endforeach; ?>
        
        <?php foreach($form->get_fields() as $field): ?>
        
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
                <?php if( isset( $field["label"] ) && ! empty( $field["label"] ) ): ?>
                    <?php echo esc_html($field["label"]) ?>
                    <?php if(adverts_field_has_validator($field, "is_required")): ?>
                    <span class="adverts-form-required">*</span>
                    <?php endif; ?>
                <?php endif; ?>
            </label><?php call_user_func( adverts_field_get_renderer($field), $field) ?>

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
        
        <?php if( isset($buttons) && is_array($buttons) ): ?>
        <div  style="">
            <?php include_once ADVERTS_PATH . "/includes/class-html.php"; ?>
            <?php foreach($buttons as $button): ?>
            <?php echo Adverts_Html::build($button["tag"], array_replace($button, array("tag"=>null, "html"=>null)), $button["html"]) ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
    </fieldset>
</form>