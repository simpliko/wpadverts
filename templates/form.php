<?php
/**
 * General Form Template
 * 
 * This template is being used tot generate most of the frontend forms in Adverts
 * 
 * @since 1.0
 * @var $form Adverts_Form
 */
?>

<form action="" method="post" class="adverts-form adverts-form-aligned">
    <fieldset>
        
        <?php foreach($form->get_fields( array( "type" => array( "adverts_field_hidden" ) ) ) as $field): ?>
        <?php call_user_func( adverts_field_get_renderer($field), $field) ?>
        <?php endforeach; ?>
        
        <?php foreach($form->get_fields() as $field): ?>
        
        <div class="adverts-control-group <?php esc_attr_e( str_replace("_", "-", $field["type"] ) . " adverts-field-name-" . $field["name"] ) ?> <?php if(adverts_field_has_errors($field)): ?>adverts-field-error<?php endif; ?>">
            
            <?php if($field["type"] == "adverts_field_header"): ?>
            <h3 style="border-bottom:2px solid silver"><?php esc_html_e($field["label"]) ?></h3>
            <?php else: ?>
            
            <label for="<?php esc_attr_e($field["name"]) ?>">
                <?php esc_html_e($field["label"]) ?>
                <?php if(adverts_field_has_validator($field, "is_required")): ?>
                <span class="adverts-form-required">*</span>
                <?php endif; ?>
            </label><?php call_user_func( adverts_field_get_renderer($field), $field) ?>

            <?php endif; ?>
            
            <?php if(adverts_field_has_errors($field)): ?>
            <ul class="adverts-field-error-list">
                <?php foreach($field["error"] as $k => $v): ?>
                <li><?php esc_html_e($v) ?></li>
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