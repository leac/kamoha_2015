<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package kamoha
 */
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <section class="error-404 not-found">
            <header class="page-header">
                <h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'kamoha_2015' ); ?></h1>
            </header><!-- .page-header -->

            <div class="entry-content">
                 <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php
                    $queried_post = get_post( POST_404_PAGE );
                    $content = $queried_post->post_content;
                    $content = apply_filters( 'the_content', $content );
                    $content = str_replace( ']]>', ']]&gt;', $content );
                    echo $content;
                    ?>
                </article>

            </div><!-- .entry-content -->
        </section><!-- .error-404 -->

    </main><!-- #main -->
</div><!-- #primary -->
<?php //get_sidebar (); ?>
<?php get_footer(); ?>