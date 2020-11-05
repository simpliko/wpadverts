var WPADVERTS = WPADVERTS || {};

WPADVERTS.EMAILS = WPADVERTS.EMAILS || {};

WPADVERTS.EMAILS.Edit = {
    
};

WPADVERTS.EMAILS.Edit.Headers = function( el ) {
    this.button = el;
    this.row = el.closest( "tr" );
    
    this.button.on( "click", jQuery.proxy( this.AddHeaderClicked, this ) );
};

WPADVERTS.EMAILS.Edit.Headers.prototype.AddHeaderClicked = function( e ) {
    e.preventDefault();
    
    // { header_name: "Content-Type", header_value: "text/html" }
    var row = new WPADVERTS.EMAILS.Edit.HeaderRow( {} ).InsertBefore( this.row );
};

WPADVERTS.EMAILS.Edit.Attachments = function( el ) {
    this.button = el;
    this.row = el.parent().find(".adext-emails-attachments");
    
    this.button.on( "click", jQuery.proxy( this.AddAttachmentClicked, this ) );
};

WPADVERTS.EMAILS.Edit.Attachments.prototype.AddAttachmentClicked = function( e ) {
    e.preventDefault();
    
    // { header_name: "Content-Type", header_value: "text/html" }
    var row = new WPADVERTS.EMAILS.Edit.AttachmentRow( {} ).InsertBefore( this.row );
};

WPADVERTS.EMAILS.Edit.AttachmentRow = function( data ) {
    this._inserted = false;
    this.tpl = null;
    this.attachment = null;
    
    if( typeof data.attachment !== 'undefined' ) {
        this.attachment = data.attachment;
    } else {
        data.attachment = "";
    }
    
    data._mode = "edit";
    
    this.Render(data);
};

WPADVERTS.EMAILS.Edit.AttachmentRow.prototype.Render = function( data ) {
    var template = wp.template( 'adext-email-edit-attachment-row' );
    var tpl = jQuery( template( data ) );
    
    if(this._inserted === true) {
        this.tpl.html(tpl.html());
    } else {
        this.tpl = tpl;
    }
    
    this.tpl.find(".adext-emails-edit-td-no").on( "click", jQuery.proxy( this.RemoveHeaderClicked, this ) );
};

WPADVERTS.EMAILS.Edit.AttachmentRow.prototype.InsertBefore = function( el ) {
    el.before( this.tpl );
    this.tpl.fadeIn("slow");
    this._inserted = true;
    
    return this;
};

WPADVERTS.EMAILS.Edit.AttachmentRow.prototype.RemoveHeaderClicked = function( e ) {
    e.preventDefault();
    this.tpl.remove();
};

WPADVERTS.EMAILS.Edit.HeaderRow = function( data ) {
    this._inserted = false;
    this.tpl = null;
    this.header_name = null;
    this.header_value = null;
    
    if( typeof data.header_name !== 'undefined' ) {
        this.header_name = data.header_name;
    } else {
        data.header_name = "";
    }
    
    if( typeof data.header_value !== 'undefined' ) {
        this.header_value = data.header_value;
    } else {
        data.header_value = "";
    }
    
    data._mode = "edit";
    
    this.Render(data);

};

WPADVERTS.EMAILS.Edit.HeaderRow.prototype.Render = function( data ) {
    var template = wp.template( 'adext-email-edit-header-row' );
    var tpl = jQuery( template( data ) );
    

    
    if(this._inserted === true) {
        this.tpl.html(tpl.html());
    } else {
        this.tpl = tpl;
    }
    
    this.tpl.find(".adext-emails-edit-td-yes").on( "click", jQuery.proxy( this.OkHeaderClicked, this ) );
    this.tpl.find(".adext-emails-edit-td-edit").on( "click", jQuery.proxy( this.EditHeaderClicked, this ) );
    this.tpl.find(".adext-emails-edit-td-no").on( "click", jQuery.proxy( this.RemoveHeaderClicked, this ) );
};

WPADVERTS.EMAILS.Edit.HeaderRow.prototype.InsertBefore = function( el ) {
    el.before( this.tpl );
    this.tpl.fadeIn("slow");
    this._inserted = true;
    
    return this;
};


WPADVERTS.EMAILS.Edit.HeaderRow.prototype.OkHeaderClicked = function( e ) {
    e.preventDefault();
    this.OkHeader();
};

WPADVERTS.EMAILS.Edit.HeaderRow.prototype.OkHeader = function() {
    this.header_name = this.tpl.find( "input.header-name" ).val();
    this.header_value = this.tpl.find( "input.header-value").val();
    
    var data = {
        _mode: "read",
        header_name: this.header_name,
        header_value: this.header_value
    };
    
    this.Render(data);
};

WPADVERTS.EMAILS.Edit.HeaderRow.prototype.RemoveHeaderClicked = function( e ) {
    e.preventDefault();
    this.tpl.remove();
};

WPADVERTS.EMAILS.Edit.HeaderRow.prototype.EditHeaderClicked = function( e ) {
    e.preventDefault();
    
    var data = {
        _mode: "edit",
        header_name: this.header_name,
        header_value: this.header_value
    };
    
    this.Render(data);
};

jQuery(function($) {
    new WPADVERTS.EMAILS.Edit.Headers($(".button.adext-emails-add-header"));
    
    if( typeof WPADVERTS_EMAIL_EDIT_HEADERS !== 'undefined' ) {
        $.each(WPADVERTS_EMAIL_EDIT_HEADERS, function(index, item) {
            var data = {
                header_name: item.name,
                header_value: item.value
            };
            var row = $(".button.adext-emails-add-header").closest("tr");
            
            new WPADVERTS.EMAILS.Edit.HeaderRow( data ).InsertBefore( row ).OkHeader();
        });
    }
    
    new WPADVERTS.EMAILS.Edit.Attachments($(".button.adext-emails-add-attachment"));
    
    if( typeof WPADVERTS_EMAIL_EDIT_ATTACHMENTS !== 'undefined' ) {
        $.each(WPADVERTS_EMAIL_EDIT_ATTACHMENTS, function(index, item) {
            var data = {
                attachment: item
            }
            var row = $(".adext-emails-attachments");
            
            new WPADVERTS.EMAILS.Edit.AttachmentRow( data ).InsertBefore( row );
        });
    }
    
});