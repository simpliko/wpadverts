jQuery(function($) {

    var spinner_large = $("<div></div>").addClass("adverts-gallery-upload-update adverts-icon-spinner animate-spin");
    $(spinner_large).css("position", "absolute").hide();

    // order images using drag and drop
    $(".adverts-gallery-uploads").sortable({
        update: function() {
            var ordered_keys = $("div.adverts-gallery-uploads").sortable("toArray", {attribute: 'attachment-key'});

            $(this).find(".adverts-gallery-upload-update.adverts-icon-spinner.animate-spin").fadeIn();

            $.ajax({
                url: adverts_gallery_lang.ajaxurl,
                context: this,
                type: "post",
                dataType: "json",
                data: {
                    action: "adverts_gallery_update_order",
                    _ajax_nonce: ADVERTS_PLUPLOAD_INIT.multipart_params._ajax_nonce,
                    post_id: $(ADVERTS_PLUPLOAD_CONF.post_id_input).val(),
                    ordered_keys: JSON.stringify(ordered_keys)
                },
                success: function (response) {
                    if (response.result == 1) {
                        $(this).find(".adverts-gallery-upload-update.adverts-icon-spinner.animate-spin").fadeOut();
                    } else {
                        alert(response.error);
                    }
                }
            });
        }
    });

    function adverts_upload_files_added(container, file) {
        var ag_ui = $("<div></div>").addClass("adverts-gallery-upload-item").attr("id", file.id).attr("attachment-key", file.attachment_key);

        $(spinner_large).show();
        $(spinner_large).clone().prependTo(ag_ui);

        $("#"+container+" .adverts-gallery-uploads").append(ag_ui);
    }

    // this is your ajax response, update the DOM with it or something...
    function adverts_upload_file_uploaded(file, result, trigger_order_update) {
        $("#" + file.id).attr('attachment-key', result.attach_id);

        if(result.error) {
            var m = $("#"+file.id+" .adverts-gallery-upload-update");
            m.removeClass("adverts-icon-spinner animate-spin");
            m.addClass("adverts-icon-attention");
            m.html($("<span></span>").addClass("adverts-gallery-upload-failed").html(result.error));
            return;
        } else {
            $("#"+file.id+" .adverts-gallery-upload-update").hide();
        }
        
        var ag_ui = $("#"+file.id);
        var ag_img = $("<img />").attr("src", result.sizes.adverts_upload_thumbnail.url).attr("alt", "").addClass("adverts-gallery-upload-item-img");
        var ag_feat = $("<span></span>").addClass("adverts-gallery-item-featured").html(adverts_gallery_lang.featured);
        var ag_p = $("<p></p>").addClass("adverts-gallery-upload-actions");
        var ag_spin = $("<span></span>").addClass("adverts-loader adverts-icon-spinner animate-spin");
        var ag_aview = $("<a></a>")
            .attr("href", result.sizes.normal.url)
            .attr("class", ADVERTS_PLUPLOAD_CONF.button_class + " adverts-button-icon adverts-icon-eye")
            .attr("title", adverts_gallery_lang.view_image)
            .attr("target", "_blank");
        var ag_aedit = $("<a></a>")
            .attr("href", "#")
            .attr("id", "adverts-upload-button-edit-"+result.attach_id)
            .attr("class", ADVERTS_PLUPLOAD_CONF.button_class + " adverts-button-icon adverts-icon-pencil")
            .attr("title", adverts_gallery_lang.edit_image)
            .data("attach-id", result.attach_id);
        var ag_adel = $("<a></a>")
            .attr("href", "#")
            .attr("class", ADVERTS_PLUPLOAD_CONF.button_class + " adverts-button-icon adverts-icon-trash-1")
            .attr("title", adverts_gallery_lang.delete_image)
            .data("attach-id", result.attach_id);
            
        ag_aedit.data("caption", result.caption);
        ag_aedit.data("featured", result.featured);
        ag_aedit.data("content", result.content);
            
        if( result.featured ) {
            ag_feat.show();
        }        
            
        ag_adel.click(function(e) {
            e.preventDefault();
            var attach_id = $(this).data("attach-id");
            $(this).parent().find(".adverts-loader.animate-spin").css("display", "inline-block");
            
           jQuery.ajax({
               url: adverts_gallery_lang.ajaxurl,
               context: this,
               type: "post",
               dataType: "json",
               data: {
                   action: "adverts_gallery_delete",
                   _ajax_nonce: ADVERTS_PLUPLOAD_INIT.multipart_params._ajax_nonce,
                   post_id: $(ADVERTS_PLUPLOAD_CONF.post_id_input).val(),
                   attach_id: attach_id
               },
               success: function(response) {
                   if(response.result == 1) {
                       $(this).closest(".adverts-gallery-upload-item").fadeOut(function() {
                           $(this).remove();
                           $(".adverts-gallery-uploads").sortable("option", "update")();
                       });
                   } else {
                        $(this).parent().find(".adverts-loader.animate-spin").hide();
                       alert(response.error);
                   }
               }
            });            
            
        });
            
        ag_aedit.click(function(e) {
            e.preventDefault();
            
            var $this = $(this);
            var $modal = $("#adverts-modal-gallery");
            
            $modal.show();
            $modal.find("#adverts_caption").val($this.data("caption"));
            $modal.find("#adverts_content").val($this.data("content"));
            $modal.find(".adverts-upload-modal-update").data("attach-id", $this.data("attach-id"));
            
            if($this.data("featured")) {
                $modal.find("#adverts_featured").prop("checked", true);
            } else {
                $modal.find("#adverts_featured").prop("checked", false);
            }
            
            if( !$modal.hasClass("adverts-modal-reposition") ) {
                return;
            }
            
            $modal.css("height", $(document).height());
            $modal.css("width", $(document).width());

            var c = $modal.find(".adverts-modal-inner");
            c.css("position","absolute");
            c.css("top", Math.max(0, (($(window).height() - c.outerHeight()) / 2) + $(window).scrollTop()) + "px");
            c.css("left", Math.max(0, (($(window).width() - c.outerWidth()) / 2) +  $(window).scrollLeft()) + "px");
            
        });
            
        ag_p.append(ag_spin).append(ag_aview).append(ag_aedit).append(ag_adel);
        ag_ui.append(ag_img).append(ag_feat).append(ag_p);
    }

    

    $(".adverts-modal .adverts-upload-modal-close").click(function(e) {
        e.preventDefault();
        $(".adverts-modal").hide();
    });

    $(".adverts-modal .adverts-upload-modal-update").click(function(e) {
        e.preventDefault();
        $(".adverts-loader.animate-spin").show();
        
        var featured = $(".adverts-modal #adverts_featured").prop("checked") ? 1 : 0;
        
        jQuery.ajax({
            url: adverts_gallery_lang.ajaxurl,
            context: this,
            type: "post",
            dataType: "json",
            data: {
                action: "adverts_gallery_update",
                _ajax_nonce: ADVERTS_PLUPLOAD_INIT.multipart_params._ajax_nonce,
                post_id: $(ADVERTS_PLUPLOAD_CONF.post_id_input).val(),
                attach_id: $(this).data("attach-id"),
                caption: $(".adverts-modal #adverts_caption").val(),
                content: $(".adverts-modal #adverts_content").val(),
                featured: featured
            },
            success: function(response) {
                $(".adverts-loader.animate-spin").hide();
                
                if(response.result == 1) {
                    $(".adverts-modal .adverts-upload-modal-close").click();
                    var attach_id = $(this).data("attach-id");
                    var button = $("#adverts-upload-button-edit-"+attach_id);
                    var featured = $(".adverts-modal #adverts_featured").prop("checked") ? 1 : 0;
                    
                    button.data("caption", $(".adverts-modal #adverts_caption").val());
                    button.data("content", $(".adverts-modal #adverts_content").val());
                    button.data("featured", featured);
                    
                    $(".adverts-gallery-item-featured").hide();
                    
                    if(featured) {
                        button.closest(".adverts-gallery-upload-item").find(".adverts-gallery-item-featured").show();
                    }
                } else {
                    alert(response.error);
                }  
            } // end success
        }); // end jQuery.ajax 
        
    });
    
    
    if (typeof ADVERTS_PLUPLOAD_INIT === 'undefined') {
        return;
    }
    
      // create the uploader and pass the config from above
      var uploader = new plupload.Uploader(ADVERTS_PLUPLOAD_INIT);
      
      // checks if browser supports drag and drop upload, makes some css adjustments if necessary
      uploader.bind('Init', function(up){
        var uploaddiv = $('#adverts-plupload-upload-ui');

        if(up.features.dragdrop) {
            uploaddiv.addClass('drag-drop');
            uploaddiv.find('.adverts-gallery').bind('dragover.wp-uploader', function(){ 
                uploaddiv.addClass('drag-over'); 
            });
            uploaddiv.find('#adverts-drag-drop-area').bind('dragleave.wp-uploader, drop.wp-uploader', function(){
                uploaddiv.removeClass('drag-over'); 
                
            });
        }else{
          uploaddiv.removeClass('drag-drop');
          $('#adverts-drag-drop-area').unbind('.wp-uploader');
        }
        
      });

      uploader.init();

      uploader.bind("BeforeUpload", function(up,file) {
          uploader.settings.multipart_params.post_id = $(ADVERTS_PLUPLOAD_CONF.post_id_input).val();
      });
      
      // a file was added in the queue
      uploader.bind('FilesAdded', function(up, files){
        var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);

        plupload.each(files, function(file){
          if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
            // file size error?

          }else{

            // a file was added, you may want to update your DOM here...
            adverts_upload_files_added(up.settings.container, file);
            
          }
        });

        up.refresh();
        up.start();
      });

      // a file was uploaded 
      uploader.bind('FileUploaded', function(up, file, response) {
        var result = $.parseJSON(response.response);
        var post_id = $( ADVERTS_PLUPLOAD_CONF.post_id_input ).val();
        
        if( post_id == "" ) {
            $( ADVERTS_PLUPLOAD_CONF.post_id_input ).val( result.post_id );
        }

        adverts_upload_file_uploaded(file, result);

        // just added a new file, so send its order to the server
        $(".adverts-gallery-uploads").sortable("option", "update")();
      });
      
    $.each(ADVERTS_PLUPLOAD_DATA, function(index, result) {
        var file = { id: "adverts-file-" + result.attach_id, attachment_key: result.attach_id };
        adverts_upload_files_added(ADVERTS_PLUPLOAD_INIT.container, file);
        adverts_upload_file_uploaded(file, result);
    });
    
});