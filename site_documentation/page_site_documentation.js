/* 
 * JS file for page_site_documentation.php page template
 */

jQuery(document).ready(function () {
    // imagemapster:
    var img = jQuery('img');
    img.mapster({
        fillOpacity: 0.7,
        strokeColor: '3d6998',
        strokeWidth: 3,
        render_highlight: {
            fillColor: '839DB6',
            stroke: true
        },
        render_select: {
            fill: false,
            stroke: false
        },
        fadeInterval: 50
    });
    // colorbox:
    jQuery('area').colorbox({inline: true, width: "50%"});
});


