<?php
/**
 * @package kamoha
 * masonry tutorial: http://www.wpbeginner.com/wp-themes/how-to-use-masonry-to-add-pinterest-style-post-grid-in-wordpress/
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <a href="<?php the_permalink(); ?>" rel="bookmark"> 

        <?php kamoha_show_homepage_thumbnail( 'medium' ); ?>

        <header class="entry-header">
            <h2 class="entry-title"><?php the_title(); ?></h2>

            <?php if ( 'post' == get_post_type() ) : ?>
                <div class="entry-meta">
                    <?php
                    // if there are comments, show number
                    $comm_num = number_format_i18n( get_comments_number() );
                    if ( $comm_num > 0 ) {
                        ?>
                        <span class="icon-bubble"><?php echo $comm_num; ?></span>
                    <?php } ?>

                    <?php kamoha_posted_on(); ?>
                    <?php kamoha_show_movie_icon(); ?>
                </div><!-- .entry-meta -->
            <?php endif; ?>
        </header><!-- .entry-header -->

        <?php if ( is_search() || is_home() || is_category() || is_tag() ) : // Only display Excerpts for Home and Search  ?>
            <div class="entry-summary">
                <?php the_excerpt(); ?>
            </div><!-- .entry-summary -->
        <?php else : ?>
            <div class="entry-content">
                <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'kamoha' ) ); ?>
                <?php
                wp_link_pages( array(
                    'before' => '<div class="page-links">' . __( 'Pages:', 'kamoha' ),
                    'after' => '</div>',
                ) );
                ?>


            </div><!-- .entry-content -->
        <?php endif; ?>
    </a>
</article><!-- #post-## -->
