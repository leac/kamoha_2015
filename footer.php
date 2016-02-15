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
        <nav id="footor-navigation" class="footor-navigation clear" role="navigation">
            <?php wp_nav_menu( array('theme_location' => 'footor_logged_out', 'menu_class' => 'clear logged-in-nav-list footer-nav-list', 'container' => '') ); ?>
            <?php if ( is_user_logged_in() ) { ?>
                <?php wp_nav_menu( array('theme_location' => 'footor_logged_in', 'menu_class' => 'clear logged-out-nav-list footer-nav-list', 'container' => '') ); ?>        <?php } ?>
        </nav>
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
