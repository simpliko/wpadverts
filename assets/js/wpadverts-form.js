// Init global namespace
var WPADVERTS = WPADVERTS || {};

WPADVERTS.Form = {
    List: []
};

WPADVERTS.Form.OnWindowUnload = function(e) {
    
    var DiscardData = [];
    
    jQuery.each(WPADVERTS.Form.List, function(index, item) {
        if( item.HasDataToDiscard() ) {
            DiscardData.push(index);
        }
    });
    
    if( DiscardData.length === 0 ) {
        return;
    }
    
    var DiscardPrompt = false;
    
    if( DiscardPrompt === true ) {
        e.preventDefault();
        return '';
    } else {
        WPADVERTS.Form.Discard( DiscardData );
        return;
    }

};

WPADVERTS.Form.Discard = function( forms ) {
    jQuery.each(forms, function(index, id) {
        WPADVERTS.Form.List[id].Discard();
    });
};

WPADVERTS.Form.Handler = function( form ) {
    this.Form = form;
    
    this.Form.on( "submit", jQuery.proxy( this.OnSubmit, this ) );
};

WPADVERTS.Form.Handler.prototype.OnSubmit = function( e ) {
    jQuery(window).unbind("beforeunload", WPADVERTS.Form.OnWindowUnload );
};

WPADVERTS.Form.Handler.prototype.HasDataToDiscard = function( e ) {
    
    if( this.HasPostId() && this.GetPostId() > 0 ) {
        return true;
    } 
    
    if( this.HasUploadId() && this.GetUploadId() !== null ) {
        return true;
    }
    
    return false;
};

WPADVERTS.Form.Handler.prototype.HasPostId = function() {
    if( this.Form.find( "#_post_id" ).length > 0 ) {
        return true;
    } else {
        return false;
    }
};

WPADVERTS.Form.Handler.prototype.GetPostId = function() {
    if( this.Form.find( "#_post_id").length === 0 ) {
        return null;
    }
    
    var value = this.Form.find( "#_post_id").val();
    
    if( value.length === 0 ) {
        return 0;
    }
    
    return parseInt(value);
};

WPADVERTS.Form.Handler.prototype.GetPostIdNonce = function() {
    if( this.HasPostId() && this.GetPostId() > 0 ) {
        return this.Form.find("#_post_id_nonce").val();
    } else {
        return null;
    }
};

WPADVERTS.Form.Handler.prototype.HasUploadId = function() {
    if( this.Form.find( "#wpadverts-form-upload-uniqid").length > 0 ) {
        return true;
    } else {
        return false;
    }
};

WPADVERTS.Form.Handler.prototype.GetUploadId = function() {
    if( this.Form.find( "#wpadverts-form-upload-uniqid" ).length === 0 ) {
        return null;
    }
    
    var value = this.Form.find( "#wpadverts-form-upload-uniqid" ).val();
    
    if( value.length === 0 ) {
        return null;
    }
    
    return value;
};

WPADVERTS.Form.Handler.prototype.Discard = function() {
    if( this.HasPostId() && this.GetPostId() > 0 ) {
        this.DiscardPost();
    } 
    if( this.HasUploadId() && this.GetUploadId() ) {
        this.DiscardUpload();
    }
};

WPADVERTS.Form.Handler.prototype.DiscardPost = function() {
    
    var data = {
        action: 'adverts_delete_tmp',
        security: 'nonce',
        _post_id: this.GetPostId(),
        _post_id_nonce: this.GetPostIdNonce(),
        _wpadverts_checksum: this.Form.find("#_wpadverts_checksum").val(),
        _wpadverts_checksum_nonce: this.Form.find("#_wpadverts_checksum_nonce").val(),
        uniqid: this.GetUploadId()
    };

    jQuery.ajax({
        url: adverts_frontend_lang.ajaxurl,
        data: data,
        dataType: 'json',
        type: 'post',
        success: function(response) { }
    });
};

WPADVERTS.Form.Handler.prototype.DiscardUpload = function() {
    
    var data = {
        action: 'adverts_delete_tmp_files',
        security: 'nonce',
        _post_id: this.GetPostId(),
        _post_id_nonce: this.GetPostIdNonce(),
        _wpadverts_checksum: this.Form.find("#_wpadverts_checksum").val(),
        _wpadverts_checksum_nonce: this.Form.find("#_wpadverts_checksum_nonce").val(),
        uniqid: this.GetUploadId()
    };

    jQuery.ajax({
        url: adverts_frontend_lang.ajaxurl, 
        data: data,
        dataType: 'json',
        type: 'post',
        success: function(response) { }
    });
};

jQuery(function($) {
    
    $(".adverts-form").each(function(index, item) {
        WPADVERTS.Form.List.push( new WPADVERTS.Form.Handler( $(item) ) );
    });
    
    $(".adverts-cancel-unload").on("click", function(e) {
        jQuery(window).unbind("beforeunload", WPADVERTS.Form.OnWindowUnload );
    });
    
    $(window).bind( "beforeunload", WPADVERTS.Form.OnWindowUnload );
    
    //WPADVERTS.Form.OnWindowUnload();
});