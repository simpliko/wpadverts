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
    },
    GetMime: function(file) {
        var mime = "other";

        if(file === null) {
            return mime;
        }

        if(typeof file.mime_type === "undefined") {
            return mime;
        }

        var types = {
            video: [ "video/webm", "video/mp4", "video/ogv" ],
            image: [ "image/jpeg", "image/jpe", "image/jpg", "image/gif", "image/png" ]
        };
        for(var index in types) {
            if(types[index].indexOf(file.mime_type) !== -1) {
                mime = index;
            }
        }  
        return mime;
    },
    GetIcon: function(file) {

        if(file === null) {
            return null;
        }

        var m = this.GetMime(file);

        if( m === "video" ) {
            return "adverts-icon-file-video";
        } else if( m === "image" ) {
            return "adverts-icon-file-image";
        }
        
        if(["application/x-pdf", "application/pdf"].indexOf(file.mime_type)) {
            return "adverts-icon-file-pdf";
        } else if(["application/zip", "application/octet-stream"].indexOf(file.mime_type)) {
            return "adverts-icon-file-archive";
        }
        
        return "adverts-icon-doc-inv";
    },
    BrowserError: function(error) {
        new WPADVERTS.File.Error(error, false);
    }
};

WPADVERTS.File.Error = function(error, overlay) {
    var template = wp.template( "wpadverts-browser-error" );
    var $ = jQuery;
    var text = "";
    
    if(typeof error.responseText !== "undefined") {
        text = error.responseText;
    } else if(typeof error.error !== "undefined") {
        text = error.error;
    }
    
    if(text.length === 0) {
        return;
    }
    
    var data = {
        error: text,
        overlay: overlay
    };
    
    var tpl = template(data);
    
    this.html = $(tpl);
    this.html.find("a.adverts-button").on("click", jQuery.proxy(this.CloseClicked, this));
    
    jQuery("body").append(this.html);
};

WPADVERTS.File.Error.prototype.CloseClicked = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.html.remove();
};

WPADVERTS.File.Uploader = function(setup) {
    var $ = jQuery;
    
    this.PostID = null;
    this.PostIDNonce = null;
    this.Type = "Media";
    
    if(typeof setup.conf.save.method != "undefined" && setup.conf.save.method === "file" ) {
        this.Type = "File";
    }
    
    if($(setup.conf.input_post_id).val()) {
        this.PostID = $(setup.conf.input_post_id).val();
    }
    if($(setup.conf.input_post_id_nonce).val()) {
        this.PostIDNonce = $(setup.conf.input_post_id_nonce).val();
    }
    if($("#"+setup.init.container).closest("form").find("#wpadverts-form-upload-uniqid").length === 0) {
        var uploadid = $("<input type='hidden' name='wpadverts-form-upload-uniqid' id='wpadverts-form-upload-uniqid' />");
        if(setup.conf.uniqid !== null) {
            uploadid.val(setup.conf.uniqid);
        } else if(this.PostID) {
            uploadid.val(this.PostID);
        }
        $("#"+setup.init.container).closest("form").append(uploadid);
    }

    this.Item = {};
    this.Browser = new WPADVERTS.File.Browser(this);
    
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
    
    
    if( this.setup.conf.save.method !== "file" ) {
        this.sortable.sortable({
            update: jQuery.proxy(this.SortableUpdate, this)
        });
    }
    
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
    
    if( this.setup.conf.save.method === "file" ) {
        return;
    }
    
    var keys = this.GetKeys();

    if(typeof e !== "undefined") {
        this.ui.find(".adverts-gallery-upload-update.adverts-icon-spinner.animate-spin").fadeIn();
    }

    if(typeof this.PostID === 'undefined') {
        return;
    }

    jQuery.ajax({
        url: adverts_gallery_lang.ajaxurl,
        context: this,
        type: "post",
        dataType: "json",
        data: {
            action: "adverts_gallery_update_order",
            _wpadverts_checksum_nonce: this.setup.init.multipart_params._wpadverts_checksum_nonce,
            _wpadverts_checksum: this.setup.init.multipart_params._wpadverts_checksum,
            _post_id: this.PostID,
            _post_id_nonce: this.PostIDNonce,
            form_name: this.setup.init.multipart_params.form,
            field_name: this.setup.init.multipart_params.field_name,
            ordered_keys: JSON.stringify(keys)
        },
        success: jQuery.proxy(this.SortableUpdateSuccess, this),
        error: jQuery.proxy(this.SortableUpdateError, this)
    });
};

WPADVERTS.File.Uploader.prototype.SortableUpdateSuccess = function(response) {
    if (response.result == 1) {
        this.ui.find(".adverts-gallery-upload-update.adverts-icon-spinner.animate-spin").fadeOut();
    } else {
        WPADVERTS.File.BrowserError(response);
    }
};

WPADVERTS.File.Uploader.prototype.SortableUpdateError = function(response) {
    this.ui.find(".adverts-gallery-upload-update.adverts-icon-spinner.animate-spin").fadeOut();
    WPADVERTS.File.BrowserError(response);
};

WPADVERTS.File.Uploader.prototype.FileAdded = function(container, file) {
    var c = jQuery("<div></div>").addClass("adverts-gallery-upload-item").attr("id", file.id);
    var init = {
        _ajax_nonce: this.setup.init.multipart_params._ajax_nonce,
        field_name: this.Browser.uploader.setup.init.multipart_params.field_name
    };
    
    this.Item[file.id] = new WPADVERTS.File.Singular(file, c, init);
    this.Item[file.id].SetBrowser(this.Browser);
    this.Item[file.id].Type = this.Type;
    
    this.Item[file.id].render();
    
    this.ui.find(".adverts-gallery-uploads").append(c);
};

WPADVERTS.File.Uploader.prototype.FileUploaded = function(file, result) {
    this.Item[file.id].setResult(result);
    this.Item[file.id].render();
    
    var GlobalPostID = null;
    var GlobalPostIDNonce = null;
    
    if(typeof result.uniqid !== "undefined" && jQuery("#wpadverts-form-upload-uniqid").val().length === 0) {
        jQuery("#wpadverts-form-upload-uniqid").val(result.uniqid);
    }
    
    if(typeof result.post_id !== "undefined") {
        this.PostID = result.post_id;
        GlobalPostID = this.PostID;
    }
    
    if( jQuery( this.setup.conf.input_post_id ).length > 0 && jQuery( this.setup.conf.input_post_id ).val().length === 0 ) {
        jQuery( this.setup.conf.input_post_id ).val( this.PostID );
    }
    
    if(typeof result.post_id_nonce !== "undefined") {
        this.PostIDNonce = result.post_id_nonce;
        GlobalPostIDNonce = this.PostIDNonce
    }
    
    if( jQuery( this.setup.conf.input_post_id_nonce ).length > 0 && jQuery( this.setup.conf.input_post_id_nonce ).val().length === 0 ) {
        jQuery( this.setup.conf.input_post_id_nonce ).val( this.PostIDNonce );
    }
    
    jQuery.each(WPADVERTS.File.Registered, function(index, item) {
        WPADVERTS.File.Registered[index].PostID = GlobalPostID;
        WPADVERTS.File.Registered[index].PostIDNonce = GlobalPostIDNonce;
    });
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
    this.uploader.bind('UploadComplete', jQuery.proxy(this.engine.UploadComplete, this));
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
        this.ui.find('.adverts-gallery').bind('dragover.wp-uploader', jQuery.proxy(this.engine.InitDragOver, this));
        this.ui.find('.adverts-drag-drop-area').bind('dragleave.wp-uploader, drop.wp-uploader', jQuery.proxy(this.engine.InitDragLeave, this));
    }else{
        this.ui.removeClass('drag-drop');
        this.ui.find('.adverts-drag-drop-area').unbind('.wp-uploader');
    }
};

WPADVERTS.File.Uploader.Plupload.prototype.InitDragOver = function() {
    this.ui.addClass('drag-over'); 
};

WPADVERTS.File.Uploader.Plupload.prototype.InitDragLeave = function() {
    this.ui.removeClass('drag-over'); 
};

WPADVERTS.File.Uploader.Plupload.prototype.BeforeUpload = function(up,file) {
    if(this.PostID !== null && this.PostIDNonce !== null) {
        up.settings.multipart_params._post_id = this.PostID;
        up.settings.multipart_params._post_id_nonce = this.PostIDNonce;
    }
    
    up.settings.multipart_params._uniqid = jQuery("#wpadverts-form-upload-uniqid").val()
    
    var mp = up.settings.multipart_params;
    var form = this.ui.closest("form");
    form.find(".wpadverts-plupload-multipart-default").each(function(index, item) {
        mp[jQuery(item).attr("name")] = jQuery(item).val();
    });
    
    up.settings.multipart_params = mp;
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
    if( this.PostIDNonce === null ) {
        this.PostIDNonce = result.post_id_nonce;
    }

    this.FileUploaded(file, result);
    
    if( typeof result.attach_id !== 'undefined' ) { 
        //this.SortableUpdate();
    }
};

WPADVERTS.File.Uploader.Plupload.prototype.UploadComplete = function(up, file, response) {
    if( this.setup.conf.save.method === "file" ) {
        return;
    }
    this.SortableUpdate();;
}

WPADVERTS.File.Singular = function(file, container, init) {
    this.type = "Media";
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
        result: this.result,
        mime: WPADVERTS.File.GetMime(this.result),
        icon: WPADVERTS.File.GetIcon(this.result),
        conf: this.browser.uploader.setup.conf
    };
    
    var tpl = template(data);
    var html = $(tpl);
    
    this.container.html(html);
    
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

    var data = null;

    if( typeof this.result.uniqid != "undefined" ) {
        data = {
            action: "adverts_gallery_delete_file",
            field_name: this.init.field_name,
            form_name: this.browser.uploader.setup.conf.form_name,
            uniqid: this.result.uniqid,
            _post_id: this.result.post_id,
            _post_id_nonce: this.result.post_id_nonce,
            _wpadverts_checksum_nonce: jQuery("#_wpadverts_checksum_nonce").val(),
            _wpadverts_checksum: jQuery("#_wpadverts_checksum").val(),
            filename: this.result.readable.name
        };
    } else {
        data = {
            action: "adverts_gallery_delete",
            field_name: this.init.field_name,
            _post_id: this.result.post_id,
            _post_id_nonce: this.result.post_id_nonce,
            _wpadverts_checksum_nonce: jQuery("#_wpadverts_checksum_nonce").val(),
            _wpadverts_checksum: jQuery("#_wpadverts_checksum").val(),
            attach_id: this.result.attach_id
        };
    }

    jQuery.ajax({
        url: adverts_gallery_lang.ajaxurl,
        context: this,
        type: "post",
        dataType: "json",
        data: data,
        success: jQuery.proxy(this.RemoveClickedSuccess, this),
        error: jQuery.proxy(this.RemoveClickedError, this)
    });            
};

WPADVERTS.File.Singular.prototype.RemoveClickedSuccess = function(response) {
    if(response.result == 1) {
        this.Dispose();
    } else {
        this.spinner.hide();
        new WPADVERTS.File.Error(response, true);
    }
};

WPADVERTS.File.Singular.prototype.RemoveClickedError = function(response) {
    this.spinner.hide();
    new WPADVERTS.File.Error(response, true);
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

WPADVERTS.File.Browser.prototype.GetIntegrityKeys = function() {
    return {
        _post_id: this.file.post_id,
        _post_id_nonce: this.file.post_id_nonce,
        _wpadverts_checksum_nonce: jQuery("#_wpadverts_checksum_nonce").val(),
        _wpadverts_checksum: jQuery("#_wpadverts_checksum").val()
    };
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
    jQuery("html").css("overflow", "hidden");
};

WPADVERTS.File.Browser.prototype.Close = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.browser.hide();
    jQuery("html").css("overflow", '');
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
    
    if (!Date.now) {
        var timestamp = new Date().getTime();
    } else {
        var timestamp = Date.now();
    }
    
    var can_feature = false;
    
    if(typeof this.uploader.setup.conf.save.supports != "undefined" && this.uploader.setup.conf.save.supports.indexOf("featured") >= 0) {
        can_feature = true;
    }
    
    var template = wp.template( "wpadverts-browser-attachment-view" );
    var mime = WPADVERTS.File.GetMime(result);
    var $ = jQuery;
    var data = {
        mime: mime,
        icon: WPADVERTS.File.GetIcon(this.file),
        file: this.file,
        timestamp: timestamp,
        can_feature: can_feature
    };
    
    var tpl = template(data);
    var html = $(tpl);
    
    html.find(".wpadverts-image-sizes").on("change", jQuery.proxy(this.ImageSizeChanged, this));
    html.find(".adverts-upload-modal-update").on("click", jQuery.proxy(this.UpdateDescription, this));
    
    this.element = {
        spinner: html.find(".adverts-loader.animate-spin"),
        success: html.find(".adverts-update-description-success"),
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
    
    if(mime == "video") {
        this.RenderVideo();
    }
};

WPADVERTS.File.Browser.prototype.RenderVideo = function() {
    this.video = new WPADVERTS.File.Browser.Video(this.browser);
    this.video.SetPostId(this.uploader.PostID);
    this.video.SetIntegrityKeys(this.GetIntegrityKeys());
    this.video.SetAttachId(this.file.attach_id);
    this.video.buttonThumb.on("click", jQuery.proxy(this.video.CreateThumbnail, this));
};

WPADVERTS.File.Browser.prototype.EditImage = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.imageSize = this.browser.find(".wpadverts-image-sizes option:selected").val();
    this.actionType = "edit";
    this.dim = [this.file.sizes[this.imageSize].width, this.file.sizes[this.imageSize].height];
    this.dimHistory = [];
    this.dimHistory.push(this.dim);
    
    this.history = [];
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.CreateImage = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.imageSize = this.browser.find(".wpadverts-image-sizes option:selected").val();
    this.actionType = "create";
    
    this.dim = [this.file.sizes.full.width, this.file.sizes.full.height];
    this.dimHistory = [];
    this.dimHistory.push(this.dim);
    
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
    
    var recommended = null;
    if(this.imageSize !== "full") {
        recommended = ADVERTS_IMAGE_SIZES[this.imageSize]
    }
    
    var integrity = this.GetIntegrityKeys();
    var template = wp.template( "wpadverts-browser-attachment-image" );
    var $ = jQuery;
    var data = {
        _wpadverts_checksum_nonce: integrity._wpadverts_checksum_nonce,
        _wpadverts_checksum: integrity._wpadverts_checksum,
        _post_id: integrity._post_id,
        _post_id_nonce: integrity._post_id_nonce,
        file: this.file,
        size: imageSize,
        dim: this.dim,
        recommended: recommended,
        rand: Math.floor(Math.random() * 10000),
        history: JSON.stringify(this.history)
    };
    
    var tpl = template(data);
    var html = $(tpl);
    
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
    
    this.spinner = html.find(".wpadverts-image-edit-spinner");

    this.input.width = html.find(".adverts-image-scale-width");
    this.input.height = html.find(".adverts-image-scale-height");
    
    if(this.history.length == 0) {
        html.find(".adverts-image-action-undo").css("opacity", "0.5");
        html.find(".adverts-image-action-save").css("opacity", "0.5");
    }
    
    html.find(".adverts-image-action-crop").css("opacity", "0.5");
    
    this.browser.find(".wpadverts-attachment-details").html(html);
    
    var icrop = this.browser.find("#wpadverts-image-crop");
    icrop.load(jQuery.proxy(this.ImageCropLoaded, this));
    icrop.attr('src', icrop.data("src"));
};

WPADVERTS.File.Browser.prototype.ImageCropLoaded = function(e) {
    
    var jopt = {
        onSelect: jQuery.proxy(this.CropSelected, this),
        onChange: jQuery.proxy(this.CropSelected, this),
        onRelease: jQuery.proxy(this.CropReleased, this)
    };
    
    if(this.imageSize == "full") {    
        jopt.trueSize = [this.dim[0], this.dim[1]];
    }
    
    if(this.actionType == "create" && this.imageSize != "full") {
        jopt.trueSize = [this.dim[0], this.dim[1]];
    } 

    
    this.image = this.browser.find("#wpadverts-image-crop");
    this.jcrop = this.image.Jcrop(jopt);
    
    var s = this.dimHistory[0];
    var d = this.image.parent().width()/this.dim[0];
    
    if(d>1) {
        d = 1;
    }

    this.browser.find(".adverts-image-prop-original-size").text(s[0].toString() + " x " + s[1].toString());
    this.browser.find(".adverts-image-prop-current-size").text(this.dim[0].toString() + " x " + this.dim[1].toString());
    this.browser.find(".adverts-image-prop-zoom").text(Math.round(d*100, 2).toString()+"%");
    
    this.spinner.hide();
};

WPADVERTS.File.Browser.prototype.ImageSizeChanged = function(e) {
    var s = this.browser.find(".wpadverts-image-sizes option:selected");

    if( s.val() === "video" ) {
        this.browser.find(".wpadverts-file-browser-image-actions").hide();
        this.browser.find(".wpadverts-file-browser-video-actions").show();
        
        this.browser.find(".wpadverts-attachment-image").hide();
        this.browser.find(".wpadverts-attachment-video").show();
    } else {
        this.browser.find(".wpadverts-file-browser-image-actions").show();
        this.browser.find(".wpadverts-file-browser-video-actions").hide();
        
        this.browser.find(".wpadverts-attachment-image").show();
        this.browser.find(".wpadverts-attachment-video").hide();
    }
    
    if( s.val() === "full" ) {
        this.browser.find(".wpadverts-attachment-create-image").hide();
    } else {
        this.browser.find(".wpadverts-attachment-create-image").show();
    }

    this.browser.find(".adverts-image-preview").hide();
    this.browser.find(".adverts-image-preview.adverts-image-preview-"+s.val()).fadeIn("fast");
    this.browser.find(".adverts-icon-size-explain-desc").text(s.data("explain"));
    
};

WPADVERTS.File.Browser.prototype.CropSelected = function(c) {
    this.browser.find(".wpadverts-attachment-details").find(".adverts-image-action-crop").css("opacity", "1");
    this.browser.find(".adverts-image-prop-selection").text( Math.round(c.w) + " x " + Math.round(c.h) );
    this.crop = c;
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
    crop.w = Math.round(crop.w);
    crop.h = Math.round(crop.h);
    
    this.dimHistory.push(this.dim);
    this.dim = [crop.w, crop.h];
    
    this.history.push(crop);
    this.crop = null;

    this.spinner.show();
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.RotateCW = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }

    this.dimHistory.push(this.dim);
    this.dim = [this.dim[1], this.dim[0]];
    
    this.history.push({a: "ro", v: "-90"});
    
    this.spinner.show();
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.RotateCCW = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }

    this.dimHistory.push(this.dim);
    this.dim = [this.dim[1], this.dim[0]];
    
    this.history.push({a: "ro", v: "90"});
    
    this.spinner.show();
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.ImageFlipH = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.dimHistory.push(this.dim);
    this.dim = [this.dim[0], this.dim[1]];
    
    this.history.push({a: "f", h: true, v: false});
    this.spinner.show();
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.ImageFlipV = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.dimHistory.push(this.dim);
    this.dim = [this.dim[0], this.dim[1]];
    
    this.history.push({a: "f", h: false, v: true});
    this.spinner.show();
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
    var height = parseInt(this.input.height.val());
    var max_width = parseInt(this.input.width.attr("max"));
    var max_height = parseInt(this.input.height.attr("max"));
    
    var scale = height * ( max_width / max_height );
    
    this.input.width.val(Math.round(scale).toString());
};

WPADVERTS.File.Browser.prototype.ImageScale = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.dimHistory.push(this.dim);
    this.dim = [this.input.width.val(), this.input.height.val()];
    
    this.history.push({a:"re", w:this.input.width.val(), h:this.input.height.val()});
    this.spinner.show();
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.ImageUndo = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    if(this.history.length === 0) {
        return;
    }
    
    this.dim = this.dimHistory.pop();
    this.history.pop();
    this.spinner.show();
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.ImageSave = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    if(this.history.length === 0) {
        return;
    }
    
    this.spinner.show();
    var applyAll = 0;
    var integrity = this.GetIntegrityKeys();
    
    if(this.imageSize == "full" && this.browser.find(".wpadverts-image-action-apply-to").is(":checked")) {
        applyAll = 1;
    }
    
    var data = {
        action: "adverts_gallery_image_save",
        _wpadverts_checksum_nonce: integrity._wpadverts_checksum_nonce,
        _wpadverts_checksum: integrity._wpadverts_checksum,
        _post_id: integrity._post_id,
        _post_id_nonce: integrity._post_id_nonce,
        history: JSON.stringify(this.history),
        size: this.imageSize,
        attach_id: this.file.attach_id,
        action_type: this.actionType,
        apply_to: this.imageSize,
        apply_to_all: applyAll
    };

    jQuery.ajax({
        url: adverts_gallery_lang.ajaxurl,
        context: this,
        type: "post",
        dataType: "json",
        data: data,
        success: jQuery.proxy(this.ImageSaveSuccess, this),
        error: jQuery.proxy(this.ImageSaveError, this)
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
    
    var integrity = this.GetIntegrityKeys();
    
    var data = {
        action: "adverts_gallery_image_restore",
        _wpadverts_checksum_nonce: integrity._wpadverts_checksum_nonce,
        _wpadverts_checksum: integrity._wpadverts_checksum,
        _post_id: integrity._post_id,
        _post_id_nonce: integrity._post_id_nonce,
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
        success: jQuery.proxy(this.ImageRestoreSuccess, this),
        error: jQuery.proxy(this.ImageRestoreError, this)
    });
    
    this.history = [];
    this.ImageLoad();
};

WPADVERTS.File.Browser.prototype.ImageRestoreSuccess = function(response) {

    this.spinner.hide();
    
    if(response.result != "1") {
        new WPADVERTS.File.Error(response, false);
        return;
    }
    
    for(var i in this.uploader.Item) {
        if(this.uploader.Item[i].result.attach_id == this.file.attach_id) {
            this.uploader.Item[i].result = response.file;
            this.file = response.file;
            this.Render(response.file);
        }
    }

};

WPADVERTS.File.Browser.prototype.ImageRestoreError = function(response) {
    this.spinner.hide();
    new WPADVERTS.File.Error(response, false);
};

WPADVERTS.File.Browser.prototype.ImageSaveSuccess = function(response) {
    var br = this;
    
    this.spinner.hide();
    
    if(response.result != "1") {
        new WPADVERTS.File.Error(response, false);
        return;
    }
    
    for(var i in WPADVERTS.File.Registered) {
        for(var j in WPADVERTS.File.Registered[i].Item) {
            if(WPADVERTS.File.Registered[i].Item[j].result.attach_id == response.file.attach_id) {
                WPADVERTS.File.Registered[i].Item[j].result = response.file;
                WPADVERTS.File.Registered[i].Item[j].render();
                WPADVERTS.File.Registered[i].Browser.Render(response.file);
            }
        }
    }

};

WPADVERTS.File.Browser.prototype.ImageSaveError = function(response) {
    this.spinner.hide();
    new WPADVERTS.File.Error(response, false);
};

WPADVERTS.File.Browser.prototype.UpdateDescription = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.element.spinner.css("display", "inline-block");

    var featured = this.element.input.featured.prop("checked") ? 1 : 0;
    var integrity = this.GetIntegrityKeys();
    
    jQuery.ajax({
        url: adverts_gallery_lang.ajaxurl,
        context: this,
        type: "post",
        dataType: "json",
        data: {
            action: "adverts_gallery_update",
            _wpadverts_checksum_nonce: integrity._wpadverts_checksum_nonce,
            _wpadverts_checksum: integrity._wpadverts_checksum,
            _post_id: integrity._post_id,
            _post_id_nonce: integrity._post_id_nonce,
            attach_id: this.file.attach_id,
            caption: this.element.input.caption.val(),
            content: this.element.input.content.val(),
            featured: featured
        },
        success: jQuery.proxy(this.UpdateDescriptionSuccess, this),
        error: WPADVERTS.File.BrowserError
    }); // end jQuery.ajax 
};

WPADVERTS.File.Browser.prototype.UpdateDescriptionSuccess = function(r) {
    this.element.spinner.hide();

    if(r.result == 1) {
        
        this.element.success.fadeIn("fast", function() {
           jQuery(this).delay(500).fadeOut("slow"); 
        });
        
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

WPADVERTS.File.Browser.Video = function(browser) {
    this.integrity = null;
    this.attachId = null;
    this.PostId = null;
    
    this.spinner = browser.find(".adverts-file-video-spinner");
    this.player = browser.find(".wpadverts-file-browser-video");
    this.preview = browser.find(".wpadverts-file-browser-video-preview")
    this.buttonThumb = browser.find(".wpadverts-file-browser-video-thumbnail");
    this.buttonSave = browser.find(".wpadverts-file-browser-video-thumbnail-save");
    this.buttonCancel = browser.find(".wpadverts-file-browser-video-thumbnail-cancel");
    this.divPlayer = browser.find(".wpadverts-file-browser-video-player");
    this.divSelect = browser.find(".wpadverts-file-browser-video-select-thumbnail");
    
    this.divPlayer.show();
    this.divSelect.hide();
    
    this.buttonSave.on("click", jQuery.proxy(this.SaveClick, this));
    this.buttonCancel.on("click", jQuery.proxy(this.CancelClick, this));
    
    this.canvas = null;
};

WPADVERTS.File.Browser.Video.prototype.SetIntegrityKeys = function(integrity) {
    this.integrity = integrity;
};

WPADVERTS.File.Browser.Video.prototype.GetIntegrityKeys = function() {
    return this.integrity;
};

WPADVERTS.File.Browser.Video.prototype.SetPostId = function(PostId) {
    this.PostId = PostId;
};

WPADVERTS.File.Browser.Video.prototype.GetPostId = function() {
    return this.PostId;
};

WPADVERTS.File.Browser.Video.prototype.SetAttachId = function(attachId) {
    this.attachId = attachId;
};

WPADVERTS.File.Browser.Video.prototype.GetAttachId = function() {
    return this.attachId;
};

WPADVERTS.File.Browser.Video.prototype.CreateThumbnail = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.video.canvasPreview = this.video.Capture(this.video.video, "preview");
    this.video.canvasOriginal = this.video.Capture(this.video.video, "original");
    
    this.video.preview.html(this.video.canvasPreview);
    
    this.video.divPlayer.hide();
    this.video.divSelect.fadeIn("fast");
};

WPADVERTS.File.Browser.Video.prototype.Capture = function(video, type) {
    var scaleFactor = 1;
    var w = 0;
    var h = 0;
    
    if(type == "preview") {
        w = this.player.width() * scaleFactor;
        h = this.player.height() * scaleFactor;
    } else {
        w = this.player[0].videoWidth;
        h = this.player[0].videoHeight;
    }

    var canvas = document.createElement('canvas');
    canvas.width  = w;
    canvas.height = h;
    
    var ctx = canvas.getContext('2d');
    ctx.drawImage(this.player[0], 0, 0, w, h);
    
    return canvas;
};

WPADVERTS.File.Browser.Video.prototype.CancelClick = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.canvasPreview = null;
    this.canvasOriginal = null;
    
    this.preview.html("");
    this.divSelect.hide();
    this.divPlayer.fadeIn("fast");
};

WPADVERTS.File.Browser.Video.prototype.SaveClick = function(e) {
    if(typeof e !== "undefined") {
        e.preventDefault();
    }
    
    this.spinner.css("display", "inline-block");
    var integrity = this.GetIntegrityKeys();
    
    var data = {
        action: "adverts_gallery_video_cover",
        _wpadverts_checksum_nonce: integrity._wpadverts_checksum_nonce,
        _wpadverts_checksum: integrity._wpadverts_checksum,
        _post_id: integrity._post_id,
        _post_id_nonce: integrity._post_id_nonce,
        attach_id: this.GetAttachId(),
        image: this.canvasOriginal.toDataURL("image/png"),
        width: this.canvasOriginal.width,
        height: this.canvasOriginal.height
    };
    
    jQuery.ajax({
        url: adverts_gallery_lang.ajaxurl,
        data: data,
        dataType: "json",
        type: "post",
        success: jQuery.proxy(this.SaveClickSuccess, this),
        error: jQuery.proxy(this.SaveClickError, this)
    });
};

WPADVERTS.File.Browser.Video.prototype.SaveClickSuccess = function(r) {
    this.spinner.hide();
    
    if(r.result != 1) {
        WPADVERTS.File.BrowserError(r);
        return;
    }

    for(var i in WPADVERTS.File.Registered) {
        for(var j in WPADVERTS.File.Registered[i].Item) {
            if(WPADVERTS.File.Registered[i].Item[j].result.attach_id == r.file.attach_id) {
                WPADVERTS.File.Registered[i].Item[j].result = r.file;
                WPADVERTS.File.Registered[i].Item[j].render();
                WPADVERTS.File.Registered[i].Browser.Render(r.file);
                WPADVERTS.File.Registered[i].Browser.browser.find(".wpadverts-image-sizes").val("full");
                WPADVERTS.File.Registered[i].Browser.ImageSizeChanged();
            }
        }
    }
};

WPADVERTS.File.Browser.Video.prototype.SaveClickError = function(r) {
    this.spinner.hide();
    WPADVERTS.File.BrowserError(r);
};

WPADVERTS.File.Browser.Other = function() {
    
};

jQuery(function($) {
    if(typeof ADVERTS_PLUPLOAD_DATA === "undefined") {
        return;
    }
    
    $.each(ADVERTS_PLUPLOAD_DATA, function(index, item) {
        WPADVERTS.File.Registered.push(new WPADVERTS.File.Uploader(item));
    });
});
