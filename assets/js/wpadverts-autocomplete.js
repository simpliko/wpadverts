// Init global namespace
var WPADVERTS = WPADVERTS || {};

WPADVERTS.Autocomplete = function( item ) {
    this.options = {
        
        hierarchical: true,
        multi: true,
        search: true,
        waterfall: true,
        
        label_wrap_at: 3 ,
        search_min: 3,
        max_choices: 2,
        
        default_close: "save",
        
        save_as: "text",
        save_separator: ",",
        
        mobile_fullscreen: true,
        leaf_only: true,
        ignore_restricted: false
    };
    
    this.name = "";
    this.xhr = null;
    this.input = item;
    this.ui = {};
    this.checked = {};
    this.tree = {};
    
    
    this._firstLoad = true;
    
    this.name = this.input.name;
    var self = this;
    var data = jQuery(this.input).data();
    
    jQuery.each(Object.keys(this.options), function(index, item) {
        if( typeof data[item] !== 'undefined' ) {
            self.options[item] = data[item];
        }
    });
    
    jQuery(this.input).attr( "name", null );
    jQuery(this.input).on( "click", jQuery.proxy( this.OnClick, this ) );
    
    
    this.input.value = "";
    
    this.CreateUI();

    var ck = jQuery(this.input).data("checked");
    
    var unfold = [];
    
    jQuery.each(ck, function(i, item) {
        
        var tmpClass = "wpadverts-ac-tmp--" + self.name + "--" + item.value;
        if( jQuery( "." + tmpClass ).length === 0 ) {
            var hidden = jQuery("<input type='hidden' />");
            hidden.attr( "name", self.name + "[]" );
            hidden.attr( "class", tmpClass );
            hidden.attr( "value", item.value );
            //self.input.after(hidden);
            hidden.insertAfter(self.input);
        }
        
        var uinner = [];
        if(self.options.hierarchical) {
            jQuery.each(item.path, function(j, l) {
                uinner.push(l.v);
            });
            unfold.push(uinner);
        } else {
            self.ItemChecked(null, item);
        }
    });
    
    if(unfold.length>0) {
        this._unfold = unfold;
        this.ui.openclose.hide();
        this.ui.spinner.show();
        this.ui.inputwrap.addClass("wpadverts-autocomplete-pending");
        this.Load();
        this._firstLoad = false;
        
    }

    this.Close("save");
};

WPADVERTS.Autocomplete.prototype.OnClick = function(e) {
    e.preventDefault();
    
    if(this.ui.inputwrap.hasClass("wpadverts-autocomplete-pending")) {
        return;
    }
    
    this.input.blur();
    
    if(this.ui.box.is(":hidden")) {
        this.Open();
    } else {
        this.Close();
    }
};

WPADVERTS.Autocomplete.prototype.Open = function() {
    this.ui.box.css("width", jQuery(this.input).outerWidth()-1);
    this.ui.box.removeClass("wpadverts-ac-none");

    if(this.ui.search) {
        this.ui.search.focus();
    }

    if(this._firstLoad) {
        this.Load();
        this._firstLoad = false;
    }
};

WPADVERTS.Autocomplete.prototype.Close = function(type, e) {
    
    if(typeof e !== 'undefined') {
        e.preventDefault();
    }
    
    if(typeof type === 'undefined') {
        type = this.default_cancel;
    }
    
    if(type === "cancel") {
        jQuery.each(this.checked, jQuery.proxy(this.CloseCancel, this));
    } else {
        jQuery.each(this.checked, this.CloseSave, this);
    }
    
    this.ui.box.addClass("wpadverts-ac-none");
};

WPADVERTS.Autocomplete.prototype.CloseCancel = function(index, checked) {
    if(checked.GetPending() === true) {
        this.TagDispose(checked.data);
    }
};

WPADVERTS.Autocomplete.prototype.CloseSave = function(index, checked) {
    checked.SetPending(false);
};

WPADVERTS.Autocomplete.prototype.IsChecked = function(value) {
    if(typeof this.checked[value] !== 'undefined') {
        return true;
    }  else {
        return false;
    }
};

WPADVERTS.Autocomplete.prototype.Load = function() {
    if(this.options.hierarchical === false) {
        var em = jQuery("<em></em>");
        em.addClass("wpadverts-autocomplete-search-results-placeholder");
        em.text(adverts_autocomplete_lang.search_placeholder);
        
        this.ui.results.html(em);
        this.ui.results.show();
        return;
    }
    
    var data = {
        action: jQuery(this.input).data("ajax_action"),
        taxonomy: jQuery(this.input).data("taxonomy"),
        parent: 0
    };
    
    this.ui.preload.show();
    
    if( this.xhr !== null ) {
        this.xhr.abort();
    }
    
    this.xhr = jQuery.ajax({
        url: adverts_autocomplete_lang.ajaxurl,
        data: data,
        type: "get",
        dataType: "json",
        context: this,
        success: this.LoadSuccess,
        error: this.LoadError
    });
};

WPADVERTS.Autocomplete.prototype.LoadError = function( response ) {
    
    if(response.statusText === "abort") {
        return;
    }
    
    var err = "";
    if(response.responseStatus) {
        err += "<b>" + response.responseStatus + "</b>";
    }
    if(response.responseText) {
        err += response.responseText;
    }
    
    this.ui.preload.hide();
    this.ui.errormsg.html(err);
    this.ui.error.slideToggle("fast");
    
};

WPADVERTS.Autocomplete.prototype.LoadSuccess = function( response ) {
    var self = this;
    
    self.ui.tree.empty();
    self.ui.results.empty();
    
    jQuery.each(response, function(index, item) {
        if(self.options.hierarchical) {
            self.tree[item.value] = new WPADVERTS.Autocomplete.Row(self, item, 0);
            self.ui.tree.append(self.tree[item.value].GetRow());
        } else {
            var row = new WPADVERTS.Autocomplete.Row(self, item, -1);
            self.ui.results.append(row.GetRow());
        }
    });
    self.ui.preload.hide();
};

WPADVERTS.Autocomplete.prototype.KeyupLoadSuccess = function( response ) {
    var self = this;
    self.ui.results.empty();
    jQuery.each(response, function(index, item) {
        self.tree[item.value] = new WPADVERTS.Autocomplete.Row(self, item, -1);
        self.ui.results.append(self.tree[item.value].GetRow());
    });
    self.ui.preload.hide();
};

WPADVERTS.Autocomplete.prototype.CreateUI = function() {
    var $this = jQuery(this.input);
    
    this.ui.holder = jQuery("<div></div>");
    this.ui.holder.addClass("wpadverts-autocomplete-holder");

    this.ui.inputwrap = jQuery("<div></div>");
    this.ui.inputwrap.addClass("wpadverts-autocomplete-input-wrap");

    this.ui.spinner = jQuery("<span></span>");
    this.ui.spinner.addClass("adverts-icon-spinner");
    this.ui.spinner.addClass("animate-spin");
    this.ui.spinner.addClass("wpadverts-autocomplete-spinner");
    this.ui.spinner.hide();
    
    this.ui.openclose = jQuery("<span></span>");
    this.ui.openclose.addClass("adverts-icon-down-open");
    this.ui.openclose.addClass("wpadverts-autocomplete-openclose");
    if(this.options.hierarchical) {
        this.ui.inputwrap.append(this.ui.openclose);
        jQuery(this.ui.openclose).on( "click", jQuery.proxy( this.OnClick, this ) );
    }
    
    this.ui.inputwrap.append(this.ui.spinner);
    this.ui.holder.append(this.ui.inputwrap);

    if(this.options.mobile_fullscreen) {
        this.ui.holder.addClass("wpadverts-autocomplete-mobile-fullscreen");
    }
    if(this.options.hierarchical) {
        this.ui.holder.addClass("wpadverts-autocomplete-hierarchical");
    }
    if(this.options.multi) {
        this.ui.holder.addClass("wpadverts-autocomplete-multi")
    }

    this.ui.holder.on("click", function(event) {
       event.stopPropagation(); 
    });
    
    jQuery(window).on("click", jQuery.proxy(this.OutsideClick, this));

    $this.attr("autocomplete", "off");
    $this.after(this.ui.holder);
    //this.ui.holder.append($this);
    this.ui.inputwrap.append($this);

    this.ui.box = jQuery("<div></div>");
    this.ui.box.addClass("wpadverts-autocomplete-options");
    this.ui.box.addClass("wpadverts-ac-none");
    
    this.ui.wrap = jQuery("<div></div>");
    this.ui.wrap.addClass("wpadverts-autocomplete-search-wrap");
    
    this.ui.error = jQuery("<div></div>");
    this.ui.error.addClass("wpadverts-autocomplete-error");
    this.ui.error.append('<span class="wpadverts-autocomplete-error-msg-icon adverts-icon-attention"></span>');
    this.ui.error.hide();
    
    this.ui.errormsg = jQuery("<span></span>");
    this.ui.errormsg.addClass("wpadverts-autocomplete-error-msg");
    
    
    this.ui.errorclose = jQuery("<span></span>");
    this.ui.errorclose.addClass("wpadverts-autocomplete-error-close");
    this.ui.errorclose.addClass("adverts-icon-cancel");
    this.ui.errorclose.on("click", jQuery.proxy(this.ErrorClose, this));
    
    this.ui.error.append(this.ui.errormsg).append(this.ui.errorclose);
    
    if(this.options.search) {
        this.CreateSearch();
    }
    
    this.ui.checked_wrap = jQuery("<div></div>");
    this.ui.checked_wrap.addClass("wpadverts-autocomplete-checked-wrap");
    this.ui.checked_wrap.hide();
    
    var label = jQuery("<div></div>");
    label.addClass("wpadverts-autocompleted-checked-wrap-label");
    label.text(adverts_autocomplete_lang.selected);
    
    this.ui.checked = jQuery("<div></div>");
    this.ui.checked.addClass("wpadverts-autocomplete-checked");
    
    this.ui.checked_wrap.append(label).append(this.ui.checked);
    this.ui.wrap.append(this.ui.checked_wrap);
    
    this.ui.results = jQuery("<div></div>");
    this.ui.results.addClass("wpadverts-autocomplete-search-results");
    this.ui.results.hide();
    
    this.ui.tree = jQuery("<div></div>");
    this.ui.tree.addClass("wpadverts-autocomplete-search-results");
    
    this.ui.preload = jQuery("<div></div>");
    this.ui.preload.addClass("wpadverts-autocomplete-search-results-preload");
    this.ui.preload.append("<span class='adverts-icon-spinner animate-spin'></span>");
    this.ui.preload.hide();
    
    if(this.options.hierarchical) {
        this.ui.tree.show();
        this.ui.results.hide();
    } else {
        this.ui.tree.hide();
        this.ui.results.show();
    }
    
    this.ui.resultswrap = jQuery("<div></div>");
    this.ui.resultswrap.addClass("wpadverts-autocomplete-search-results-wrap");
    this.ui.resultswrap.append(this.ui.tree).append(this.ui.results).append(this.ui.preload);
    
    this.ui.box.append(this.ui.wrap).append(this.ui.error);
    this.ui.box.append(this.ui.resultswrap);
    this.ui.holder.append(this.ui.box);
    
    var buttons = jQuery("<div></div>");
    buttons.addClass("wpadverts-autocomplete-buttons");
    
    this.ui.button_ok = jQuery("<a></a>");
    this.ui.button_ok.attr("href", "#");
    this.ui.button_ok.addClass("wpadverts-autocomplete-button-ok");
    this.ui.button_ok.addClass("wpadverts-autocomplete-button");
    this.ui.button_ok.text(adverts_autocomplete_lang.ok);
    this.ui.button_ok.on("click", jQuery.proxy(this.Close, this, "save"));
    
    this.ui.button_cancel = jQuery("<a></a>");
    this.ui.button_cancel.attr("href", "#");
    this.ui.button_cancel.addClass("wpadverts-autocomplete-button-cancel");
    this.ui.button_cancel.addClass("wpadverts-autocomplete-button");
    this.ui.button_cancel.text(adverts_autocomplete_lang.cancel);
    this.ui.button_cancel.on("click", jQuery.proxy(this.Close, this, "cancel"));
    
    buttons.append(this.ui.button_ok).append(this.ui.button_cancel);
    
    this.ui.box.append(buttons);
    
    if(this.options.save_as === "text") {
        this.ui.hidden = jQuery("<input type='hidden' />");
        this.ui.hidden.attr("name", this.name);
        this.ui.box.append(this.ui.hidden);
    }
    
    this.HandleToolbar();
};

WPADVERTS.Autocomplete.prototype.ErrorClose = function() {
    this.ui.error.hide();
};

WPADVERTS.Autocomplete.prototype.OutsideClick = function() {
    if(this.ui.box.is(":visible")) {
        this.Close();
    }
};

WPADVERTS.Autocomplete.prototype.CreateSearch = function() {
    var wrap = jQuery("<div></div>");
    
    this.ui.search = jQuery("<input type='text' />");
    this.ui.search.attr("placeholder", adverts_autocomplete_lang.start_typing_here);
    this.ui.search.addClass("wpadverts-autocomplete-input-search");
    
    this.ui.search.on("keyup", jQuery.proxy(this.KeyUp, this));
    
    wrap.append(this.ui.search);
    
    this.ui.wrap.append(wrap);
};

WPADVERTS.Autocomplete.prototype.KeyUp = function(e) {

    var search = this.ui.search.val().trim();
    
    if(this.options.hierarchical && search.length < this.options.search_min) {
        this.ui.tree.show();
        this.ui.results.hide();
        return;
    }

    this.ui.tree.hide();
    this.ui.results.show();

    var data = {
        action: jQuery(this.input).data("ajax_action"),
        taxonomy: jQuery(this.input).data("taxonomy"),
        text: this.ui.search.val()
    };

    if( this.xhr !== null ) {
        this.xhr.abort();
    }

    this.ui.preload.show();

    this.xhr = jQuery.ajax({
        url: adverts_autocomplete_lang.ajaxurl,
        data: data,
        type: "get",
        dataType: "json",
        context: this,
        success: this.KeyupLoadSuccess,
        error: this.LoadError
    });
};

WPADVERTS.Autocomplete.prototype.HandleHidden = function() {
    if(typeof this.ui.hidden === 'undefined') {
        return;
    }
    
    var labels = [];
    this.ui.checked.find("input[type=hidden]").each(function(index, item) {
        labels.push(jQuery(item).val());
    });
    
    this.ui.hidden.val(labels.join(this.options.save_separator));
};

WPADVERTS.Autocomplete.prototype.GenerateLabel = function() {
    var labels = [];
    var label = "";
    this.ui.checked.find("input[type=hidden]").each(function(index, item) {
        labels.push(jQuery(item).data("label"));
    });
    
    if(labels.length >= this.options.label_wrap_at) {
        label = labels.length.toString() + " " + adverts_autocomplete_lang.selected;
    } else {
        label = labels.join(", ");
    }
    
    jQuery(this.input).val(label);
};

WPADVERTS.Autocomplete.prototype.HandleToolbar = function() {
    
    var tlen = Object.keys(this.checked).length;
    
    if(tlen === 0 ) {
        this.ui.checked_wrap.hide();
    } else {
        this.ui.checked_wrap.show();
    }
    
    if(!this.ui.search && tlen === 0) {
        this.ui.wrap.hide();
        
    } else {
        this.ui.wrap.show();
    }
};

WPADVERTS.Autocomplete.prototype.ItemChecked = function(target, data) {

    if(Object.keys(this.checked).length >= this.options.max_choices) {
        this.ui.errormsg.text(adverts_autocomplete_lang.max_choices.replace("%s", this.options.max_choices));
        this.ui.error.slideToggle("fast");
        jQuery(target).attr("checked", false);
        return;
    }

    this.checked[data.value] = new WPADVERTS.Autocomplete.Tag(this, data);
    
    if(this.ui.checked_wrap.is(":hidden")) {
        this.ui.checked_wrap.show();
    }
    this.checked[data.value].SetPending(true);
    this.ui.checked.append(this.checked[data.value].GetTag());

    this.checked[data.value].GetTag().fadeIn("fast");
    
    this.GenerateLabel();
    this.HandleToolbar();
    this.HandleHidden();
    
    this.ui.wrap.find("#"+"wpac__search__"+this.name + "__" + data.value ).attr("checked","checked").change();
};

WPADVERTS.Autocomplete.prototype.ItemUnchecked = function(target, data) {
    this.TagDispose(data, target);
};

WPADVERTS.Autocomplete.prototype.Check = function(values) {
    
};

WPADVERTS.Autocomplete.prototype.TagDispose = function(data, e) {

    if(typeof this.checked[data.value] === 'undefined') {
        return;
    } 

    this.checked[data.value].GetTag().remove();
    delete this.checked[data.value];
    this.GenerateLabel();
    this.HandleToolbar();
    this.HandleHidden();
    
    jQuery(".wpadverts-autocomplete-search-result-ckbox input[type='checkbox'][value='"+data.value+"']").attr("checked", false).change();
    jQuery(".wpadverts-autocomplete-search-result-ckbox span#"+this.name+"__"+data.value).hide();
    
    if(!this.options.multi) {
        this.Close("save");
    }
};

WPADVERTS.Autocomplete.prototype.TagDisposeAll = function() {
    var self = this;
    jQuery.each(self.checked, function(index, item) {
        self.TagDispose(item.data);
    });
};

WPADVERTS.Autocomplete.Tag = function(ac, data) {
    
    this.pending = false;
    this.data = data;
    
    this.dispose = jQuery("<span></span>");
    this.dispose.addClass("adverts-icon-cancel");
    this.dispose.addClass("wpadverts-autocomplete-tag-dispose");
    

    this.hidden = jQuery("<input type=\"hidden\" />");
    this.hidden.data( "label", data.label );
    this.hidden.val(data.value);
        
    if(ac.options.save_as === "array") {
        this.hidden.attr("name", ac.name + "[]" );
    }
    
    var path = [];
    jQuery.each(data.path, function(index, item) {
        path.push(item.t);
    });
    
    this.tag = jQuery("<div></div>");
    this.tag.addClass("wpadverts-ac-checked");
    this.tag.text(data.label);
    this.tag.attr("title", path.join( " / " ) );
    this.tag.append(this.dispose);
    this.tag.append(this.hidden);
    this.tag.hide();

    this.dispose.on("click", jQuery.proxy( ac.TagDispose, ac, data ) );
    
    // remove tmp value
    jQuery(".wpadverts-ac-tmp--" + ac.name + "--" + data.value ).remove();
};

WPADVERTS.Autocomplete.Tag.prototype.SetPending = function(isPending) {
    this.pending = isPending;
};

WPADVERTS.Autocomplete.Tag.prototype.GetPending = function() {
    return this.pending;
};

WPADVERTS.Autocomplete.Tag.prototype.GetTag = function() {
    return this.tag;
};

WPADVERTS.Autocomplete.Row = function(ac, item, level) {
    
    this.AC = ac;
    this.Level = level;
    this.Data = item;
    this.tree = {};
    
    this.wrap = jQuery("<div></div>");
    this.wrap.addClass("wpadverts-autocomplete-search-result-wrap");
    this.wrap.addClass("wpadverts-autocomplete-level-" + level.toString());
    
    this.inner = jQuery("<div></div>");
    this.inner.addClass("wpadverts-autocomplete-search-result-inner");
    this.inner.addClass("wpadverts-autocomplete-search-result-inner-load");
    this.inner.hide();
    
    this.inputrow = jQuery("<div></div>");
    this.inputrow.addClass("wpadverts-autocomplete-search-result-ckbox");
    
    this.row = jQuery("<div></div>");
    this.row.addClass("wpadverts-autocomplete-search-result");
    this.row.data( "value", item.value);
    this.row.append(this.inputrow);
    
    if(this.Level >= 0) {
        this.Type = "tree";
        this.InitArrows(item);
    } else {
        this.Type = "search";
    }
    

    this.labelrow = jQuery("<label></label>");
    this.labelrow.addClass("wpadverts-autocomplete-search-result-label");
    this.labelrow.attr("for", "wpac__"+this.Type+"__"+this.AC.name + "__" + item.value ) ;
    this.labelrow.append(item.html);

    if((item.is_restricted && !this.AC.options.ignore_restricted) || ( this.AC.options.leaf_only && item.has_children ) ) {
        this.Restrict(item, this.AC.IsChecked(item.value));
    } else if(this.AC.options.multi) {
        this.InitCheckbox(item, this.AC.IsChecked(item.value));
    } else {
        this.InitSelect(item, this.AC.IsChecked(item.value));
    }
    
    if(this.AC.options.waterfall && this.checkbox && this.Level >= 0) {
        this.checkbox.on("change", jQuery.proxy(this.Waterfall, this, item));
    }
     
    if(this.Level === -1) {
        //this.checkbox.on("change", jQuery.proxy(this.ResultCheck, this, item));
    }
    
    this.wrap.append(this.row).append(this.inner);
    this.row.append(this.labelrow);
    
    this.MaybeUnfold();
};

WPADVERTS.Autocomplete.Row.prototype.MaybeUnfold = function(item, checked) {
    
    if(typeof this.AC._unfold === 'undefined') {
        return;
    }
    
    var self = this;
    
    jQuery.each(self.AC._unfold, function(index, item) {


        if(typeof self.AC._unfold[index] !== 'undefined' && self.AC._unfold[index][0] != self.Data.value ) {
            return;
        }

        var click = self.AC._unfold[index].shift();

        if(self.AC._unfold[index].length === 0) {

            if(self.checkbox) {
                self.checkbox.attr("checked", "checked").change();
            } else if(self.checkmark) {
                self.Selected();
            }

            self.AC.ui.spinner.hide();
            self.AC.ui.openclose.show();
            self.AC.ui.inputwrap.removeClass("wpadverts-autocomplete-pending");
            self.AC.Close("save");
            
        } else {
            self.arrow.open.click();
        }

    });

};

WPADVERTS.Autocomplete.Row.prototype.Restrict = function(item, checked) {
    this.checkbox = jQuery("<input type='checkbox' />");
    this.checkbox.val(item.value);
    this.checkbox.attr("id", "wpac__"+this.Type+"__"+this.AC.name + "__" + item.value );
    this.checkbox.attr("disabled", "disabled");
    
    this.inputrow.append(this.checkbox);
    this.labelrow.on("click", jQuery.proxy(this.RestrictClick, this));
    
    if(!this.AC.options.multi) {
        this.inputrow.hide();
    }
};

WPADVERTS.Autocomplete.Row.prototype.RestrictClick = function() {
    if(typeof this.arrow !== 'undefined' && this.arrow.open.is(":visible")) {
        this.ArrowOpenClick();
    } else if(typeof this.arrow !== 'undefined' && this.arrow.close.is(":visible")) {
        this.ArrowCloseClick();
    }
};

WPADVERTS.Autocomplete.Row.prototype.InitCheckbox = function(item, checked) {
    this.checkbox = jQuery("<input type='checkbox' />");
    this.checkbox.val(item.value);
    this.checkbox.attr("id", "wpac__"+this.Type+"__"+this.AC.name + "__" + item.value );
    if(checked) {
        this.checkbox.attr("checked", "checked");
    }

    this.inputrow.append(this.checkbox);
    
    
    
    this.checkbox.on( "change", jQuery.proxy( this.CheckboxChanged, this ) );
};

WPADVERTS.Autocomplete.Row.prototype.CheckboxChanged = function(e) {
    var checked = jQuery(e.target).is(":checked");
    
    if(checked) {
        this.AC.ItemChecked(e.target, this.Data);
    } else {
        this.AC.ItemUnchecked(e.target, this.Data);
    }
};

WPADVERTS.Autocomplete.Row.prototype.InitSelect = function(item, checked) {
    
    this.is_checked = false;
    this.checkmark = jQuery("<span></span>");
    this.checkmark.attr("id", this.AC.name + "__" + item.value );
    this.checkmark.addClass("adverts-icon-ok");
    this.checkmark.css("color", "green");
    this.checkmark.hide();
    
    if(checked) {
        this.checkmark.show();
    }


    this.inputrow.append(this.checkmark);
    
    this.row.append(this.inputrow);
    
    this.labelrow.on("click", jQuery.proxy(this.Selected, this));
};

WPADVERTS.Autocomplete.Row.prototype.Selected = function(e) {
    
    if(!this.checkmark.is(":visible")) {
        this.checkmark.fadeIn("fast");
        this.AC.TagDisposeAll();
        this.AC.ItemChecked(e, this.Data);
        this.AC.Close("save");
    } else {
        this.checkmark.hide();
        this.AC.ItemUnchecked(e, this.Data);
        this.AC.Close("save");
    }
};


WPADVERTS.Autocomplete.Row.prototype.Waterfall = function(target, data) {
    if(this.checkbox.is(":checked")) {
        this.WaterfallCheck(target, data);
    } else {
        this.WaterfallUncheck(target, data);
    }
};

WPADVERTS.Autocomplete.Row.prototype.WaterfallCheck = function(target, data) {
    var self = this;
    jQuery.each(this.tree, function(index, item) {
        if(item.checkbox.is(":checked")) {
            self.AC.TagDispose(item.Data);
        } else {
            
        }
        item.checkbox.attr("checked", "checked");
        item.checkbox.attr("disabled", "disabled");
        item.WaterfallCheck();
    });
};

WPADVERTS.Autocomplete.Row.prototype.WaterfallUncheck = function(target, data) {
    jQuery.each(this.tree, function(index, item) {
        item.checkbox.attr("checked", false).attr("disabled", false);
        item.WaterfallUncheck();
    });
};

WPADVERTS.Autocomplete.Row.prototype.InitArrows = function(item) {
    
    this.arrow = { };
    this.arrow.state = "closed";
    this.arrow.open = jQuery("<span></span>");
    this.arrow.open.addClass("adverts-icon-right-open");
    this.arrow.open.addClass("wpadverts-autocomplete-arrow-open");
    this.arrow.open.attr("title", adverts_autocomplete_lang.open);
    this.arrow.close = jQuery("<span></span>");
    this.arrow.close.addClass("adverts-icon-down-open");
    this.arrow.close.addClass("wpadverts-autocomplete-arrow-close");
    this.arrow.close.attr("title", adverts_autocomplete_lang.close);
    this.arrow.close.hide();
    this.arrow.minus = jQuery("<span></span>");
    this.arrow.minus.addClass("adverts-icon-minus");
    this.arrow.minus.addClass("wpadverts-autocomplete-arrow-leaf")
    this.arrow.minus.css("opacity", "0.1");
    //this.arrow.minus.css("visibility", "hidden");
    this.arrow.minus.hide();

    var arrowrow = jQuery("<div></div>");
    arrowrow.append(this.arrow.open);
    arrowrow.append(this.arrow.close);
    arrowrow.append(this.arrow.minus);

    if(!item.has_children) {
        this.arrow.open.hide();
        this.arrow.minus.show();
    }

    if(!this.AC.options.multi) {
        this.arrow.minus.hide();
    }
    
    this.arrow.open.on( "click", jQuery.proxy( this.ArrowOpenClick, this ) );
    this.arrow.close.on( "click", jQuery.proxy( this.ArrowCloseClick, this ) );
    
    this.row.append(arrowrow)
};

WPADVERTS.Autocomplete.Row.prototype.ArrowOpenClick = function() {
    
    this.arrow.state = "open";
    this.arrow.open.hide();
    this.arrow.close.show();
    
    if(this.inner.hasClass("wpadverts-autocomplete-search-result-inner-load")) {
        
        var loadDiv1 = jQuery("<div></div>");
        loadDiv1.addClass("wpadverts-autocomplete-search-result");
        var loadSpan = jQuery("<span></span>");
        loadSpan.addClass("adverts-icon-spinner");
        loadSpan.addClass("animate-spin");
        var loadDiv2 = jQuery("<div></div>");
        loadDiv2.addClass("wpadverts-autocomplete-level-"+ (this.Level+1).toString());
        
        loadDiv1.append(loadSpan).append( "Loading ..." );
        
        loadDiv2.append(loadDiv1);
        
        this.inner.show();
        this.inner.html(loadDiv2);
        // Ajax load results
        
        var data = {
            action: jQuery(this.AC.input).data("ajax_action"),
            taxonomy: jQuery(this.AC.input).data("taxonomy"),
            parent: this.row.data("value")
        };

        jQuery.ajax({
            url: adverts_autocomplete_lang.ajaxurl,
            data: data,
            type: "get",
            dataType: "json",
            context: this,
            success: this.ArrowOpenSuccess,
            error: jQuery.proxy(this.AC.LoadError, this.AC)
        });
            
    } else {
        this.inner.show();
    }
};

WPADVERTS.Autocomplete.Row.prototype.ArrowOpenSuccess = function(response) {
    var self = this;
    this.inner.removeClass("wpadverts-autocomplete-search-result-inner-load");
    this.inner.empty();
    
    jQuery.each(response, function(index, item) {
        self.tree[item.value] = new WPADVERTS.Autocomplete.Row(self.AC, item, self.Level+1);
        
        if(self.AC.options.waterfall && self.checkbox && self.checkbox.is(":checked")) {
            self.tree[item.value].checkbox.attr("checked", "checked").attr("disabled", "disabled");
        }
        
        self.inner.append(self.tree[item.value].GetRow());
    });
    
    if(typeof self.AC._unfold === 'undefined') {
        return;
    }
    
    var total = 0;
    jQuery.each(self.AC._unfold, function(index, item) {
        total += item.length;
    });
    
    if(total === 0) {
        this.AC.ui.spinner.hide();
        this.AC.ui.openclose.show();
        this.AC.ui.inputwrap.removeClass("wpadverts-autocomplete-pending");
    }
};

WPADVERTS.Autocomplete.Row.prototype.ArrowCloseClick = function() {
    this.arrow.state = "closed";
    this.arrow.open.show();
    this.arrow.close.hide();
    this.inner.hide();
    
};

WPADVERTS.Autocomplete.Row.prototype.GetRow = function() {
    return this.wrap;
};

jQuery(function($) {
    $("input.wpadverts-autocomplete").each(function(index, item) {
        var AC = new WPADVERTS.Autocomplete( item );
    });
});

jQuery(function($) {
    return;
    
    if($("input.wpadverts-autocomplete").length == 0) {
        return;
    }
    
    $("input.wpadverts-autocomplete").each(function(index, item) {
        var $this = $(item);
        var holder = $("<div></div>");
        holder.addClass("wpadverts-autocomplete-holder");

        $this.attr("autocomplete", "off");
        $this.after(holder);
        holder.append($this);
        
        var suggest = $("<div></div>");
        suggest.addClass("wpadverts-autocomplete-options");
        holder.append(suggest);
        

        $this.keyup(function(e) {
            
            var $this = $(this);
            

            var caret = adverts_autocomplete_caret_position($this.get(0));
            var text = $.trim(adverts_autocomplete_extract_text($this.val(), caret, $this.data("separator")));
            
            if(parseInt($this.data("start")) > text.length) {
                return;
            }
            
            var data = {
                action: $this.data("ajax_action"),
                taxonomy: $this.data("taxonomy"),
                text: text
            }
            
            if(WPAdvertsAcRequest) {
                WPAdvertsAcRequest.abort();
            }
            
            var $parent = $this.parent();
            var suggest = $parent.find(".wpadverts-autocomplete-options");
            
            var span = $("<span></span>");
            span.addClass("adverts-loader");
            span.addClass("adverts-icon-spinner");
            span.addClass("animate-spin");
            span.css("display", "inline");
            span.css("font-size", "32px");

            suggest.html(span);
            suggest.css("width", $this.outerWidth()-1);
            suggest.show();
            
            WPAdvertsAcRequest = $.ajax(adverts_autocomplete_lang.ajaxurl, {
                input: $this,
                data: data,
                dataType: 'json',
                type: 'post',
                success: function(response) {
                    
                    var $parent = $this.parent();
                    var suggest = $parent.find(".wpadverts-autocomplete-options");
                    
                    suggest.empty();
                    
                    if(response.length == 0) {
                        var sitem = $("<div></div>");
                        sitem.addClass("wpadverts-autocomplete-none");
                        sitem.html(adverts_autocomplete_lang.no_results);
                        suggest.append(sitem);
                    } else {
                        $.each(response, function(index, item) {
                            var sitem = $("<div></div>");
                            sitem.addClass("wpadverts-autocomplete-item");
                            sitem.data("value", item.value);
                            sitem.html(item.html);
                            sitem.click(function(e) {
                                var holder = $(this).closest(".wpadverts-autocomplete-holder");
                                var input = holder.find("input.wpadverts-autocomplete");
                                var value = $(this).data("value");

                                adverts_autocomplete_update_text(input, value)
                                


                                holder.find(".wpadverts-autocomplete-options").hide();

                            });
                            suggest.append(sitem);
                        });
                    }
                    
                    WPAdvertsAcRequest = null;
                }
            });
        });

    });
    
    $(document).mouseup(function(e) {
        var container = $(".wpadverts-autocomplete-options");

        if (container.length == 0) {
            return;
        }

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.hide();
        }
    });
});

function adverts_autocomplete_extract_text(text, caret, sep) {
    var parsed = "";
    var cut = 0;
    
    parsed = text.substr(0, caret);
    cut = parsed.lastIndexOf(sep);
    
    if(cut >= 0) {
        parsed = parsed.substr(cut + sep.length);
    }
    
    return parsed;
}

//console.log("New York: " + adverts_autocomplete_extract_text("New York", 8, "; "));
//console.log("Chicago: " + adverts_autocomplete_extract_text("New York; Chicago", 17, "; "));
//console.log("Chicago: " + adverts_autocomplete_extract_text("New York; Chicago", 15, "; "));
//console.log("Kraków: " + adverts_autocomplete_extract_text("New York; Chicago; Kraków", 25, "; "));
//console.log("New York: " + adverts_autocomplete_extract_text("New York; Chicago; Kraków", 8, "; "));

function adverts_autocomplete_update_text(input, value) {
    
    var sep = "; ";
    var caret = adverts_autocomplete_caret_position(input.get(0));
    var parsed = "";
    
    var text = input.val();
    var text1 = text.substr(0, caret);
    var text2 = text.substr(caret);
    
    var cut1 = text1.lastIndexOf(sep);
    var cut2 = text2.indexOf(sep);
    
    if(cut1 >= 0) {
        parsed += text1.substr(0, cut1) + sep;
    }
    
    parsed += value + sep;
    
    if(cut2 >= 0) {
        parsed += text2.substr(cut2+sep.length);
    }
    
    input.val(parsed);
    input.focus();
}

function adverts_autocomplete_caret_position (oField) {

  // Initialize
  var iCaretPos = 0;

  // IE Support
  if (document.selection) {

    // Set focus on the element
    oField.focus();

    // To get cursor position, get empty selection range
    var oSel = document.selection.createRange();

    // Move selection start to 0 position
    oSel.moveStart('character', -oField.value.length);

    // The caret position is selection length
    iCaretPos = oSel.text.length;
  }

  // Firefox support
  else if (oField.selectionStart || oField.selectionStart == '0')
    iCaretPos = oField.selectionStart;

  // Return results
  return iCaretPos;
}

var WPAdvertsAcRequest = null;