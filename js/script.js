/* this part of the code has to run outside (document).ready.
 * This code checks the width of the window. 
 * If it's wider that 800px, hide the 2 latest posts in the newest-posts block. Else don't hide.
 * In CSS, when width is less that 800px, we hide the showMore button of newest posts, and all 7 posts are shown.*/
var mql = window.matchMedia("screen and (min-width: 800px)");
mql.addListener(handleResizeChange);
handleResizeChange(mql);
function handleResizeChange(mql) {
    if (mql.matches) {
        /* the view port is at least 800 pixels wide */
        /* toggle show and hide of posts, comments and events - begin*/
        // hide 2 last posts
        jQuery("#morePosts").prevUntil('.hentry:nth-child(5)').addClass('hide');
    }
}

//set the container that Masonry will be inside of in a var
var container = document.querySelector('.site-main'); // define container here so can be used by infinite scroll too
jQuery(document).ready(function ($) {

    /***********************************************
     * The MORE (posts, comments) button
     ***********************************************/

    /* toggle show and hide of posts, comments and events - begin*/
    // hide any more than 3 next events or comments. 
    $("#secondary .showMore").prev().children(':nth-child(3)').nextAll().addClass('hide');

    //more posts toggle  
    $(".showMore").click(function () {
        /* get the relevant items to show/hide by parent. The logic for getting them is as follows:
         * In the primary area, the showMore button is right after the relevant elements
         * In the secondary area, the elements are in a list, and the button follows the list */
        var items;
        if ($(this).parents('#primary').length) {
            items = $(this).siblings('.hide');
            if (items.length === 0) { // if the items aren't hidden, then they're highlighted
                items = $(this).siblings('.highlight');
            }
        } else {
            if ($(this).parents('#secondary').length) {
                items = $(this).prev().children('.hide');
                if (items.length === 0) { // if the items aren't hidden, then they're highlighted
                    items = $(this).prev().children('.highlight');
                }
            }
        }

        if (items.length > 0)
        {
            if (items.eq(0).hasClass('hide') > 0) // check if these are hidden items. If so, show them
            {
                // slide down the posts
                items.slideDown("slow");
                // remove the hide class and replace is with highlight, so we can put the hide class back
                items.removeClass('hide').addClass('highlight');
                // change class of morePosts button
                $(this).removeClass("toOpen").addClass("toClose");
                $(this).html(MyScriptParams.sUnloadPosts); //change button text
            } else // these are shown items. The click is meant to hide them.
            {
                // slide up the posts
                items.slideUp("slow");
                // remove the hide class and replace is with highlight, so we can put the hide class back
                items.removeClass('highlight').addClass('hide');
                // change class of morePosts button
                $(this).removeClass("toClose").addClass("toOpen");
                $(this).html(MyScriptParams.sLoadPosts);
            }
        }
    });

    /***********************************************
     * Hide comments form 
     ***********************************************/
    $('#respond').addClass('hide');
    $(".comment-form-btn, .comment-reply-link").click(function () {
        $('#respond').removeClass('hide');
        //$(this).addClass('hide');
    });



    /***********************************************
     * Masonry  
     ***********************************************/

    //set the container that Masonry will be inside of in a var
    var container = document.querySelector('.archive .site-main');
    if (container) {
        //create empty var msnry
        var msnry;
        if (typeof (imagesLoaded) === 'function') {
            // initialize Masonry after all images have loaded
            imagesLoaded(container, function () {
                msnry = new Masonry(container, {
                    // masonry should be rtl except in english and french categories
                    isOriginLeft: ($('.category-lang-en').length === 0 && $('.category-francais').length === 0) ? false : true
                });
            });
        }
    }


    /* set masonry on table of contents. set the column width because we have different widths.
     * they all have to be multipliers of the column width set here. 
     * Because they're in percents, we have to set percentPosition 
     * http://www.bloggerever.com/2014/09/understanding-masonry-plugin-for.html */
    if ($('.kamoha_toc').length > 0) {
        $('.kamoha_toc').masonry({
            itemSelector: '.kamoha_toc > li',
            columnWidth: '.kamoha_toc > li',
            percentPosition: true,
            isOriginLeft: false
        });
    }


    /***********************************************
     * AJAX for calendar
     ***********************************************/

    /* Ajaxify the next/prev month links under the calendar
     * Bind the event to the parent div, since all the contents of that div - including the links -
     * get overwritten by the result from the back end */
    $(".calendar_block").on("click", ".ajax-link", function () {
        month = $(this).attr("href");
        $('.calendar_block').html('<div class="loader">Loading...</div>');
        var data = {
            action: 'calendar_response',
            month: month
        };
        type : "post",
                // the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
                $.post(the_ajax_script.ajaxurl, data, function (response) {
                    $('.calendar_block').html(response);
                });
        return false;
    });

    /***********************************************
     * The Read More button in the registration form
     ***********************************************/
    var $el, $ps, $up, totalHeight;

    jQuery(".sidebar-box .kamoha_btn").click(function () {

        totalHeight = 0

        $el = jQuery(this);
        $p = $el.parent();
        $up = $p.parent();
        $ps = $up.find("p:not('.read-more')");

        // measure how tall inside should be by adding together heights of all inside paragraphs (except read-more paragraph)
        $ps.each(function () {
            totalHeight += jQuery(this).outerHeight(true);
        });

        $up
                .css({
                    // Set height to prevent instant jumpdown when max height is removed
                    "height": $up.outerHeight(true),
                    "max-height": 9999
                })
                .animate({
                    "height": totalHeight
                });

        // fade out read-more
        $p.fadeOut();

        // prevent jump-down
        return false;

    });

    /***********************************************
     * Body class to distinguish IE
     ***********************************************/

//    var doc = document.documentElement;
//    doc.setAttribute('data-useragent', navigator.userAgent);


    /***********************************************
     * JS from old kamoha theme
     ***********************************************/

    /* This is so in Ask The Rebbi page, when filling out the form, the first option in the select box won't be automatically selected. Copied from old theme.*/
    $(".tffaq-ask-selector").val([]);

    /***********************************************
     * Registration form - in rules checkbox, move "required" symbol to appear before validation message
     ***********************************************/
    $(".checkbox-806 .required-symbol ").insertAfter(".checkbox-806 .wpcf7-list-item");

    /***********************************************
     * For images in blog category page - give class that will indicate that mix-blend-mode CSS will be applied to featured image
     ***********************************************/
    if (window.getComputedStyle(document.body).mixBlendMode !== undefined) {
        $(".category .post").addClass("has-mix-blend-mode");
    }

});

/********* These functions are needed for signing on joining the organization. Copied from old theme ***************/
function colorinput(trigger)
{
    var el = document.getElementById(trigger);
    if (el.value.length == 0)
    {
        el.style.backgroundColor = 'red';
    } else
    {
        el.style.backgroundColor = '';
    }
}

function checkInput(trigger)
{
    switch (trigger)
    {
        case 'firstname':
        {
            colorinput(trigger);
            break;
        }
        case 'lastname':
            colorinput(trigger);
            break;
        case 'nickname':
            colorinput(trigger);
            break;
        case 'id_number':
            colorinput(trigger);
            break;
        case 'city':
            colorinput(trigger);
            break;
        case 'email':
            colorinput(trigger);
            break;
        case 'cellphone':
            colorinput(trigger);
            break;
        case 'hebdate':
            colorinput(trigger);
            break;
        case 'georgedate':
            colorinput(trigger);
            break;
        case 'age':
            colorinput(trigger);
            break;
        case 'elem_school':
            var el = jQuery('input[name=elem_school]:checked').val();
            if (el == 'other')
            {
                var text = document.getElementById('elem_school_text');
                text.disabled = false;
            } else
            {
                var text = document.getElementById('elem_school_text');
                text.disabled = true;
            }
            break;
        case 'high_school':
            var el = jQuery('input[name=high_school]:checked').val();
            if (el == 'other')
            {
                var text = document.getElementById('high_school_text');
                text.disabled = false;
            } else
            {
                var text = document.getElementById('high_school_text');
                text.disabled = true;
            }
            break;
        case 'after_high_school':
            var el = jQuery("input[id='after_high_school'][value='other']").attr('checked');
            if (el == "checked")
            {
                var text = document.getElementById('after_high_school_text');
                text.disabled = false;
            } else
            {
                var text = document.getElementById('after_high_school_text');
                text.disabled = true;
            }
            break;
        case 'job':
            var el = jQuery("input[id='job'][value='other']").attr('checked');
            var count = jQuery('[id="job"]:checked').length;
            if (el == "checked")
            {
                var text = document.getElementById('job_text');
                if (text.value != '')
                {
                    text.style.backgroundColor = '';
                } else
                {
                    text.style.backgroundColor = 'red';
                }
                text.disabled = false;
            } else
            {
                var text = document.getElementById('job_text');
                text.disabled = true;
                text.style.backgroundColor = '';
            }
            if (count > 0)
            {
                document.getElementById('radio-red').style.backgroundColor = '';
            } else
            {
                document.getElementById('radio-red').style.backgroundColor = 'red';
            }
            break;
        default:
            break;
    }

    if (document.getElementById('firstname').style.backgroundColor == '' &&
            document.getElementById('lastname').style.backgroundColor == '' &&
            document.getElementById('nickname').style.backgroundColor == '' &&
            document.getElementById('id_number').style.backgroundColor == '' &&
            document.getElementById('city').style.backgroundColor == '' &&
            document.getElementById('email').style.backgroundColor == '' &&
            document.getElementById('cellphone').style.backgroundColor == '' &&
            document.getElementById('hebdate').style.backgroundColor == '' &&
            document.getElementById('georgedate').style.backgroundColor == '' &&
            document.getElementById('age').style.backgroundColor == '' &&
            jQuery('[id="job"]:checked').length != 0 &&
            (jQuery("input[id='job'][value='other']").attr('checked') == false ||
                    document.getElementById('job_text').style.backgroundColor == '') &&
            document.getElementById("sign_canvas").style.border == '1px solid black')
    {
        var submit_form = document.getElementById('submit_form');
        submit_form.disabled = false;
        var fillall = document.getElementById('fillall');
        fillall.style.color = 'white';
    } else
    {
        var submit_form = document.getElementById('submit_form');
        submit_form.disabled = true;
        var fillall = document.getElementById('fillall');
        fillall.style.color = 'red';
    }
}

function clear_canvas() {
    var canvas = document.getElementById("sign_canvas");
    canvas.style.border = '3px solid red';
    checkInput('aaa');
    var context = canvas.getContext('2d');

    // draw
    context.clearRect(0, 0, 190, 90);
}

// Author: Richard Garside - www.nogginbox.co.uk [2010]

var cb_canvas = null;
var cb_ctx = null;
var cb_lastPoints = null;
var cb_easing = 0.4;

// Setup event handlers
window.onload = init;
function init(e) {
    cb_canvas = document.getElementById("sign_canvas");

    cb_lastPoints = Array();

    if (cb_canvas != null && cb_canvas.getContext) {
        cb_ctx = cb_canvas.getContext('2d');
        cb_ctx.lineWidth = 2;
        cb_ctx.strokeStyle = "rgb(0, 0, 0)";
        cb_ctx.beginPath();

        cb_canvas.onmousedown = startDraw;
        cb_canvas.onmouseup = stopDraw;
        cb_canvas.ontouchstart = startDraw;
        cb_canvas.ontouchstop = stopDraw;
        cb_canvas.ontouchmove = drawMouse;
    }
}

function startDraw(e) {
    if (e.touches) {
        // Touch event
        for (var i = 1; i <= e.touches.length; i++) {
            cb_lastPoints[i] = getCoords(e.touches[i - 1]); // Get info for finger #1
        }
    } else {
        // Mouse event
        cb_lastPoints[0] = getCoords(e);
        cb_canvas.onmousemove = drawMouse;
    }

    return false;
}

// Called whenever cursor position changes after drawing has started
function stopDraw(e) {
    e.preventDefault();
    cb_canvas.onmousemove = null;
}

function drawMouse(e) {
    if (e.touches) {
        // Touch Enabled
        for (var i = 1; i <= e.touches.length; i++) {
            var p = getCoords(e.touches[i - 1]); // Get info for finger i
            cb_lastPoints[i] = drawLine(cb_lastPoints[i].x, cb_lastPoints[i].y, p.x, p.y);
        }
    } else {
        // Not touch enabled
        var p = getCoords(e);
        cb_lastPoints[0] = drawLine(cb_lastPoints[0].x, cb_lastPoints[0].y, p.x, p.y);
    }
    cb_ctx.stroke();
    cb_ctx.closePath();
    cb_ctx.beginPath();

    return false;
}

// Draw a line on the canvas from (s)tart to (e)nd
function drawLine(sX, sY, eX, eY) {
    cb_ctx.moveTo(sX, sY);
    cb_ctx.lineTo(eX, eY);
    var canvas = document.getElementById("sign_canvas");
    canvas.style.border = '1px solid black';
    checkInput('aaa');
    return {x: eX, y: eY};
}

// Get the coordinates for a mouse or touch event
function getCoords(e) {
    if (e.offsetX) {
        return {x: e.offsetX, y: e.offsetY};
    }
//	else if (e.layerX) {
//		return { x: e.layerX, y: e.layerY };
//	}
    else {
        return {x: e.pageX - cb_canvas.offsetLeft, y: e.pageY - cb_canvas.offsetTop};
    }
}

function dataURItoBlob(dataURI) {
    // convert base64 to raw binary data held in a string
    // doesn't handle URLEncoded DataURIs
    var byteString;
    if (dataURI.split(',')[0].indexOf('base64') >= 0)
        byteString = atob(dataURI.split(',')[1]);
    else
        byteString = unescape(dataURI.split(',')[1]);

    // separate out the mime component
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]

    // write the bytes of the string to an ArrayBuffer
    var ab = new ArrayBuffer(byteString.length);
    var ia = new Uint8Array(ab);
    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }

    // write the ArrayBuffer to a blob, and you're done
    var dataView = new DataView(ab);
    var blob = new Blob([dataView], {type: mimeString});
    return (blob);
}

function post_canvas()
{
    var canvas = document.getElementById("sign_canvas");
    var dataurl = canvas.toDataURL('image/jpg');
    var hiddencanvas = document.getElementById("hidden_canvas");
    hiddencanvas.value = dataurl;
}
