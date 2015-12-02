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
            <h1 class="error-404-title">
                <?php esc_html_e( '404', 'scientific-2016' ); ?>
            </h1>
            <div class="page-content">
                <p>
                    <span><?php esc_html_e( 'That page can&rsquo;t be found. ', 'kamoha' ); ?></span>
                </p>
                <p>
                    <?php esc_html_e( 'we&rsquo;re sure you&rsquo;ll find us again on', 'kamoha' ); ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php esc_html_e( 'home page', 'kamoha' ); ?></a>
                </p>

            </div><!-- .page-content -->
        </section><!-- .error-404 -->

    </main><!-- #main -->
</div><!-- #primary -->
<?php //get_sidebar (); ?>
<?php get_footer(); ?>