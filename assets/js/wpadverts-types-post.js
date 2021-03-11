jQuery(function($) {
    
    
    $(".wpadverts-types-edit-menu a").on("click", function(e) {
        e.preventDefault();
        var tab = $(this).data("tab");
        
        $(".wpadverts-types-edit-menu li").removeClass("active");
        $(".wpadverts-types-edit-form .adverts-dt-form").hide();
        
        $(this).closest("li").addClass("active");
        $(".wpadverts-types-edit-form .adverts-dt-form[data-tab=" + tab + "]").show();
    });
    
    $(".wpadverts-types-edit-menu a[data-tab=general]").click();

    
    $(".wpadverts-fcl-tr-supports input[type=checkbox]").each(function(index, item) {
        var $this = $(item);
        var lock = [ "title", "editor", "author" ];
        
        if( lock.indexOf( $this.val() ) >= 0 ) {
            $this.closest("label").addClass( "wpadverts-admin-cursor-block" );
            $this.attr( "readonly", "readonly" );
            $this.on("click", function(e) {
                $(this).blur();
                e.preventDefault();
                return false;
            });
        }
    });
    
    $("#rewrite_slug").on("keyup", wpadverts_admin_type_slug_preview);
    $("#rewrite_slug").on("change", wpadverts_admin_type_slug_preview);
    
    $("#rewrite_slug").change();
    
    
    
    if($("#hierarchical_1").length > 0) {
        $("#hierarchical_1").on("change", function(e) {
            if($(this).is(":checked")) {
                 $("#rewrite_hierarchical_1").closest("tr").show();
            } else {
                 $("#rewrite_hierarchical_1").closest("tr").hide();
            }
            
            wpadverts_admin_type_slug_preview();
        });
        $("#hierarchical_1").change();
    }
    
    
    if($("#rewrite_hierarchical_1").length > 0) {
        $("#rewrite_hierarchical_1").on("change", wpadverts_admin_type_slug_preview);
    }

    
    $(".wpadverts-admin-types-icon-select").on("click", function(e) {
        e.preventDefault();
        $("#menu_icon").val( $(".media-frame-content .button-primary").data("icon") );
        $(".wpadverts-admin-types-icon").hide();

        $(".wpadverts-admin-types-icon-select span").attr("class", "");
        $(".wpadverts-admin-types-icon-select span").addClass("dashicons");
        $(".wpadverts-admin-types-icon-select span").addClass($("#menu_icon").val());
        
        return false;
    });
    
    $(".wpadverts-admin-types-icon-close").on("click", function(e) {
        e.preventDefault();
        $(".wpadverts-admin-types-icon").hide();
    });
    
    $(".wpadverts-admin-types-icon-button").on("click", function(e) {
        e.preventDefault();
        $(".media-frame-content a").removeClass("button-primary");
        $(this).addClass("button-primary");
    });
    
    $(".wpadverts-admin-types-icon-search").on("keyup", function(e) {
        var filter = $(this).val();
        $(".media-frame-content a").each(function(index, item) {
            if( $(item).data("icon").includes(filter) ) {
                $(item).show();
            } else {
                $(item).hide();
            }
        });
    });

    var button = $('<a href="#" class="wpadverts-admin-types-icon-select button-secondary"><span class="dashicons"></span></a>');
    button.find("span").addClass($("#menu_icon").val()).css("line-height", "28px");
    
    $("#menu_icon").after(button);
    $("#menu_icon").hide();
    
    button.on("click", function(e) {
        e.preventDefault();
        $(".media-frame-content a").removeClass("button-primary");
        $(".media-frame-content ." + $("#menu_icon").val() ).closest("a").addClass("button-primary");
        
        $(".wpadverts-admin-types-icon").show();
    });
});

function wpadverts_admin_type_slug_preview() {
    var $ = jQuery;
    var $this = $("#rewrite_slug");
    var preview = $(".wpadverts-admin-type-slug-preview");

    if( $this.val().length === 0 ) {
        preview.text( $this.attr("placeholder") );
    } else {
        preview.text( $this.val() );
    }
    
    if($("#rewrite_hierarchical_1").length > 0) {
        if($("#rewrite_hierarchical_1").is(":checked") && $("#hierarchical_1").is(":checked") ) {
            $(".wpadverts-admin-type-slug-preview-sub").show();
        } else {
            $(".wpadverts-admin-type-slug-preview-sub").hide();
        }
    }
}

