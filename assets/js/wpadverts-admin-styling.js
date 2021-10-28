var WPADVERTS = WPADVERTS || {};

WPADVERTS.Styling = {
    
};

WPADVERTS.Styling.Button = function( el, preview ) {
    this.el = el;
    this.radius = el.find(".border-radius");
    this.width = el.find(".border-width");

    this.preview = preview;

    this.radius.on("change", jQuery.proxy(this.onRadiusChange, this));
    this.width.on("change", jQuery.proxy(this.onWidthChange, this));

    this.setDefaults();
}

WPADVERTS.Styling.Button.prototype.removePreviewClass = function(index, item) {
    this.preview.removeClass(jQuery(item).attr("value"));
}

WPADVERTS.Styling.Button.prototype.setDefaults = function() {
    this.preview.addClass(this.width.val());
    this.preview.addClass(this.radius.val());
};

WPADVERTS.Styling.Button.prototype.onRadiusChange = function( e ) {
    jQuery.each(this.radius.find("option"), jQuery.proxy(this.removePreviewClass,this));
    this.preview.addClass(e.target.value);
};

WPADVERTS.Styling.Button.prototype.onWidthChange = function( e ) {
    jQuery.each(this.width.find("option"), jQuery.proxy(this.removePreviewClass,this));
    this.preview.addClass(e.target.value);
};

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

jQuery(function($) {

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


});