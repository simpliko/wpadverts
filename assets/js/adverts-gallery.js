// Init global namespace
var WPADVERTS = WPADVERTS || {};

WPADVERTS.File = {
    Registered: [],
    RemoveItem: function(id) {
        jQuery.each(WPADVERTS.File.Registered, function(i, item) {
            if(typeof item.Item[id] !== "undefined") {
                delete item.Item[id];
                item.SortableUpdate();
            }
        });
    }
};

WPADVERTS.File.Uploader = function(setup) {
    var $ = jQuery;
    
    this.PostID = null;
    
    if($(setup.conf.post_id_input).val()) {
        this.PostID = $(setup.conf.post_id_input).val();
    }
    
    this.Item = {};
    this.Browser = new WPADVERTS.File.Browser(this);
    //this.Browser.browser.find(".wpadverts-file-pagi-next").on("click", jQuery.proxy(this.Browser.NextClicked, this));
    
    
    
    this.setup = setup;
    this.ui = $("#"+setup.init.container);
    this.sortable = this.ui.find(".adverts-gallery-uploads");
    this.engine = new WPADVERTS.File.Uploader.Plupload();
    
    var $this = this;
    jQuery.each(setup.data, function(index, result) {
        var file = { id: "adverts-file-" + result.attach_id };
        $this.FileAdded(null, file);
        $this.FileUploaded(file, result);
        var x = 0;
    });
    
    this.sortable.sortable({
        update: jQuery.proxy(this.SortableUpdate, this)
    });
    
    this.Plupload(setup.init);
    
};

WPADVERTS.File.Uploader.prototype.GetKeys = function() {
    var keys = [];
    keys.fill(0, 0, this.Item.length);
    
    jQuery.each(this.Item, function(index, item) {
        keys[item.container.index()] = item.result.attach_id;
    });
    
    return keys;
};

WPADVERTS.File.Uploader.prototype.SortableUpdate = function(e) {
    var keys = this.GetKeys();

    if(typeof e !== "undefined") {
        this.ui.find(".adverts-gallery-upload-update.adverts-icon-spinner.animate-spin").fadeIn();
    }

    jQuery.ajax({
        url: adverts_gallery_lang.ajaxurl,
        context: this,
        type: "post",
        dataType: "json",
        data: {
            action: "adverts_gallery_update_order",
            _ajax_nonce: this.setup.init.multipart_params._ajax_nonce,
            post_id: this.PostID,
            ordered_keys: JSON.stringify(keys)
        },
        success: jQuery.proxy(this.SortableUpdateSuccess, this)
    });
};

WPADVERTS.File.Uploader.prototype.SortableUpdateSuccess = function(response) {
    if (response.result == 1) {
        this.ui.find(".adverts-gallery-upload-update.adverts-icon-spinner.animate-spin").fadeOut();
    } else {
        alert(response.error);
    }
};

WPADVERTS.File.Uploader.prototype.FileAdded = function(container, file) {
    var c = jQuery("<div></div>").addClass("adverts-gallery-upload-item").attr("id", file.id);
    var init = {
        _ajax_nonce: this.setup.init.multipart_params._ajax_nonce
    };
    
    this.Item[file.id] = new WPADVERTS.File.Singular(file, c, init);
    this.Item[file.id].SetBrowser(this.Browser);
    this.Item[file.id].render();
    
    this.ui.find(".adverts-gallery-uploads").append(c);
};

WPADVERTS.File.Uploader.prototype.FileUploaded = function(file, result) {
    this.Item[file.id].setResult(result);
    
    this.Item[file.id].render();
};

WPADVERTS.File.Uploader.prototype.Plupload = function(init) {
    // create the uploader and pass the config from above
    this.uploader = new plupload.Uploader(init);

    // checks if browser supports drag and drop upload, makes some css adjustments if necessary
    this.uploader.bind('Init', jQuery.proxy(this.engine.Init, this));
    this.uploader.init();
    this.uploader.bind("BeforeUpload", jQuery.proxy(this.engine.BeforeUpload, this));
    this.uploader.bind('FilesAdded', jQuery.proxy(this.engine.FilesAdded, this));
    this.uploader.bind('FileUploaded', jQuery.proxy(this.engine.FileUploaded, this));
};

WPADVERTS.File.Uploader.Plupload = function(init) {
    // do nothing ...
};

WPADVERTS.File.Uploader.Plupload.prototype.getUploader = function() {
    return this.uploader;
};

WPADVERTS.File.Uploader.Plupload.prototype.Init = function(up) {
    if(up.features.dragdrop) {
        this.ui.addClass('drag-drop');
        this.ui.find('.adverts-gallery').bind('dragover.wp-uploader', function(){ 
            this.ui.addClass('drag-over'); 
        });
        this.ui.find('.adverts-drag-drop-area').bind('dragleave.wp-uploader, drop.wp-uploader', function(){
            this.ui.removeClass('drag-over'); 
        });
    }else{
        this.ui.removeClass('drag-drop');
        this.ui.find('.adverts-drag-drop-area').unbind('.wp-uploader');
    }
};

WPADVERTS.File.Uploader.Plupload.prototype.BeforeUpload = function(up,file) {
    up.settings.multipart_params.post_id = this.PostID;
};

WPADVERTS.File.Uploader.Plupload.prototype.FilesAdded = function(up, files){
    jQuery.each(files, jQuery.proxy(this.engine.FileAdded, this), up);

    up.refresh();
    up.start();
};

WPADVERTS.File.Uploader.Plupload.prototype.FileAdded = function(index, file) {
    var hundredmb = 100 * 1024 * 1024;
    var up = this.uploader;
    var max = parseInt(up.settings.max_file_size, 10);
    
    if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
        // file size error?
    } else {
        // a file was added, you may want to update your DOM here...
        //adverts_upload_files_added(up.settings.container, file);
        this.FileAdded(up.settings.container, file);
    }
};

WPADVERTS.File.Uploader.Plupload.prototype.FileUploaded = function(up, file, response) {
    var result = jQuery.parseJSON(response.response);

    if( this.PostID === null ) {
        this.PostID = result.post_id;
    }

    this.FileUploaded(file, result);
    this.SortableUpdate();
};

WPADVERTS.File.Singular = function(file, container, init) {
    this.file = file;
    this.container = container;
    this.init = init;
    this.browser = null;
    
    this.result = null;
    this.spinner = null;

    this.button = {
        edit: null,
        remove: null,
    };
};

WPADVERTS.File.Singular.prototype.SetBrowser = function(browser) {
    this.browser = browser;
};

WPADVERTS.File.Singular.prototype.render = function() {
    var template = wp.template( "wpadverts-uploaded-file" );
    var $ = jQuery;
    var data = {
        file: this.file,
        result: this.result
    };
    
    var tpl = template(data);
    var html = $(tpl);
    
    this.container.html(html)
    
    if(this.result) {
        if(typeof this.result.error !== "undefined") {
            this.container.on("click", jQuery.proxy(this.Dispose, this));
        } else {
            this.button.edit = this.container.find(".adverts-button-edit");
            this.button.remove = this.container.find(".adverts-button-remove");

            this.spinner = this.container.find(".adverts-loader");
            this.spinner.hide();
            
            this.button.edit.on("click", jQuery.proxy(this.EditClicked, this));
            this.button.remove.on("click", jQuery.proxy(this.RemoveClicked, this));
        }
    } 
};

WPADVERTS.File.Singular.prototype.Dispose = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    var fileId = this.file.id;
    
    this.container.fadeOut("fast", function() {
        jQuery(this).remove();
        WPADVERTS.File.RemoveItem(fileId);
    });
};

WPADVERTS.File.Singular.prototype.EditClicked = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }

    this.browser.Open();
    this.browser.Render(this.result);
    this.browser.UpdateNavigation();
};

WPADVERTS.File.Singular.prototype.RemoveClicked = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.spinner.css("display", "block");

    jQuery.ajax({
        url: adverts_gallery_lang.ajaxurl,
        context: this,
        type: "post",
        dataType: "json",
        data: {
            action: "adverts_gallery_delete",
            _ajax_nonce: this.init._ajax_nonce,
            post_id: this.result.post_id,
            attach_id: this.result.attach_id
        },
        success: jQuery.proxy(this.RemoveClickedSuccess, this)
    });            
};

WPADVERTS.File.Singular.prototype.RemoveClickedSuccess = function(response) {
    if(response.result == 1) {
        this.Dispose();
    } else {
        this.spinner.hide();
        alert(response.error);
    }
};

WPADVERTS.File.Singular.prototype.setResult = function(result) {
    this.result = result;
};

WPADVERTS.File.Singular.prototype.Uploaded = function() {
    
};

WPADVERTS.File.Browser = function(uploader) {
    this.file = null;
    this.uploader = uploader;
    
    var template = wp.template( "wpadverts-browser" );
    var compiled = template({modal_id:"xxx"});
    var html = jQuery(compiled);
    
    html.find(".wpadverts-overlay-close").on("click", jQuery.proxy(this.Close, this));
    html.find(".wpadverts-file-pagi-prev").on("click", jQuery.proxy(this.PrevClicked, this))
    html.find(".wpadverts-file-pagi-next").on("click", jQuery.proxy(this.NextClicked, this))
    
    this.browser = html;
    this.browser.hide();

    jQuery("body").append(this.browser);
};

WPADVERTS.File.Browser.prototype.SetFile = function(file) {
    this.file = file;
};

WPADVERTS.File.Browser.prototype.GetNavigation = function() {
    var keys = this.uploader.GetKeys();
    var index = keys.indexOf(this.file.attach_id);
    
    var prev_id = false;
    var next_id = false;
    
    if(index > 0) {
        prev_id = keys[index-1];
    } 
    
    if(index+1 < keys.length) {
        next_id = keys[index+1];
    } 
    
    return {
        prev_id: prev_id,
        next_id: next_id
    };
}

WPADVERTS.File.Browser.prototype.UpdateNavigation = function() {
    var navi = this.GetNavigation();

    if(navi.prev_id) {
        this.browser.find(".wpadverts-file-pagi-prev").removeClass("wpadverts-navi-disabled");
    } else {
        this.browser.find(".wpadverts-file-pagi-prev").addClass("wpadverts-navi-disabled");
    }
    
    if(navi.next_id) {
        this.browser.find(".wpadverts-file-pagi-next").removeClass("wpadverts-navi-disabled");
    } else {
        this.browser.find(".wpadverts-file-pagi-next").addClass("wpadverts-navi-disabled");
    }
};

WPADVERTS.File.Browser.prototype.Open = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
        
    
    this.browser.show();
};

WPADVERTS.File.Browser.prototype.Close = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    this.browser.hide();
};

WPADVERTS.File.Browser.prototype.NextClicked = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }

    var navi = this.GetNavigation();
    var next = null;
    
    if(navi.next_id === false) {
        return;
    }
    
    jQuery.each(this.uploader.Item, function(i, item) {
        if(item.result.attach_id == navi.next_id) {
            next = i;
        }
    });

    this.Render(this.uploader.Item[next].result);
    this.UpdateNavigation();
};

WPADVERTS.File.Browser.prototype.PrevClicked = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }

    var navi = this.GetNavigation();
    var prev = null;
    
    if(navi.prev_id === false) {
        return;
    }
    
    jQuery.each(this.uploader.Item, function(i, item) {
        if(item.result.attach_id == navi.prev_id) {
            prev = i;
        }
    });

    this.Render(this.uploader.Item[prev].result);
    this.UpdateNavigation();
};

WPADVERTS.File.Browser.prototype.Render = function(result) {
    this.SetFile(result);
    
    var template = wp.template( "wpadverts-browser-attachment-view" );
    var $ = jQuery;
    var data = {
        file: this.file
    };
    
    var tpl = template(data);
    var html = $(tpl);
    
    html.find(".wpadverts-image-sizes").on("change", jQuery.proxy(this.ImageSizeChanged, this));
    html.find(".adverts-upload-modal-update").on("click", jQuery.proxy(this.UpdateDescription, this));
    
    this.element = {
        spinner: html.find(".adverts-loader.animate-spin"),
        input: {
            featured: html.find("input[name='adverts_featured']"),
            caption: html.find("input[name='adverts_caption']"),
            content: html.find("textarea[name='adverts_content']")
        }
    };
    
    if(html.find(".wpadverts-attachment-edit-image").length > 0) {
        this.element.input.edit = html.find(".wpadverts-attachment-edit-image");
        this.element.input.edit.on("click", jQuery.proxy(this.EditImage, this));
    }
    
    if(html.find(".wpadverts-attachment-create-image").length > 0) {
        this.element.input.edit = html.find(".wpadverts-attachment-create-image");
        this.element.input.edit.on("click", jQuery.proxy(this.CreateImage, this));
    }
    
    this.browser.find(".wpadverts-attachment-details").html(html);
    this.browser.find(".wpadverts-attachment-details").find(".wpadverts-image-sizes").change();
    
    if(this.imageSize) {
        this.browser.find(".wpadverts-image-sizes option[value='"+this.imageSize+"']").prop("selected", true);
        this.browser.find(".wpadverts-image-sizes").change();
    }
    
    this.imageSize = null;

};

WPADVERTS.File.Browser.prototype.EditImage = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.imageSize = this.browser.find(".wpadverts-image-sizes option:selected").val();
    this.actionType = "edit";
    
    this.history = [];
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.CreateImage = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.imageSize = this.browser.find(".wpadverts-image-sizes option:selected").val();
    this.actionType = "create";
    
    this.history = [];
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.ImageLoad = function() {
    
    this.jcrop = null;
    this.crop = null;
    this.input = {
        
    };
    
    var imageSize = this.imageSize;
    if(this.actionType === "create") {
        imageSize = null;
    }
    
    var template = wp.template( "wpadverts-browser-attachment-image" );
    var $ = jQuery;
    var data = {
        file: this.file,
        size: imageSize,
        history: JSON.stringify(this.history)
    };
    
    var tpl = template(data);
    var html = $(tpl);
    
    var jcrop = html.find(".wpadverts-image-sizes option:selected");
    var jopt = {
        aspectRatio: jcrop.data("ratio"),
        //minSize: [jcrop.data("width"), jcrop.data("height")],
        //maxSize: [jcrop.data("width"), jcrop.data("height")],
        //setSelect: [0, 0, jcrop.data("width"), jcrop.data("height")],
        onSelect: jQuery.proxy(this.CropSelected, this),
        onChange: jQuery.proxy(this.CropSelected, this),
        onRelease: jQuery.proxy(this.CropReleased, this)
    };
    
    this.image = html.find("#wpadverts-image-crop");
    this.jcrop = this.image.Jcrop(jopt);
    

    html.find(".adverts-image-action-crop").on("click", jQuery.proxy(this.ImageCrop, this));
    html.find(".adverts-image-action-rotate-ccw").on("click", jQuery.proxy(this.RotateCCW, this));
    html.find(".adverts-image-action-rotate-cw").on("click", jQuery.proxy(this.RotateCW, this));
    html.find(".adverts-image-action-flip-h").on("click", jQuery.proxy(this.ImageFlipH, this));
    html.find(".adverts-image-action-flip-v").on("click", jQuery.proxy(this.ImageFlipV, this));
    html.find(".adverts-image-action-undo").on("click", jQuery.proxy(this.ImageUndo, this));
    html.find(".adverts-image-action-save").on("click", jQuery.proxy(this.ImageSave, this));
    html.find(".adverts-image-action-cancel").on("click", jQuery.proxy(this.ImageCancel, this));
    html.find(".adverts-image-action-restore").on("click", jQuery.proxy(this.ImageRestore, this));
    
    html.find(".adverts-image-scale-width").on("keyup", jQuery.proxy(this.KeyWidth, this));
    html.find(".adverts-image-scale-height").on("keyup", jQuery.proxy(this.KeyHeight, this));
    html.find(".adverts-image-action-scale").on("click", jQuery.proxy(this.ImageScale, this));
    
    this.input.width = html.find(".adverts-image-scale-width");
    this.input.height = html.find(".adverts-image-scale-height");
    
    if(this.history.length == 0) {
        html.find(".adverts-image-action-undo").css("opacity", "0.5");
    }
    
    html.find(".adverts-image-action-crop").css("opacity", "0.5");
    
    this.browser.find(".wpadverts-attachment-details").html(html);
};

WPADVERTS.File.Browser.prototype.ImageSizeChanged = function(e) {
    var s = this.browser.find(".wpadverts-image-sizes option:selected");

    this.browser.find(".adverts-image-preview").hide();
    this.browser.find(".adverts-image-preview.adverts-image-preview-"+s.val()).fadeIn("fast");
    this.browser.find(".adverts-icon-size-explain-desc").text(s.data("explain"));
    
};

WPADVERTS.File.Browser.prototype.CropSelected = function(e) {
    this.browser.find(".wpadverts-attachment-details").find(".adverts-image-action-crop").css("opacity", "1");
    this.crop = e;
};

WPADVERTS.File.Browser.prototype.CropReleased = function(e) {
    this.browser.find(".wpadverts-attachment-details").find(".adverts-image-action-crop").css("opacity", "0.5");
    this.crop = null;
};

WPADVERTS.File.Browser.prototype.ImageCrop = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    if(this.crop === null) {
        return;
    }
    
    var crop = this.crop;
    crop.a = "c";

    this.history.push(crop);
    this.crop = null;
    
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.RotateCW = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }

    this.history.push({a: "ro", v: "90"});
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.RotateCCW = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }

    this.history.push({a: "ro", v: "-90"});
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.ImageFlipH = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }

    this.history.push({a: "f", h: true, v: false});
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.ImageFlipV = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }

    this.history.push({a: "f", h: false, v: true});
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.KeyWidth = function(e) {

    // calculate height
    var width = parseInt(this.input.width.val());
    var max_width = parseInt(this.input.width.attr("max"));
    var max_height = parseInt(this.input.height.attr("max"));
    
    var scale = width * ( max_height / max_width );
    
    this.input.height.val(Math.round(scale).toString());
};

WPADVERTS.File.Browser.prototype.KeyHeight = function(e) {

    // calculate width
    var height = parseInt(this.input.width.val());
    var max_width = parseInt(this.input.width.attr("max"));
    var max_height = parseInt(this.input.height.attr("max"));
    
    var scale = height * ( max_width / max_height );
    
    this.input.width.val(Math.round(scale).toString());
};

WPADVERTS.File.Browser.prototype.ImageScale = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.history.push({a:"re", w:this.input.width.val(), h:this.input.height.val()});
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.ImageUndo = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    if(this.history.length === 0) {
        return;
    }
    
    this.history.pop();
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.ImageSave = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    var data = {
        action: "adverts_gallery_image_save",
        history: JSON.stringify(this.history),
        size: this.imageSize,
        attach_id: this.file.attach_id,
        action_type: this.actionType,
        apply_to: this.imageSize
    };
    
    jQuery.ajax({
        url: adverts_gallery_lang.ajaxurl,
        context: this,
        type: "post",
        dataType: "json",
        data: data,
        success: jQuery.proxy(this.ImageSaveSuccess, this)
    });
};

WPADVERTS.File.Browser.prototype.ImageCancel = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.Render(this.file);
};

WPADVERTS.File.Browser.prototype.ImageRestore = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.history = [];
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.ImageSaveSuccess = function(response) {
    var x = 0;
};

WPADVERTS.File.Browser.prototype.UpdateDescription = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.element.spinner.show();

    var featured = this.element.input.featured.prop("checked") ? 1 : 0;

    jQuery.ajax({
        url: adverts_gallery_lang.ajaxurl,
        context: this,
        type: "post",
        dataType: "json",
        data: {
            action: "adverts_gallery_update",
            _ajax_nonce: this.uploader.setup.init.multipart_params._ajax_nonce,
            post_id: this.uploader.PostID,
            attach_id: this.file.attach_id,
            caption: this.element.input.caption.val(),
            content: this.element.input.content.val(),
            featured: featured
        },
        success: jQuery.proxy(this.UpdateDescriptionSuccess, this)
    }); // end jQuery.ajax 
};

WPADVERTS.File.Browser.prototype.UpdateDescriptionSuccess = function(r) {
    this.element.spinner.hide();

    if(r.result == 1) {
        
        var featured = this.element.input.featured.prop("checked") ? 1 : 0;
        var br = this;
        var response = r;
        
        jQuery.each(this.uploader.Item, function(index, item) {
            if(item.result.attach_id == br.file.attach_id) {
                item.result = response.file;
                item.render();
                
                br.file = response.file;
            } else if(featured) {
                item.result.featured = 0;
                item.render();
            }
        });

    } else {
        alert(r.error);
    }  
};

jQuery(function($) {
    $.each(ADVERTS_PLUPLOAD_DATA, function(index, item) {
        WPADVERTS.File.Registered.push(new WPADVERTS.File.Uploader(item));
    });
});
