<?php
/**
 * Used to diplay almost all homepage content - the sticky post, the first 6 posts, the category posts and the blog posts (only not the tabs)
 * Since there are miled differences between the display of each kind of post, a variable is used to differentiate what is being displayed
 * @package kamoha
 */
?>

<?php
global $homepage_part, // differentiate which part of homepage we're in
$blog_post_index, $issue_post_index, $sticky_exists, $latest_post_index;
?>


<?php $article_class = 'clear'; ?>
<article  <?php post_class( $article_class ); ?>><!-- Lea 2014/12 - the id invalidates the html when the same posts appears twice. No need for id on homepage. id="post-<?php //the_ID(); ?>" -->
    <?php
    if ( $homepage_part == HomepagePart::Sticky || $homepage_part == HomepagePart::Newest ) { //show the film icon only in newest posts
        kamoha_show_movie_icon();
    }
    ?>
    <?php /* allways show thumbnail, except in blogs posts, 
     * where only the first post's thumbnail is shown, 
     * because all posts of same blog, have the same image */ ?>
    <?php
    if ( ($homepage_part != HomepagePart::Blogs && $homepage_part != HomepagePart::Issues) ||
            ($homepage_part == HomepagePart::Blogs && $blog_post_index == 1 ) ||
            ($homepage_part == HomepagePart::Issues && $issue_post_index == 1 ) ) {
        $thumb_size = ($homepage_part == HomepagePart::Tabs) ? 'teeny' :
                ((is_sticky() || ( ! $sticky_exists && $latest_post_index == 1)) ? 'medium' : 'small');
        ?>
        <a href="<?php the_permalink(); ?>" rel="bookmark" class="image-wrapper">
            <?php kamoha_show_homepage_thumbnail( $thumb_size ); ?>
        </a>
    <?php }
    ?>
    <section class="entry-body clear">

        <header class="entry-header">
            <h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo esc_html( get_the_title() ); ?></a></h2>
        </header><!-- .entry-header -->


        <div class="entry-summary">
            <a href="<?php the_permalink(); ?>" rel="bookmark">
                <?php echo kamoha_the_short_excerpt_by_len( $homepage_part ); ?>
            </a>
        </div><!-- .entry-summary -->

        <footer class="entry-meta">
            <?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search  ?>

                <?php if ( $homepage_part == HomepagePart::Sticky || $homepage_part == HomepagePart::Newest ) { ?>
                    <?php
                    /* translators: used between list items, there is a space after the comma */
                    /* $categories_list = get_the_category_list ( __ ( ', ', 'kamoha' ) );
                      printf ( __ ( '%1$s', 'kamoha' ), $categories_list ); */
                    kamoha_show_post_one_cat();
                    ?>

                <?php } // End if not categories and not blogs    ?>

                <?php
                // if there are comments, show number
                $comm_num = number_format_i18n( get_comments_number() );
                if ( $comm_num > 0 && $homepage_part != HomepagePart::Tabs ) {
                    ?>
                    <span class="icon-bubble"><?php echo $comm_num; ?></span>
                <?php } ?>
                <a href="<?php the_permalink(); ?>" rel="bookmark">
                    <?php kamoha_posted_on(); ?>
                </a>

            <?php endif; // End if 'post' == get_post_type()     ?>

            <?php //edit_post_link( __( 'Edit', 'kamoha' ), '<span class="edit-link">', '</span>' );          ?>
        </footer><!-- .entry-meta -->
    </section>
</article><!-- #post-## -->
