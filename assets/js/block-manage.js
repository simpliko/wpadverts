var WPADVERTS = WPADVERTS || {};

WPADVERTS.Modal = function( template, data ) {

    var tpl = template( data );

    this.UI = jQuery( tpl );
    this.Icon = null;
    this.Title = data.title;
    this.Text = data.text;

    this.UI.removeClass("wpadverts-hidden");

    this.UI.Progress = this.UI.find(".wpa-progress");
    this.UI.Confirm = this.UI.find(".wpa-confirm");
    this.UI.Cancel = this.UI.find(".wpa-cancel");

    this.UI.Cancel.on("click", jQuery.proxy(this.onCancel, this));

    //return this;
};

WPADVERTS.Modal.prototype.onCancel = function(e) {
    e.preventDefault();
    this.UI.addClass("wpadverts-hidden");
    this.UI.remove();
};

WPADVERTS.ModalDelete = function( data ) {

    this.Data = data;

    var data = {
        icon: "question",
        title: wpadverts_block_manage.delete_q,
        text: wpadverts_block_manage.delete_q_text.replace( "%s", this.Data.Title ),
        confirm: {
            title: wpadverts_block_manage.delete_q_confirm,
            action: function() {}
        },
        cancel: {
            title: wpadverts_block_manage.delete_q_cancel,
            action: function() {}
        }
    };

    

    this.ModalQ = new WPADVERTS.Modal( wp.template( "wpadverts-modal" ), data );
    this.ModalQ.UI.Confirm.on( "click", jQuery.proxy( this.onConfirm, this ) );

    jQuery("body").append(this.ModalQ.UI);
};

WPADVERTS.ModalDelete.prototype.onConfirm = function(e) {
    e.preventDefault();
    this.ModalQ.UI.Confirm.hide();
    this.ModalQ.UI.Cancel.hide();

    this.ModalQ.UI.Progress.removeClass("wpadverts-hidden");


    var data = {
        action: "adverts_delete",
        id: this.Data.ID,
        ajax: 1,
        _ajax_nonce: this.Data.Nonce
    };

    this.xhr = jQuery.ajax({
        url: wpadverts_block_manage.ajaxurl,
        data: data,
        type: "post",
        dataType: "json",
        context: this,
        success: this.onConfirmSuccess,
        error: this.onConfirmErrorAjax
    });
};

WPADVERTS.ModalDelete.prototype.onConfirmSuccess = function( response ) {

    if(response.result !== 1) {
        return this.onConfirmError(response.error);
    }

    this.ModalQ.UI.remove();
    this.ModalQ = null;

    var data = {
        icon: "success",
        title: wpadverts_block_manage.delete_s,
        text: "",
        confirm: {
            title: wpadverts_block_manage.delete_s_confirm,
            action: function() {}
        }
    }

    this.ModalS = new WPADVERTS.Modal(wp.template( "wpadverts-modal" ), data);
    this.ModalS.UI.Confirm.on("click", jQuery.proxy(this.GoBack, this));

    jQuery("body").append(this.ModalS.UI);
};

WPADVERTS.ModalDelete.prototype.onConfirmErrorAjax = function( response ) {
    this.onConfirmError(response.responseText)
};

WPADVERTS.ModalDelete.prototype.onConfirmError = function( error ) {
    this.ModalQ.UI.remove();
    this.ModalQ = null;
    
    var data = {
        icon: "question",
        title: wpadverts_block_manage.delete_e,
        text: error,
        confirm: {
            title: wpadverts_block_manage.delete_e_confirm,
            action: function() {}
        }
    }

    this.ModalE = new WPADVERTS.Modal(wp.template( "wpadverts-modal" ), data);
    this.ModalE.UI.Confirm.on("click", jQuery.proxy(this.Close, this));

    jQuery("body").append(this.ModalE.UI);
};

WPADVERTS.ModalDelete.prototype.GoBack = function( response ) {
    window.location = jQuery(".js-wpa-manage-go-back").attr("href");
}

WPADVERTS.ModalDelete.prototype.Close = function(e) {
    e.preventDefault();

    if(this.ModalQ) {
        this.ModalQ.UI.remove();
        this.ModalQ = null;
    }
    if(this.ModalE) {
        this.ModalE.UI.remove();
        this.ModalE = null;
    }    
    if(this.ModalS) {
        this.ModalS.UI.remove();
        this.ModalS = null;
    }

    return false;
}

jQuery(function($) {
    jQuery(".js-wpa-block-manage-delete button").on("click", function(e) {
        e.preventDefault();

        var $this = jQuery(this);
        var data = {
            ID: $this.data("id"),
            Nonce: $this.data("nonce"),
            Title: $this.data("title")
        };
        new WPADVERTS.ModalDelete(data);

        return false;
    });

    $(".wpadverts-block-manage .wpa-block-list-results > .wpa-result-item").on("click", function(e) {
        window.location = $(this).find(".wpa-result-link").attr("href");
    });
    $(".wpadverts-block-manage .wpa-block-list-results > .wpa-result-item").addClass("atw-cursor-pointer");
    
});