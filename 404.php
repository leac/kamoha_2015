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
                <h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'kamoha' ); ?></h1>
            </header><!-- .page-header -->

            <div class="page-content">
                <!--<p><?php //_e ( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?' , 'kamoha' );   ?></p>-->
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php
                    $queried_post = get_post( POST_404_PAGE );
                    $content = $queried_post->post_content;
                    $content = apply_filters( 'the_content', $content );
                    $content = str_replace( ']]>', ']]&gt;', $content );
                    echo $content;
                    ?>
                </article>

            </div><!-- .page-content -->
        </section><!-- .error-404 -->

    </main><!-- #main -->
</div><!-- #primary -->
<?php //get_sidebar (); ?>
<?php get_footer(); ?>