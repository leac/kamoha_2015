<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package kamoha
 */
get_header();
?>

<section id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php if ( have_posts() ) : ?>

            <header class="page-header">
                <h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'kamoha' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
            </header><!-- .page-header -->

            <?php /* Start the Loop */ ?>
            <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'content', 'search' ); ?>

            <?php endwhile; ?>

            <?php /* Display navigation to next/previous pages when applicable */ ?>
            <?php if ( $wp_query->max_num_pages > 1 && function_exists( 'wp_pagenavi' ) ) : ?>
                <?php wp_pagenavi(); ?>
            <?php endif; ?>

        <?php else : ?>

            <?php get_template_part( 'content', 'none' ); ?>

        <?php endif; ?>

    </main><!-- #main -->
</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
