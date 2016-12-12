<?php
/**
 * @package kamoha
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php kamoha_show_movie_icon(); ?>
    <a href="<?php the_permalink(); ?>" rel="bookmark" class="image-wrapper">
        <?php kamoha_show_homepage_thumbnail( 'small' ); ?>
    </a>
    <section class="entry-body clear">
        <header class="entry-header">
            <h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
            <?php if ( 'post' == get_post_type() ) : ?>
                <div class="entry-meta">
                    <a href="<?php the_permalink(); ?>" rel="bookmark">
                        <?php kamoha_posted_on(); ?>
                    </a>
                </div><!-- .entry-meta -->
            <?php endif; ?>
        </header><!-- .entry-header -->

        <div class="entry-summary">
            <a href="<?php the_permalink(); ?>" rel="bookmark">
                <?php the_excerpt(); ?>
            </a>
        </div><!-- .entry-summary -->

        <footer class="entry-meta">
            <?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
                <?php
                /* translators: used between list items, there is a space after the comma */
                $categories_list = get_the_category_list( __( ', ', 'kamoha_2015' ) );
                if ( $categories_list && kamoha_categorized_blog() ) :
                    ?>
                    <span class="cat-links">
                        <?php printf( __( 'Posted in %1$s', 'kamoha_2015' ), $categories_list ); ?>
                    </span>
                <?php endif; // End if categories ?>

                <?php
                /* translators: used between list items, there is a space after the comma */
                $tags_list = get_the_tag_list( '', __( ', ', 'kamoha_2015' ) );
                if ( $tags_list ) :
                    ?>
                    <span class="tags-links">
                        <?php printf( __( 'Tagged %1$s', 'kamoha_2015' ), $tags_list ); ?>
                    </span>
                <?php endif; // End if $tags_list ?>
            <?php endif; // End if 'post' == get_post_type() ?>

            <?php
            // if there are comments, show number
            $comm_num = number_format_i18n( get_comments_number() );
            if ( $comm_num > 0 ) {
                ?>
                <span class="icon-bubble"><?php echo $comm_num; ?></span>
            <?php } ?>

            <?php edit_post_link( __( 'Edit', 'kamoha_2015' ), '<span class="edit-link">', '</span>' ); ?>
        </footer><!-- .entry-meta -->
    </section>
</article><!-- #post-## -->
