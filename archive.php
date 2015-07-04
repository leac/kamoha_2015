<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package kamoha
 */
get_header();
?>

<section id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <?php if ( have_posts() ) : ?>

            <header class="page-header">
                <h1 class="page-title">
                    <?php
                    if ( is_category() ) :
                        single_cat_title();

                    elseif ( is_tag() ) :
                        single_tag_title();

                    else :
                        _e( 'Archives', 'kamoha' );

                    endif;
                    ?>
                </h1>
                <?php
                // Show an optional term description.
                $term_description = term_description();
                if ( !empty( $term_description ) ) :
                    printf( '<div class="taxonomy-description">%s</div>', $term_description );
                endif;
                ?>
            </header><!-- .page-header -->

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

        <?php else : ?>

            <?php get_template_part( 'content', 'none' ); ?>

        <?php endif; ?>

    </main><!-- #main -->

    <?php /* Display navigation to next/previous pages when applicable. Must be outside main, so isn't affected by masonry 
     * Use plugin pagination, or core pagination if plugin doesn't exist  */ ?>
    <?php if ( $wp_query->max_num_pages > 1 ) : ?>
        <?php if ( function_exists( 'wp_pagenavi' ) ) : ?>
            <?php wp_pagenavi(); ?>
        <?php else: ?>
            <?php kamoha_paging_nav(); ?>
        <?php endif; ?>
    <?php endif; ?>

</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
