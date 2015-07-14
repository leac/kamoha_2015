<?php
/*
  Template Name: עמוד ללא כותרת עליונה ותחתונה
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
    <body <?php body_class( 'clear' );  // clear class is added so the footer and its bottom margin fit into the body.    ?>>
        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">

                <?php while ( have_posts() ) : the_post(); ?>

                    <?php get_template_part( 'content', 'page' ); ?>

                <?php endwhile; // end of the loop.  ?>

            </main><!-- #main -->
        </div><!-- #primary -->

        <?php wp_footer(); ?>
    </body>
</html>