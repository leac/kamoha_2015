<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package kamoha
 */
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php if ( have_posts() ) : ?>

            <?php /* Start the Loop */ ?>
            <?php while ( have_posts() ) : the_post(); ?>

                <?php
                /* Include the Post-Format-specific template for the content.
                 * If you want to override this in a child theme, then include a file
                 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                 */
                get_template_part( 'content', get_post_format() );
                ?>

            <?php endwhile; ?>

            <?php /* Display navigation to next/previous pages when applicable. Must be outside main, so isn't affected by masonry 
             * Use plugin pagination, or core pagination if plugin doesn't exist  */ ?>
            <?php if ( $wp_query->max_num_pages > 1 ) : ?>
                <?php if ( function_exists( 'wp_pagenavi' ) ) : ?>
                    <?php wp_pagenavi(); ?>
                <?php else: ?>
                    <?php kamoha_paging_nav(); ?>
                <?php endif; ?>
            <?php endif; ?>

        <?php else : ?>

            <?php get_template_part( 'content', 'none' ); ?>

        <?php endif; ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
