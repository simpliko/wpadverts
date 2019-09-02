// Init global namespace
var WPADVERTS = WPADVERTS || {};

WPADVERTS.Manage = {
    
};

WPADVERTS.Manage.Actions = function( actions ) {
    this.actions = actions;
    this.input = { };
    this.element = { };
    
    this.element.remove = jQuery(this.actions).find(".adverts-manage-delete-confirm");
    this.element.more = jQuery(this.actions).find(".adverts-manage-actions-more");
    this.element.spinner = jQuery(this.actions).find(".adverts-manage-action-spinner");
    
    this.input.remove = jQuery(this.actions).find(".adverts-manage-action-delete");
    this.input.remove_yes = jQuery(this.actions).find(".adverts-manage-action-delete-yes");
    this.input.remove_no = jQuery(this.actions).find(".adverts-manage-action-delete-no");
    this.input.more = jQuery(this.actions).find(".adverts-manage-action-more");
    
    this.input.remove.click( jQuery.proxy( this.remove, this ) );
    this.input.remove_yes.click( jQuery.proxy( this.removeYes, this ) );
    this.input.remove_no.click( jQuery.proxy( this.removeNo, this ) );
    this.input.more.click( jQuery.proxy( this.more, this ) );
    
    // check if has more items
    if(this.element.more.find(".adverts-manage-action").length < 1) {
        this.input.more.hide();
    }
};

WPADVERTS.Manage.Actions.prototype.remove = function(e) {
    e.preventDefault();
    this.element.remove.css("display", "inline-block");
    this.element.spinner.hide();
    this.input.remove_yes.show();
    this.input.remove_no.show();
    this.input.remove.hide();
};

WPADVERTS.Manage.Actions.prototype.removeYes = function(e) {
    e.preventDefault();
    
    this.element.spinner.show();
    this.input.remove_yes.hide();
    this.input.remove_no.hide();
    
    var data = {
        action: "adverts_delete",
        ajax: "1",
        _ajax_nonce: this.input.remove.data("nonce"),
        id: this.input.remove.data("id")
    };

    jQuery.ajax({
        url: adverts_frontend_manage_lang.ajaxurl,
        context: this,
        type: "post",
        dataType: "json",
        data: data,
        success: jQuery.proxy( this.removeSuccess, this ),
        error: jQuery.proxy( this.removeError, this )
    });
};

WPADVERTS.Manage.Actions.prototype.removeNo = function(e) {
    e.preventDefault();
    this.element.remove.attr("style", "");
    this.input.remove.show();
};

WPADVERTS.Manage.Actions.prototype.removeSuccess = function(response) {
    if(response.result == "1") {
        
        var a = jQuery("<a></a>").attr("href", "#").html(adverts_frontend_manage_lang.ok).click(function(e) {
            e.preventDefault();
            jQuery(this).closest(".advert-manage-deleted").fadeOut("fast");
        });
        
        jQuery(this.actions)
            .hide()
            .addClass("advert-manage-deleted adverts-icon-trash")
            .html(response.message)
            .append(" ")
            .append(a)
            .fadeIn("fast");
    } else {
        this.element.spinner.hide();
        this.input.remove_yes.show();
        this.input.remove_no.show();
        alert(response.error);
    }
};

WPADVERTS.Manage.Actions.prototype.removeError = function(response) {
    if(response.result) {
        alert(response.eror);
    } else {
        alert(response);
    }
};

WPADVERTS.Manage.Actions.prototype.more = function(e) {
    e.preventDefault();
    this.element.more.slideToggle("fast");
};

jQuery(function($) {
    
    $(".advert-manage-item").each(function(index, item) {
        new WPADVERTS.Manage.Actions(item);
    });
    
    
});