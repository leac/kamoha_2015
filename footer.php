<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package kamoha
 */
?>

</div><!-- #content -->

<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="site-info">
        <a href="<?php echo get_permalink( LINKS_AND_EMERGECY ) ?>"><?php echo get_the_title( LINKS_AND_EMERGECY ) ?></a>
        <span class="sep"> | </span>
        <a href="http://wordpress.org/"><?php printf( __( 'Proudly powered by %s', 'kamoha' ), 'WordPress' ); ?></a><!--  rel="generator" -->
        <span class="sep"> | </span>
        <a href="<?php echo get_permalink( ABOUT_PAGE ) . '#credits'; ?>"><?php _e( 'credits', 'kamoha' ) ?></a>
        <?php if ( is_user_logged_in() ) { ?>
            <span class="sep"> | </span>
            <a href="<?php echo get_permalink( DOCUMENTATION_PAGE ) ?>"><?php echo get_the_title( DOCUMENTATION_PAGE ) ?></a>
            <span class="sep"> | </span>
            <a href="<?php echo get_category_link( MEETINGS_CAT ) . '&futureposts=1' ?>"><?php echo get_cat_name( MEETINGS_CAT ) . ' ' . __( 'future', 'kamoha' ) ?></a>
        <?php } ?>
    </div><!-- .site-info -->
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>