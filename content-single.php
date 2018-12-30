<?php
/**
 * @package kamoha
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <h1 class="entry-title">
            <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a> |
            <?php
            kamoha_show_post_one_cat();
            ?>
        </h1>

        <div class="entry-meta">
            <?php kamoha_posted_on(); ?>
        </div><!-- .entry-meta -->
    </header><!-- .entry-header -->

    <?php
    /* If there is an excerpt, show it. Don't show auto-generated excerpts. Check if the user has written an excerpt with has_excerpt()  */
    if ( has_excerpt() ) {
        ?>
        <div class="entry-summary">
            <?php the_excerpt(); ?>
        </div><!-- .entry-summary -->
    <?php } ?>

    <div class="entry-content">
        <?php the_content(); ?>
        <?php
        wp_link_pages( array(
            'before' => '<div class="page-links">' . __( 'Pages:', 'kamoha_2015' ), 'after' => '</div>',
        ) );
        ?>
		
		<?php kamoha_the_post_navigation(); ?>
    </div><!-- .entry-content -->

    <?php // in the footer, show tags, and share buttons   ?>
    <footer class="entry-meta clear">

        <div class="entry-tags">
            <?php
            /* translators: used between list items, there is a space after the comma */
            $tag_list = get_the_tag_list( '', __( ', ', 'kamoha_2015' ) );

            if ( '' != $tag_list ) {
                printf(
                        __( 'This entry was tagged %1$s.', 'kamoha_2015' ), $tag_list
                );
            }
            ?>

            <?php edit_post_link( __( 'Edit', 'kamoha_2015' ), '<span class="edit-link">', '</span>' ); ?>
        </div>

    </footer><!-- .entry-meta -->

</article><!-- #post-## -->

<?php // after post, show related articles ?>
<?php
if ( function_exists( 'wp_related_posts' ) ) {
    wp_related_posts();
}

    