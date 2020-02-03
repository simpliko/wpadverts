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
        this.Nav.Nav = nav;
        this.Nav.Current = nav.find(".wpadverts-slide-nav-current");
        this.Nav.Thumbnails = nav.find(".wpadverts-slide-nav-thumbnails");
        this.Nav.ThumbsList = nav.find(".wpadverts-slide-nav-thumbnails-list");
        this.Nav.ThumbsClose = nav.find(".wpadverts-slide-nav-thumbnails-close");
        this.Nav.Prev = nav.find(".wpadverts-slide-nav-paginate-left");
        this.Nav.Next = nav.find(".wpadverts-slide-nav-paginate-right");
        
        this.Nav.Next.on("click", jQuery.proxy(this.Next, this));
        this.Nav.Prev.on("click", jQuery.proxy(this.Prev, this));
        
        this.Nav.Thumbnails.on("click", jQuery.proxy(this.ToggleThumbnails, this));
        
        this.Nav.ThumbsList.find("li").each(jQuery.proxy(this.ThumbnailLoad, this));
        this.Nav.ThumbsClose.on("click", jQuery.proxy(this.ThumbnailClose, this));

    },
    
    InitAls: function(als) {

        var container = als;
        var als = jQuery('#wpadverts-rsliders-controls');
        var SlickSettings = {
            infinite: false,
            slidesToShow: parseInt(adverts_frontend_lang.als_visible_items),
            slidesToScroll: parseInt(adverts_frontend_lang.als_scrolling_items),
            prevArrow: '.als-nav-wrap-left',
            nextArrow: '.als-nav-wrap-right',
            responsive: [
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                }
            ]
        };

        if(typeof wpadverts_slick_override !== 'undefined' ) {
            jQuery.extend( SlickSettings, wpadverts_slick_override );
        }

        this.Nav.Als = als;
        
        this.Nav.Als.slick( SlickSettings );
        
        this.Nav.Als.find(".wpadverts-als-item a").each(jQuery.proxy(this.AlsItem, this));
        this.Nav.Als.find(".wpadverts-als-item:first-child a").click();
        
        if(this.Items.length <= 1) {
            container.hide();
            return;
        }
    },
    
    AlsItem: function(index, item) {
        jQuery(item).on("click", this.AlsItemClick);
    },
    
    AlsItemClick: function(e) {
        if(typeof e !== "undefined") {
            e.preventDefault();
        }

        var id = jQuery(this).data("advert-slide");

        jQuery(".wpadverts-slides-list .wpadverts-slide").hide();
        jQuery(".wpadverts-slides-list #"+id).fadeIn("fast");
    },
    
    Prev: function(e) {
        if(this.Index <= 0) {
            return;
        }
        
        for(var i in this.Items) {
            if(typeof this.Items[parseInt(i)] === "object" && typeof this.Items[parseInt(i)].hide === "function") {
                this.Items[parseInt(i)].hide();
            }
        }
        this.Index--;
        this.Items[this.Index].fadeIn("fast");
        
        this.Nav.Current.text((this.Index+1).toString());
        this.TogglePrevNext();
    },
    
    Next: function(e) {
        if(this.Index+1 >= this.Items.length) {
            return;
        }
        
        for(var i in this.Items) {
            if(typeof this.Items[parseInt(i)] === "object" && typeof this.Items[parseInt(i)].hide === "function") {
                this.Items[parseInt(i)].hide();
            }
        }
        this.Index++;
        this.Items[this.Index].fadeIn("fast");
        
        this.Nav.Current.text((this.Index+1).toString());
        this.TogglePrevNext();
    },
    
    GoTo: function(index) {
        for(var i in this.Items) {
            if(typeof this.Items[parseInt(i)] === "object" && typeof this.Items[parseInt(i)].hide === "function") {
                this.Items[parseInt(i)].hide();
            }
        }
        this.Index = index;
        this.Items[this.Index].fadeIn("fast");
        
        this.Nav.Current.text((this.Index+1).toString());
        this.TogglePrevNext();
    },
    
    ToggleThumbnails: function() {
        this.Nav.ThumbsList.slideDown();
    },
    
    ThumbnailLoad: function(index, thumb) {
        thumb = jQuery(thumb);
        thumb.on("click", jQuery.proxy(this.ThumbnailClicked, this));
        this.Thumbs.push(thumb);
    },
    ThumbnailClose: function() {
        this.Nav.ThumbsList.hide();
    },
    ThumbnailClicked: function(e) {
        e.preventDefault();
        var index = jQuery(e.currentTarget).index();
        this.GoTo(index);
        this.Nav.ThumbsList.hide();
    },
    
    HideNavigation: function() {
        if(this.Nav.Nav && this.Nav.Nav.length > 0) {
            this.Nav.Nav.hide();
        }
    },
    
    ShowNavigation: function() {
        if(this.Nav.Nav && this.Nav.Nav.length > 0) {
            this.Nav.Nav.show();
        }
    },
    
    TogglePrevNext: function() {
        if(this.Index === 0) {
            this.Nav.Prev.hide();
        } else {
            this.Nav.Prev.show();
        }
        
        if(this.Index+1 >= this.Items.length) {
            this.Nav.Next.hide();
        } else {
            this.Nav.Next.show();
        }
    }
};

WPADVERTS.Single.Gallery.Item = function() {
    
};

WPADVERTS.Single.Player = function(player) {
    
    this.Nav = {
        Player: player,
        Video: player.find("video"),
        Caption: player.find(".wpadverts-slide-caption"),
        Nav: player.find(".wpadverts-player"),
        Play: player.find(".wpadverts-player-play"),
        Pause: player.find(".wpadverts-player-pause"),
        Replay: player.find(".wpadverts-player-replay"),
        VolumeDown: player.find(".wpadverts-player-volume-down"),
        VolumeUp: player.find(".wpadverts-player-volume-up"),
        Progress: player.find(".wpadverts-player-progress"),
        ProgressBar: player.find(".wpadverts-player-item-progress-bar"),
        ProgressText: player.find(".wpadverts-player-item-progress-text"),
        Shadow: player.find(".wpadverts-slide-with-shadow"),
        FullScreen: player.find(".wpadverts-player-fullscreen"),
        Swipe: player.find(".wpadverts-swipe")
    };
    
    this.video = this.Nav.Video[0];
    
    if(!this.video.canPlayType) {
        this.Nav.Nav.hide();
        return;
    }
    
    this.Nav.Pause.hide();
    this.Nav.Replay.hide();
    
    var clickEvent = "click";
    
    if(this.IsTouchDevice()) {
        clickEvent = "touchstart";
    }

    this.Nav.Play.on(clickEvent, jQuery.proxy(this.PlayPause, this));
    this.Nav.Pause.on(clickEvent, jQuery.proxy(this.PlayPause, this));
    this.Nav.Replay.on(clickEvent, jQuery.proxy(this.PlayPause, this));
    
    this.Nav.VolumeDown.on(clickEvent, jQuery.proxy(this.VolumeDown, this));
    this.Nav.VolumeUp.on(clickEvent, jQuery.proxy(this.VolumeUp, this));
    
    this.Nav.ProgressBar.on(clickEvent, jQuery.proxy(this.Seek, this));
    
    this.Nav.Video.on(clickEvent, jQuery.proxy(this.PlayPause, this));

    this.video.addEventListener("timeupdate", jQuery.proxy(this.TimeUpdate, this));
    this.video.addEventListener("ended", jQuery.proxy(this.Ended, this));
    this.video.addEventListener("loadedmetadata", jQuery.proxy(this.LoadedMetaData, this));
    
    this.Nav.Player.on("mouseenter", jQuery.proxy(this.ShowInterface, this));
    this.Nav.Player.on("mouseleave", jQuery.proxy(this.HideInterface, this));
    
    if(this.Nav.Swipe.length > 0) {
        this.Nav.FullScreen.on("click", jQuery.proxy(this.FullScreen, this));
    }
};

WPADVERTS.Single.Player.prototype.IsTouchDevice = function() {
  return 'ontouchstart' in window        // works on most browsers 
      || navigator.maxTouchPoints;       // works on IE10/11 and Surface
};

WPADVERTS.Single.Player.prototype.PlayPause = function() {
    if (this.video.paused || this.video.ended) {
        this.video.play();
        this.Nav.Play.hide();
        this.Nav.Replay.hide();
        this.Nav.Pause.show();
        this.Nav.Player.addClass("wpadverts-video-is-playing");
        WPADVERTS.Single.Gallery.HideNavigation();
    } else {
        this.Pause();
    }
};

WPADVERTS.Single.Player.prototype.Pause = function() {
    this.video.pause();
    this.Nav.Play.show();
    this.Nav.Pause.hide();
    this.Nav.Replay.hide();
    this.Nav.Player.removeClass("wpadverts-video-is-playing");
    WPADVERTS.Single.Gallery.ShowNavigation();
};

WPADVERTS.Single.Player.prototype.Ended = function() {
    this.Nav.Play.hide();
    this.Nav.Pause.hide();
    this.Nav.Replay.show();
    this.Nav.Player.removeClass("wpadverts-video-is-playing");
    WPADVERTS.Single.Gallery.ShowNavigation();
};

WPADVERTS.Single.Player.prototype.VolumeDown = function() {
    var volume = Math.floor(this.video.volume * 10) / 10;
    
    if(volume > 0) {
        this.video.volume -= 0.1;
    }
};

WPADVERTS.Single.Player.prototype.VolumeUp = function() {
    var volume = Math.floor(this.video.volume * 10) / 10;
    
    if(volume < 1) {
        this.video.volume += 0.1;
    }
};

WPADVERTS.Single.Player.prototype.Seek = function(e) {
    var offset = this.Nav.ProgressBar.offset();
    var clickPos = offset.left;
    
    if(typeof e.pageX !== "undefined") {
        clickPos = e.pageX;
    } else if( typeof e.originalEvent.touches[0].pageX !== undefined ) {
        clickPos = e.originalEvent.touches[0].pageX;
    }
    
    var pos = (clickPos  - offset.left) / this.Nav.ProgressBar[0].offsetWidth;
    this.video.currentTime = pos * this.video.duration;
    
    if(this.video.paused) {
        this.Nav.Play.show();
        this.Nav.Replay.hide();
        this.Nav.Pause.hide();
    } else {
        this.Nav.Play.hide();
        this.Nav.Replay.hide();
        this.Nav.Pause.show();
    }
};

WPADVERTS.Single.Player.prototype.FullScreen = function(e) {
    this.Pause();
    this.Nav.Swipe.click();
};

WPADVERTS.Single.Player.prototype.TimeUpdate = function() {
    this.Nav.Progress.css("width", Math.floor((this.video.currentTime / this.video.duration) * 100) + '%');
   
    var pad = "00";
   
    var totalMinutes = parseInt(this.video.duration / 60, 10).toString();
    var totalSeconds = Math.round(this.video.duration % 60, 2).toString();
    var currentMinutes = parseInt(this.video.currentTime / 60, 10).toString();
    var currentSeconds = Math.round(this.video.currentTime % 60, 2).toString();
    
    var tm = pad.substring(0, pad.length - totalMinutes.length) + totalMinutes;
    var ts = pad.substring(0, pad.length - totalSeconds.length) + totalSeconds;
    var cm = pad.substring(0, pad.length - currentMinutes.length) + currentMinutes;
    var cs = pad.substring(0, pad.length - currentSeconds.length) + currentSeconds;
   
    this.Nav.ProgressText.text(cm+":"+cs+" / "+tm+":"+ts);
};

WPADVERTS.Single.Player.prototype.LoadedMetaData = function() {
    var minutes = parseInt(this.video.duration / 60, 10);
    var seconds = Math.round(this.video.duration % 60, 2);
    
    this.Nav.ProgressText.text(minutes.toString() + ":" + seconds.toString());
};

WPADVERTS.Single.Player.prototype.ShowInterface = function() {
    this.Nav.Nav.show();
    this.Nav.Caption.show();
};

WPADVERTS.Single.Player.prototype.HideInterface = function() {
    if (!this.video.paused) {
        this.Nav.Nav.hide();
        this.Nav.Caption.hide();
    }
};

jQuery(function($) {

    $(".wpadverts-video-player").each(function(index, item) {
        new WPADVERTS.Single.Player($(item));
    });

    $(".wpadverts-slides-list .wpadverts-slide").each(function(index, item) {
        WPADVERTS.Single.Gallery.Items.push($(item));
    });
    
    if($(".wpadverts-als-container").length > 0) {
        WPADVERTS.Single.Gallery.InitAls($(".wpadverts-als-container"));
    } else {
        WPADVERTS.Single.Gallery.InitSlider($(".wpadverts-slide-nav"));
        WPADVERTS.Single.Gallery.GoTo(0);
    }
});