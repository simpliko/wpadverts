<?php wp_enqueue_style( 'media-views' ) ; ?>

<div class="wrap">
    
    <h2 class="nav-tab-wrapper">
        <a href="<?php esc_attr_e( remove_query_arg( array( 'adaction', 'edit-post' => false, 'edit-taxonomy' => false ) ) ) ?>" class="nav-tab"><?php _e("Core Options", "wpadverts") ?></a>
        <a href="<?php esc_attr_e( add_query_arg( array('adaction' => 'gallery', 'edit-post' => false, 'edit-taxonomy' => false ) ) ) ?>" class="nav-tab "><?php _e("Gallery", "wpadverts") ?></a>
        <a href="<?php esc_attr_e( add_query_arg( array('adaction' => 'types', 'edit-post' => false, 'edit-taxonomy' => false ) ) ) ?>" class="nav-tab nav-tab-active"><?php _e("Types", "wpadverts") ?></a>
    </h2>
    
    <h2 class="">
        <?php echo esc_html( $h2_title ) ?>
        
    </h2>
</div>

<style type="text/css">
    #poststuff .wpadverts-types-edit {
        display: flex;
        margin: 0;
        padding: 0;
    }
    .wpadverts-types-edit-menu {
        width: 250px;
        margin:0;
        line-height: 1em;
        padding: 0 0 10px;
        position: absolute;
        background-color: #fafafa;
        border-left: 1px solid #eee;
        right: 0;
        top: 0;
        bottom: 0;
        
    }  
    
    .wpadverts-types-edit-menu > li {
        margin: 0;
        padding: 0;
    }
    

    
    .wpadverts-types-edit-menu > li > a {
        margin: 0;
        padding: 10px;
        display: block;
        box-shadow: none;
        line-height: 20px!important;
        border-bottom: 1px solid #eee;
        text-decoration: none;
        vertical-align: middle;
    }
    
    .wpadverts-types-edit-menu > li > a > .dashicons {
        font-size: 14px;
        vertical-align: baseline;
        height: 18px;
    }
    .wpadverts-types-edit-menu > li > a > .dashicons:before {
        font-size: 14px;
    }
    

    .wpadverts-types-edit-menu > li.active {
        color: #555;
        position: relative;
        background-color: #eee;
    }
    
    .wpadverts-types-edit-menu > li:hover > a,
    .wpadverts-types-edit-menu > li.active > a {
        color: #555;
    }
    
    .wpadverts-types-edit-form {
        margin-right: 250px;
    }
    
    .wpadverts-types-edit-form .form-table {
        margin: 0;
        border-spacing: 12px;
        border-collapse: separate;
    }
    .wpadverts-types-edit-form .form-table tr {
        margin: 0;
    }
    .wpadverts-types-edit-form .form-table th {
        margin:0;
        padding: 6px 0 4px 0;
        font-size: 13px;
        color: #555;
        font-weight: normal;
    }
    
    .wpadverts-types-edit-form .form-table td {
        margin: 0;
        padding: 0;
        font-size: 13px;
    }
    
 
    
    .wpadverts-types-form-support th,
    .wpadverts-types-form-taxonomies th {
        width: 0 !important;
        display: none !important;
    }
    
    
    
    
    
    
    
    .wpadverts-admin-cursor-block,
    .wpadverts-admin-cursor-block input[type=checkbox] {
        cursor: not-allowed !important;
    }
    
    .wpadverts-fcl-tr-rewrite_slug th,
    .wpadverts-fcl-tr-rewrite_slug td {
        margin-bottom:10px;
        padding-bottom: 10px;
    }
    
    .wpadverts-fcl-tr-_rewrite_slug th,
    .wpadverts-fcl-tr-_rewrite_slug td {
        margin-top:0;
        padding-top: 0;
    }
    
 
    
    .wpadverts-admin-types-icon .edit-attachment-frame .media-frame-title {
        right:500px;
    }
    
    .wpadverts-admin-types-icon .dashicons {
        line-height: 29px;
    }
    .wpadverts-admin-types-icon .media-frame-content {
        padding: 16px;
    }
    
    .wpadverts-admin-types-icon .edit-attachment-frame .media-frame-content {
        bottom: 62px;
    }
    
    .wpadverts-admin-types-icon-actions {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 30px;
        border-top: 1px solid #ddd;
        background: #f3f3f3;
        padding: 16px;
    }
    
    .media-frame-content .button-secondary {
        display: inline-block;
        margin: 4px;
    }
    
    .wpadverts-admin-types-icon-search {
        position: absolute;
        right: 32px;
        top: 9px;  
    }
</style>

<div id="poststuff" class="wrap">
    
    <?php adverts_admin_flash() ?>
    
    <form action="" method="post">
    
        <div class="postbox ">
            <div class="postbox-header">
                <h2 class="hndle ui-sortable-handle">
                    <?php _e( "Configuration", "wpadverts" ) ?>
                </h2>
   
            </div>
            <div class="inside wpadverts-types-edit">

                <div class="wpadverts-types-edit-form" >
                    
                    <div class="adverts-dt-form adverts-form wpadverts-types-form-general" data-tab="general">
                        <table class="form-table">
                            <tbody>
                            <?php echo adverts_form_layout_config($form_simple) ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="adverts-dt-form adverts-form wpadverts-types-form-labels" data-tab="labels">
                        <table class="form-table">
                            <tbody>
                            <?php echo adverts_form_layout_config($form_labels) ?>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                
                <ul class="wpadverts-types-edit-menu">
                    <li><a href="#" data-tab="general"><?php _e( "General", "wpadverts" ) ?></a></li>
                    <li><a href="#" data-tab="labels"><?php _e( "Labels", "wpadverts" ) ?></a></li>
                </ul>
            </div>
        </div>
        

        
        
        <div class="postbox ">
            <div class="postbox-header">
                <h2 class="hndle ui-sortable-handle">
                    <?php _e( "Actions", "wpadverts" ) ?>
                </h2>
                
            </div>
            <div class="inside">
                
                <input type="submit" value="<?php echo esc_attr($button_text) ?>" class="button-primary" name="_submit_cpt" />
                <a href="<?php echo esc_attr( $restore_url ) ?>" class="button-secondary"><?php _e( "Restore Defaults", "wpadverts" ) ?></a>
            </div>
        </div>

    </form>

</div>

<div class="wpadverts-admin-types-icon" style="display:none">
    <div tabindex="0" class="media-modal wp-core-ui" role="dialog" aria-labelledby="media-frame-title">
			
	<div class="media-modal-content" role="document">
            <div class="edit-attachment-frame mode-select hide-menu hide-router">
                <div class="edit-media-header">
                    <input type="text" class="wpadverts-admin-types-icon-search" placeholder="<?php _e( "Filter ...", "wpadverts" ) ?>" />
		</div>
		<div class="media-frame-title">
                    <h1><?php _e( "Select Icon", "wpadverts" ) ?> </h1>
                    
                </div>
                <div class="media-frame-content">
                    <?php foreach( $dashicons as $di ): ?>
                    <a href="#" class="button-secondary wpadverts-admin-types-icon-button" data-icon="<?php echo $di ?>" title="<?php echo ucwords( str_replace( "-", " ", $di ) ) ?>"><span class="dashicons  <?php echo $di ?>"></span></a>
                    <?php endforeach; ?>
                </div>
                <div class="wpadverts-admin-types-icon-actions">
                    <div class="actions">
                        <a class="button-primary wpadverts-admin-types-icon-select" href="#"><?php _e( "Select", "wpadverts" ) ?></a>					
                        <a class="button-secondary wpadverts-admin-types-icon-close" href="#"><?php _e( "Cancel", "wpadverts" ) ?></a>					
                    </div>
		</div>
            </div>

        </div>
		
    </div>
    <div class="media-modal-backdrop"></div>
</div>

<?php


//echo "<pre>";
global $_wp_post_type_features;
//print_r(get_taxonomies());
//print_r(get_post_type_object("advert"));



//echo "</pre>";