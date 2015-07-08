<?php
/**
 * The Template for displaying posts without headers, sidebars and footers.
 *
 * @package kamoha
 */

get_header('blank');

/* -----------------------------------------
 * Tal - Lea Jewish Calendar
 * ----------------------------------------- */

function kamoha_jewish_calendar_shortcode(){
    echo '<aside class="events_box" id="events_box">';
    echo '<section class="aside_content calendar_block clear">';
    kamoha_get_event_calendar();
    echo '</section>';
    echo '</aside>';
}

add_shortcode( "jewish_calendar_lea", 'kamoha_jewish_calendar_shortcode' );
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php while ( have_posts() ) : the_post(); ?>

            <?php the_content(); ?>

        <?php endwhile; // end of the loop. ?>

    </main><!-- #main -->
</div><!-- #primary -->