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
        <title><?php wp_title(); ?></title>
        <?php wp_head(); ?>
    </head>
    <body>
        <div class="content">
            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'content', 'page' ); ?>

            <?php endwhile; // end of the loop. ?>
        </div>
        <?php wp_footer(); ?>
    </body>
</html>
