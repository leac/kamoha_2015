<?php
/*
  Template Name: עמוד ללא כותרת עליונה ותחתונה
 */
?>
<!DOCTYPE html>
<html lang="he-IL" dir="rtl">
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class( 'clear' );  // clear class is added so the footer and its bottom margin fit into the body.       ?>>
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