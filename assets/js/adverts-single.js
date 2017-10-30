var WPADVERTS = WPADVERTS || {};

WPADVERTS.Single = {
    
};

WPADVERTS.Single.Gallery = {
    Index: 0,
    
    Items: [
        
    ],
    
    Thumbs: [
        
    ],
    
    Nav: {
        
    },
    
    InitSlider: function(nav) {
        this.Nav.Current = nav.find(".wpadverts-slide-nav-current");
        this.Nav.Thumbnails = nav.find(".wpadverts-slide-nav-thumbnails");
        this.Nav.ThumbsList = nav.find(".wpadverts-slide-nav-thumbnails-list");
        this.Nav.Prev = nav.find(".wpadverts-slide-nav-paginate-left");
        this.Nav.Next = nav.find(".wpadverts-slide-nav-paginate-right");
        
        this.Nav.Next.on("click", jQuery.proxy(this.Next, this));
        this.Nav.Prev.on("click", jQuery.proxy(this.Prev, this));
        
        this.Nav.Thumbnails.on("click", jQuery.proxy(this.ToggleThumbnails, this));
        
        var thumbs = this.Nav.ThumbsList.find("li");
        
        this.Nav.ThumbsList.find("li").each(jQuery.proxy(this.ThumbnailLoad, this));

    },
    
    Prev: function(e) {
        if(this.Index <= 0) {
            return;
        }
        
        for(var i in this.Items) {
            this.Items[i].hide();
        }
        this.Index--;
        this.Items[this.Index].fadeIn("fast");
    },
    
    Next: function(e) {
        if(this.Index+1 >= this.Items.length) {
            return;
        }
        
        for(var i in this.Items) {
            this.Items[i].hide();
        }
        this.Index++;
        this.Items[this.Index].fadeIn("fast");
    },
    
    GoTo: function(index) {
        for(var i in this.Items) {
            this.Items[i].hide();
        }
        this.Index = index;
        this.Items[this.Index].fadeIn("fast");
    },
    
    ToggleThumbnails: function() {
        this.Nav.ThumbsList.slideDown();
    },
    
    ThumbnailLoad: function(index, thumb) {
        thumb = jQuery(thumb);
        thumb.on("click", jQuery.proxy(this.ThumbnailClicked, this));
        this.Thumbs.push(thumb);
    },
    
    ThumbnailClicked: function(e) {
        e.preventDefault();
        var index = jQuery(e.currentTarget).index();
        this.GoTo(index);
        this.Nav.ThumbsList.hide();
    }
};

WPADVERTS.Single.Gallery.Item = function() {
    
};

WPADVERTS.Single.Player = function() {
    
};

jQuery(function($) {

    $(".wpadverts-video-player").each(function(index, item) {
        new WPADVERTS.Single.Player($(item));
    });

    $(".wpadverts-slides-list .wpadverts-slide").each(function(index, item) {
        WPADVERTS.Single.Gallery.Items.push($(item));
    });
    
    WPADVERTS.Single.Gallery.InitSlider($(".wpadverts-slide-nav"));
    WPADVERTS.Single.Gallery.GoTo(0);
});