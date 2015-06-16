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
            /* translators: used between list items, there is a space after the comma */
//            $category_list = get_the_category_list(__(', ', 'kamoha'));
            // get_the_category _list returns a comma separated HTML list of categories. we want to show only one
//            $category_list = explode('</a>,', $category_list);
//            if (count($category_list) > 0) {
//                echo $category_list[0] . '</a>';
//            }
            ?>
        </h1>

        <div class="entry-meta">
            <?php kamoha_posted_on(); ?>
        </div><!-- .entry-meta -->
    </header><!-- .entry-header -->

    <div class="entry-summary">
        <?php the_excerpt(); ?>
    </div><!-- .entry-summary -->

    <div class="entry-content">
        <?php the_content(); ?>
    </div><!-- .entry-content -->

    <?php // in the footer, show tags, and share buttons ?>
    <footer class="entry-meta clear">

        <div class="entry-tags">
            <?php
            /* translators: used between list items, there is a space after the comma */
            $tag_list = get_the_tag_list( '', __( ', ', 'kamoha' ) );

            if ( '' != $tag_list ) {
                printf(
                        __( 'This entry was tagged %1$s.', 'kamoha' ), $tag_list
                );
            }
            ?>

            <?php edit_post_link( __( 'Edit', 'kamoha' ), '<span class="edit-link">', '</span>' ); ?>
        </div>

        <?php echo do_shortcode( '[ssba]' ); ?> 

    </footer><!-- .entry-meta -->

</article><!-- #post-## -->

<?php // after post, show related articles ?>
<?php
if ( function_exists( 'wp_related_posts' ) ) {
    wp_related_posts();
}

