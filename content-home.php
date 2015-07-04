<?php
/**
 * Used to display almost all homepage content - 
 * the sticky post, the first 6 posts, the category posts and the blog posts (the only thing it doesn't display is the tabs).
 * Since there are differences between the display of each kind of post, a variable is used to differentiate what is being displayed
 * @package kamoha
 */
?>

<?php
global $kamoha_homepage_part, // differentiate which part of homepage we're in
$kamoha_blog_post_index, $issue_post_index, $kamoha_sticky_exists, $kamoha_latest_post_index;
?>


<?php $article_class = 'clear'; ?>
<article  <?php post_class( $article_class ); ?>><!-- Lea 2014/12 - the id invalidates the html when the same posts appears twice. No need for id on homepage. id="post-<?php //the_ID();    ?>" -->
    <?php
    if ( $kamoha_homepage_part == KamohaHomepagePart::Sticky || $kamoha_homepage_part == KamohaHomepagePart::Newest ) { //show the film icon only in newest posts
        kamoha_show_movie_icon();
    }
    ?>
    <?php /* allways show thumbnail, except in blogs posts, 
     * where only the first post's thumbnail is shown, 
     * because all posts of same blog, have the same image */ ?>
    <?php
    if ( ($kamoha_homepage_part != KamohaHomepagePart::Blogs && $kamoha_homepage_part != KamohaHomepagePart::Issues) ||
            ($kamoha_homepage_part == KamohaHomepagePart::Blogs && $kamoha_blog_post_index == 1 ) ||
            ($kamoha_homepage_part == KamohaHomepagePart::Issues && $issue_post_index == 1 ) ) {
        $thumb_size = ($kamoha_homepage_part == KamohaHomepagePart::Tabs) ? 'kamoha_teeny' :
                ((is_sticky() || (!$kamoha_sticky_exists && $kamoha_latest_post_index == 1)) ? 'kamoha_medium' : 'kamoha_small');
        ?>
        <a href="<?php the_permalink(); ?>" rel="bookmark" class="image-wrapper">
            <?php kamoha_show_homepage_thumbnail( $thumb_size ); ?>
        </a>
    <?php }
    ?>
    <section class="entry-body clear">

        <header class="entry-header">
            <h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title() ; ?></a></h2>
        </header><!-- .entry-header -->


        <div class="entry-summary">
            <a href="<?php the_permalink(); ?>" rel="bookmark">
                <?php echo kamoha_the_short_excerpt_by_len( $kamoha_homepage_part ); ?>
            </a>
        </div><!-- .entry-summary -->

        <footer class="entry-meta">
            <?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search  ?>

                <?php if ( $kamoha_homepage_part == KamohaHomepagePart::Sticky || $kamoha_homepage_part == KamohaHomepagePart::Newest ) { ?>
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
                if ( $comm_num > 0 && $kamoha_homepage_part != KamohaHomepagePart::Tabs ) {
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
