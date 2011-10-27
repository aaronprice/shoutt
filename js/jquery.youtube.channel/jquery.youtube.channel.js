/**
 *  Plugin which renders the YouTube channel videos list to the page
 *  @author:  H. Yankov (hristo.yankov at gmail dot com)
 *  @version: 1.0.0 (Nov/27/2009)
 *    http://yankov.us
 *  
 *  Modified my Dan Hounshell (Jan/2010) to work for favorites or 
 *  uploads feeds and simplified output 
 */

 var __mainDiv;
 var __preLoaderHTML;
 var __opts;

 function __jQueryYouTubeChannelReceiveData(data) {

     var cnt = 0;

     $.each(data.feed.entry, function(i, e) {
         if (cnt < __opts.numberToDisplay) {
             var parts = e.id.$t.split('/');
             var videoId = parts[parts.length-1];
             var out = '<a href="' + 
                  e.link[0].href + '" title="' + e.title.$t.replace('.flv', '') + '"><img src="http://i.ytimg.com/vi/' + 
                  videoId + '/0.jpg"';
 			 if ( __opts.thumbWidth != null )
				out = out + ' width="' + __opts.thumbWidth + '"';
			 if ( __opts.thumbHeight != null )
				out = out + ' height="' + __opts.thumbHeight + '"';
			 out = out + ' /></a>';
             if (!__opts.hideAuthor) {
                 out = out + '<span>Author: ' + e.author[0].name.$t + '</span>';
             }
             __mainDiv.append(out);
             cnt = cnt + 1;
         }
     });
            
    // Open in new tab?
    if (__opts.linksInNewWindow) {
        $(__mainDiv).find("a").attr("target", "_blank");
    }
    
    // Remove the preloader and show the content
    $(__preLoaderHTML).remove();
    __mainDiv.show();
}

                
(function($) {
    $.fn.youTubeChannel = function(options) {
        var videoDiv = $(this);

        $.fn.youTubeChannel.defaults = {
            userName: null,
            channel: "uploads", //options are favorites or uploads
            loadingText: "Loading...",
            numberToDisplay: 3,
            linksInNewWindow: true,
            hideAuthor: true,
			thumbWidth: null,
			thumbHeight: null,
			fancybox: true
        }

        __opts = $.extend({}, $.fn.youTubeChannel.defaults, options);

        return this.each(function() {
            if (__opts.userName != null) {
                videoDiv.append('<div id="channel_div"></div>');
                __mainDiv = $("#channel_div");
                __mainDiv.hide();

                __preLoaderHTML = $('<p class="loader">' + 
                    __opts.loadingText + '</p>');
                videoDiv.append(__preLoaderHTML);

                // TODO: Error handling!
                $.ajax({
                    url: "http://gdata.youtube.com/feeds/base/users/" + 
                        __opts.userName + "/" + __opts.channel + "?alt=json",
                    cache: true,
                    dataType: 'jsonp',                    
                    success: __jQueryYouTubeChannelReceiveData
                });
            }
        });
    };
})(jQuery);