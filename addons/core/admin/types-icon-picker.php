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
                    <?php foreach( Adverts_Types_Admin::scan_dashicons() as $di ): ?>
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