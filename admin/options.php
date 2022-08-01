<?php
/**
 * Displays Adverts Options Page
 * 
 * This file is a template for wp-admin / Classifieds / Options page. It is being loaded
 * by adverts_admin_page_extensions() function.
 * 
 * @see adverts_admin_page_extensions()
 * @since 0.1
 */
?>

<style type="text/css">
.wpadverts-admin-modules-header {
    margin-left:-20px !important;
    padding-left:20px;
    overflow: hidden;
    clear: both;
    margin: 0;
    
    color: rgb(30, 30, 30);
    font-weight: normal;
    background: white;
}
.wpadverts-admin-modules-header > h2 {
    font-size: 2em;
    font-weight: normal;
}
.wpadverts-module-group {
    font-size: 23px;
    font-weight: 400;
    margin: 0;
    padding: 9px 0 4px;
    line-height: 1.3;
}
.wpadverts-module-group-title {
    font-size: 23px;
    font-weight: 400;
    margin: 0;
    margin-right: 0px;
    padding: 9px 0 4px;
    line-height: 1.3;
}

.adverts-options {
    display: flex;
    flex-wrap: wrap;
}

.adverts-options-wrap {
    display: flex;
    flex-direction: column;
}
.adverts-options-item {
    flex: 1 1 0%;
}
.adverts-options-actions {
    align-self: flex-end;
    box-sizing: border-box;
    width: 100%;
    flex: none;
}

.adverts-options-item-title {
    display: flex;
    align-items: center;
}

.adverts-options-item-title > span {
    font-weight: 400;
    padding: 0 0 0 6px;
}

.adverts-options-item-title .wpadverts-item-icon {
    filter: invert(70%) sepia(9%) saturate(103%) hue-rotate(169deg) brightness(96%) contrast(88%);
}

@media screen and (max-width:430px) {
    body .adverts-options-wrap {
        margin: 8px 0px 0 0px;
        width: 100%;
    }
}
</style>

<div class="wpadverts-admin-modules-header">
    <h2><?php _e( "Modules and Extensions", "wpadverts" ) ?></h2>
</div>  

<div class="wrap">

<?php foreach($module_groups as $mg_name => $group): ?>
<div class="wpadverts-module-group">

    <span class="wpadverts-module-group-title"><?php esc_html_e($group["title"]) ?></span>
</div>

<div class="adverts-options">
    
    <?php foreach($group["modules"] as $key => $data): ?>
    <div class="adverts-options-wrap">
        <div class="adverts-options-item">
            <h3 class="adverts-options-item-title">
                <img class="wpadverts-item-icon" src="https://wpadverts.com/wp-content/themes/red-dune/docs/contact-form.svg" height="24" alt="" />
                <span><?php esc_html_e($data["title"]) ?></span>
            </h3>
            <p><?php esc_html_e($data["text"]) ?></p>
        </div>
        <div class="adverts-options-actions">
            <?php if($data["type"]=="static"): ?>
            
                <em><?php _e("Cannot be disabled", "wpadverts") ?></em>
                <a href="<?php esc_attr_e( add_query_arg( array( 'module'=>$key ) ) ) ?>" class="button-primary"><?php _e("Settings") ?></a>

            <?php elseif($data["plugin"]): ?>
            
                <?php include_once ABSPATH . 'wp-admin/includes/plugin.php' ?>
            
                <?php if( is_plugin_active( $data["plugin"] ) ): ?>
                    <em><?php _e( "Addon Uploaded and Activated", "wpadverts") ?></em>
                    <a href="<?php esc_attr_e( add_query_arg( array( 'module'=>$key ) ) ) ?>" class="button-primary"><?php _e("Settings") ?></a>
                <?php elseif( adverts_plugin_uploaded( $data["plugin"] ) ): ?>
                    <em><?php _e( "Addon Uploaded but Inactive", "wpadverts") ?></em>
                    <a href="<?php esc_attr_e( admin_url( 'plugins.php?plugin_status=inactive' ) ) ?>" class="button-primary"><?php _e("Activate") ?></a>
                <?php else: ?>
                    <a href="<?php esc_attr_e( $data["purchase_url"]) ?>" class="button-secondary">
                        <strong><?php _e("Get This Addon", "wpadverts") ?></strong>
                        <span class="dashicons dashicons-cart" style="font-size:18px; line-height: 24px"></span>
                    </a>
                <?php endif; ?>
            
            <?php else: ?>
            
                <?php if(isset($module[$key])): ?>
                <a href="<?php esc_attr_e( add_query_arg( array( 'module'=>$key ) ) ) ?>" class="button-primary"><?php _e("Settings") ?></a>
                <a href="<?php esc_attr_e( add_query_arg( array( 'disable'=>$key, "noheader"=>1 ) ) ) ?>" class="button-secondary" style="margin-right:4px"><?php _e("Disable", "wpadverts") ?></a>
                <?php else: ?>
                <a href="<?php esc_attr_e( add_query_arg( array( 'enable'=>$key, "noheader"=>1 ) ) ) ?>" class="button-secondary"><?php _e("Enable") ?></a>
                <?php endif; ?>
            
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

    
</div>
<?php endforeach; ?>

</div>