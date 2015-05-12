jQuery(document).ready(function($) {
    //put IDs for sidebar and toggle in vars
    var toggle = "#menu-toggle";
    var slideout = "#secondary";
    //calculate the width of the mobile sidebar and put inverse in var
    var slideoff = -Math.abs($(slideout).width());
    //add margin-left to mobile sidebar to push off canvas
    //$( slideout).css( "marginRight", slideoff );

    //When toggle is clicked show the slideout
    $(toggle).on('click', function() {
        if ($(slideout).hasClass('hide') || $(slideout).css('display') === 'none') { /*when window first loads, the hide class is not on the slideout element*/
            $(slideout).removeClass('hide').addClass('unhide').animate({//open the sidebar
                opacity: 1,
                marginLeft: 0,
            }, 500, function() {
                $('.site-main').css('z-index', '-1'); /*needed for inner page, otherwise the widget area is obfuscated when open*/
            });
            // move the button too
            $(this).animate({
                left: $(slideout).css('width')
            }, 500, function() {
                //I can haz callback?
            });
        } else { //close the sidebar
            $(slideout).animate({
                opacity: 0,
                marginLeft: slideoff,
                //display: block,
            }, 500, function() {
                $(slideout).addClass('hide').removeClass('unhide');
                $('.site-main').css('z-index', '0'); /* return to zero, otherwise links in main area aren't clickable*/
            });
            // move the button too
            $(this).animate({
                left: 0 /*return button to original poistion*/
            }, 500, function() {
                //I can haz callback?
            });
        }
    }); //end slideout function
}); //end noConflict