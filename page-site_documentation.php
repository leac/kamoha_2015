<?php
/*
  Template Name: דף הסברים על האתר
 */
?>
<!DOCTYPE html>
<html lang="he-IL" dir="rtl">
    <head>
        <meta name="generator"
              content="HTML Tidy for HTML5 (experimental) for Windows https://github.com/w3c/tidy-html5/tree/c63cc39" />
        <meta charset="UTF-8" />
        <title>מסמך הסבר לתבנית החדשה של כמוך</title>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/site_documentation/colorbox.css">
        <style>
            body{width: 1250px; margin: 0 auto;background: url('http://www.kamoha.org.il/wp-content/themes/kamoha_2015/images/pattern.png');}
            img{display: block;}
            #explenation{    
                background-color: #839db9;
                box-shadow: 0 2px 8px 0 #aaa;
                font-size: 20px;
                padding: 0 15px;
            }

            #cboxLoadedContent {
                margin-bottom: 28px;
                padding: 0 15px;
            }

            #cboxClose{
                bottom: auto;
                right: auto;
                top: 0;
                left: 0;
            }

            .section-title {
                font-size: 22px;
                font-weight: bold;
                color: #3d6998;
            }
            .section img {
                max-width: 100%;
            }
            .key {
                font-weight: bold;
                margin-top: 29px;
            }
        </style>
    </head>
    <body>
        <div class="content">
            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'content', 'page' ); ?>

            <?php endwhile; // end of the loop. ?>
            <!-- image mapster taken from here: http://www.outsharked.com/imagemapster-->
            <script src="<?php echo get_template_directory_uri(); ?>/site_documentation/jquery-2.1.1.min.js"></script> 
            <script src="<?php echo get_template_directory_uri(); ?>/site_documentation/jquery.imagemapster.min.js"></script> 
            <script src="<?php echo get_template_directory_uri(); ?>/site_documentation/jquery.colorbox-min.js"></script> 
            <script>
                $(document).ready(function () {
                    // imagemapster:
                    var img = $('img');
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
                    $('area').colorbox({inline: true, width: "50%"});
                });
            </script>
        </div>
    </body>
</html>
