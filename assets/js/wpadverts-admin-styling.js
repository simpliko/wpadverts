var WPADVERTS = WPADVERTS || {};

WPADVERTS.Styling = {
    
};

WPADVERTS.Styling.Tabs = function( tabs ) {
    this.tabs = tabs;
    
    this.tabs.find("a").on("click", jQuery.proxy(this.onTabClick,this));
}

WPADVERTS.Styling.Tabs.prototype.onTabClick = function(e) {

    jQuery.each(this.tabs.find("a"), jQuery.proxy(this.onTabClickEach,this));

    jQuery(e.target).addClass("nav-tab-active");
    jQuery("." + jQuery(e.target).data("show-tab") ).show();

    return false;
}

WPADVERTS.Styling.Tabs.prototype.onTabClickEach = function(index, item) {
    var $this = jQuery(item);
    $this.removeClass("nav-tab-active");
    jQuery("." + $this.data("show-tab")).hide();
}

WPADVERTS.Styling.Button = function( el, preview ) {
    this.el = el;
    this.form = el.find("form");
    this.radius = el.find(".border-radius");
    this.width = el.find(".border-width");
    this.font_weight = el.find(".font-weight");
    this.submit = el.find(".button-primary");
    this.reset = el.find(".wpa-button-settings-reset");
    this.spinner = el.find(".spinner");

    this.preview = preview;

    this.radius.on("change", jQuery.proxy(this.onRadiusChange, this));
    this.width.on("change", jQuery.proxy(this.onWidthChange, this));
    this.font_weight.on("change", jQuery.proxy(this.onFontWeightChange, this));

    this.submit.on("click", jQuery.proxy(this.onSubmitClick, this));
    this.reset.on("click", jQuery.proxy(this.onResetClick, this));
    this.setDefaults();
}

WPADVERTS.Styling.Button.prototype.removePreviewClass = function(index, item) {
    this.preview.removeClass(jQuery(item).attr("value"));
}

WPADVERTS.Styling.Button.prototype.removePreviewDataClass = function(index, item) {
    this.preview.removeClass(jQuery(item).data("value"));
};

WPADVERTS.Styling.Button.prototype.setDefaults = function() {
    this.preview.addClass(this.width.find("opton:selected").data("value"));
    this.preview.addClass(this.radius.find("option:selected").data("value"));
};

WPADVERTS.Styling.Button.prototype.onRadiusChange = function( e ) {
    jQuery.each(this.radius.find("option"), jQuery.proxy(this.removePreviewDataClass,this));
    this.preview.addClass(this.radius.find("option:selected").data("value"));
};

WPADVERTS.Styling.Button.prototype.onWidthChange = function( e ) {
    jQuery.each(this.width.find("option"), jQuery.proxy(this.removePreviewDataClass,this));
    this.preview.addClass(this.width.find("option:selected").data("value"));
};

WPADVERTS.Styling.Button.prototype.onFontWeightChange = function( e ) {
    jQuery.each(this.font_weight.find("option"), jQuery.proxy(this.removePreviewClass,this));
    this.preview.addClass(e.target.value);
};

WPADVERTS.Styling.Button.prototype.onResetClick = function( e ) {
    e.preventDefault();

    this.submit.addClass("disabled");
    this.spinner.addClass("atw-visible");

    var data = {
        action: "wpadverts-styling-reset",
        param: this.form.data("param")
    };

    jQuery.ajax({
        url: adverts_admin_styling_lang.ajax,
        data: data,
        dataType: "json",
        type: "POST",
        context: this,
        success: this.ResetSuccess,
        error: this.GlobalError

    });

    return false;
}

WPADVERTS.Styling.Button.prototype.onSubmitClick = function( e ) {
    e.preventDefault();

    this.submit.addClass("disabled");
    this.spinner.addClass("atw-visible");

    var data = {
        action: "wpadverts-styling-save",
        param: this.form.data("param"),
        form: {}
    };

    jQuery.each(this.form.serializeArray(), function() {
        data.form[this.name] = this.value;
    });

    jQuery.ajax({
        url: adverts_admin_styling_lang.ajax,
        data: data,
        dataType: "json",
        type: "POST",
        context: this,
        success: this.SaveSuccess,
        error: this.GlobalError

    });

    return false;
}
 
WPADVERTS.Styling.Button.prototype.SaveSuccess = function( response ) {
    if( response.status != "1" ) {
        return this.GlobalError();
    }

    this.submit.removeClass("disabled");
    this.spinner.removeClass("atw-visible");
}

WPADVERTS.Styling.Button.prototype.ResetSuccess = function( response ) {
    if( response.status != "1" ) {
        return this.GlobalError();
    }

    this.el.find(".wp-picker-default").click();


    this.submit.removeClass("disabled");
    this.spinner.removeClass("atw-visible");


}

WPADVERTS.Styling.Button.prototype.GlobalError = function( response ) {

    this.submit.removeClass("disabled");
    this.spinner.removeClass("atw-visible");

    if( typeof response.error !== "undefined" && response.error.length > 0 ) {
        alert(response.error);
    }
    
}

WPADVERTS.Styling.ButtonColors = function( el, preview ) {
    this.color_text = el.find(".color-text");
    this.color_bg = el.find(".color-bg");
    this.color_border = el.find(".color-border");   

    this.preview = preview;


    this.color_text.wpColorPicker({ 
        change: jQuery.proxy( this.onTextColorChange, this )
    });    
    this.color_bg.wpColorPicker({ 
        change: jQuery.proxy( this.onBgColorChange, this )
    });    
    this.color_border.wpColorPicker({ 
        change: jQuery.proxy( this.onBorderColorChange, this )
    }); 

    this.setDefaults();
}

WPADVERTS.Styling.ButtonColors.prototype.onTextColorChange = function( event, ui ) {
    this.preview.css("color", ui.color.toString());
};

WPADVERTS.Styling.ButtonColors.prototype.onBgColorChange = function( event, ui ) {
    this.preview.css("background-color", ui.color.toString());
};

WPADVERTS.Styling.ButtonColors.prototype.onBorderColorChange = function( event, ui ) {
    this.preview.css("border-color", ui.color.toString());
};

WPADVERTS.Styling.ButtonColors.prototype.setDefaults = function() {

    this.preview.css("color", this.color_text.val());
    this.preview.css("background-color", this.color_bg.val());
    this.preview.css("border-color", this.color_border.val());    
};

WPADVERTS.Styling.Form = function( el, preview ) {
    this.el = el;
    this.preview = preview;

    this.form = el.find("form");
    this.palette = el.find(".palette");
    this.style = el.find(".style");
    this.shadow = el.find(".shadow");
    this.rounded = el.find(".rounded");
    this.border = el.find(".border");
    this.interline = el.find(".interline");
    this.spacing = el.find(".spacing");

    this.palette.on("change", jQuery.proxy(this.onPaletteChange, this));
    this.style.on("change", jQuery.proxy(this.onStyleChange, this));
    this.shadow.on("change", jQuery.proxy(this.onShadowChange, this));
    this.rounded.on("change", jQuery.proxy(this.onRoundedChange, this));
    this.border.on("change", jQuery.proxy(this.onBorderChange, this));
    this.interline.on("change", jQuery.proxy(this.onInterlineChange, this));
    this.spacing.on("change", jQuery.proxy(this.onSpacingChange, this));

    this.submit = el.find(".button-primary");
    this.reset = el.find(".wpa-button-settings-reset");
    this.spinner = el.find(".spinner");

    this.submit.on("click", jQuery.proxy(this.onSubmitClick, this));
    this.reset.on("click", jQuery.proxy(this.onResetClick, this));

    this.setDefaults();
};

WPADVERTS.Styling.Form.prototype.onSubmitClick = function( e ) {
    e.preventDefault();

    this.submit.addClass("disabled");
    this.spinner.addClass("atw-visible");

    var data = {
        action: "wpadverts-styling-save",
        param: this.form.data("param"),
        form: {}
    };

    jQuery.each(this.form.serializeArray(), function() {
        data.form[this.name] = this.value;
    });

    jQuery.ajax({
        url: adverts_admin_styling_lang.ajax,
        data: data,
        dataType: "json",
        type: "POST",
        context: this,
        success: this.SaveSuccess,
        error: this.GlobalError

    });

    return false;
}
 
WPADVERTS.Styling.Form.prototype.onResetClick = function( e ) {
    e.preventDefault();

    this.submit.addClass("disabled");
    this.spinner.addClass("atw-visible");

    var data = {
        action: "wpadverts-styling-reset",
        param: this.form.data("param")
    };

    jQuery.ajax({
        url: adverts_admin_styling_lang.ajax,
        data: data,
        dataType: "json",
        type: "POST",
        context: this,
        success: this.ResetSuccess,
        error: this.GlobalError

    });

    return false;
}

WPADVERTS.Styling.Form.prototype.SaveSuccess = function( response ) {
    if( response.status != "1" ) {
        return this.GlobalError();
    }

    this.submit.removeClass("disabled");
    this.spinner.removeClass("atw-visible");
}

WPADVERTS.Styling.Form.prototype.ResetSuccess = function( response ) {
    if( response.status != "1" ) {
        return this.GlobalError();
    }

    this.el.find(".wp-picker-default").click();


    this.submit.removeClass("disabled");
    this.spinner.removeClass("atw-visible");


}

WPADVERTS.Styling.Form.prototype.GlobalError = function( response ) {

    this.submit.removeClass("disabled");
    this.spinner.removeClass("atw-visible");

    if( typeof response.error !== "undefined" && response.error.length > 0 ) {
        alert(response.error);
    }
    
}

WPADVERTS.Styling.Form.prototype.setDefaults = function() {

    this.preview.addClass(this.palette.val());
    this.preview.addClass(this.style.val());
    this.preview.addClass(this.shadow.val());
    this.preview.addClass(this.rounded.find("option:selected").data("value"));
    this.preview.addClass(this.border.find("option:selected").data("value"));
    this.preview.addClass(this.interline.find("option:selected").data("value"));
    this.preview.addClass(this.spacing.find("option:selected").data("value"));
};

WPADVERTS.Styling.Form.prototype.removePreviewClass = function(index, item) {
    this.preview.removeClass(jQuery(item).attr("value"));
};

WPADVERTS.Styling.Form.prototype.removePreviewDataClass = function(index, item) {
    this.preview.removeClass(jQuery(item).data("value"));
};

WPADVERTS.Styling.Form.prototype.onPaletteChange = function() {
    jQuery.each(this.palette.find("option"), jQuery.proxy(this.removePreviewClass,this));
    this.preview.addClass(this.palette.val());
};

WPADVERTS.Styling.Form.prototype.onStyleChange = function() {
    jQuery.each(this.style.find("option"), jQuery.proxy(this.removePreviewClass,this));
    this.preview.addClass(this.style.val());
};

WPADVERTS.Styling.Form.prototype.onShadowChange = function() {
    jQuery.each(this.shadow.find("option"), jQuery.proxy(this.removePreviewClass,this));
    this.preview.addClass(this.shadow.val());
};

WPADVERTS.Styling.Form.prototype.onRoundedChange = function() {
    jQuery.each(this.rounded.find("option"), jQuery.proxy(this.removePreviewDataClass,this));
    this.preview.addClass(this.rounded.find("option:selected").data("value"));
};

WPADVERTS.Styling.Form.prototype.onBorderChange = function() {
    jQuery.each(this.border.find("option"), jQuery.proxy(this.removePreviewDataClass,this));
    this.preview.addClass(this.border.find("option:selected").data("value"));
};

WPADVERTS.Styling.Form.prototype.onInterlineChange = function() {
    jQuery.each(this.interline.find("option"), jQuery.proxy(this.removePreviewDataClass,this));
    this.preview.addClass(this.interline.find("option:selected").data("value"));
};

WPADVERTS.Styling.Form.prototype.onSpacingChange = function() {
    jQuery.each(this.spacing.find("option"), jQuery.proxy(this.removePreviewDataClass,this));
    this.preview.addClass(this.spacing.find("option:selected").data("value"));
};

jQuery(function($) {

    new WPADVERTS.Styling.Tabs( $( ".wpa-styling-tabs" ) );

    /* Primary Button */

    new WPADVERTS.Styling.Button( 
        $( ".wpa-style-primary-button" ),
        $( ".wpa-preview-button-primary" )
    );

    new WPADVERTS.Styling.ButtonColors(
        $( ".wpa-style-primary-button .wpa-colors-normal" ),
        $( ".wpa-preview-button-primary-normal" ),
    );

    new WPADVERTS.Styling.ButtonColors(
        $( ".wpa-style-primary-button .wpa-colors-hover" ),
        $( ".wpa-preview-button-primary-hover" ),
    );

    /* Secondary Button */

    new WPADVERTS.Styling.Button( 
        $( ".wpa-style-secondary-button" ),
        $( ".wpa-preview-button-secondary" )
    );

    new WPADVERTS.Styling.ButtonColors(
        $( ".wpa-style-secondary-button .wpa-colors-normal" ),
        $( ".wpa-preview-button-secondary-normal" ),
    );

    new WPADVERTS.Styling.ButtonColors(
        $( ".wpa-style-secondary-button .wpa-colors-hover" ),
        $( ".wpa-preview-button-secondary-hover" ),
    );

    /* Form */

    new WPADVERTS.Styling.Form(
        $(".wpa-style-form"), 
        $(".wpa-styling-form-preview .wpadverts-form") 
    );

});