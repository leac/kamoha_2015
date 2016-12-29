<?php
/*
  Template Name: עמוד ראשי
 */
get_header();

$do_not_duplicate = array();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main clear" role="main">


        <?php global $kamoha_homepage_part; ?>


        <?php /*         * *****************First block - one sticky post and 6 latest posts.*************************** */ ?>
        <?php
        // the first query in homepage is controlled by pre_get_posts and is defined to return 1 sticky post or 6 latest posts if no sticky exists
        if ( have_posts() ) :
            ?>
            <div class="block block-newest-posts clear">

                <?php
                /* In homepage, get sticky post, or - if no sticky post exists - get the 7 newest posts */
                $home_page_query = kamoha_homepage_main_query();
                global $kamoha_sticky_exists, $kamoha_latest_post_index;
                $kamoha_latest_post_index = 1;
                while ( $home_page_query->have_posts() ) : $home_page_query->the_post();
                    /* we don't want posts that appeared in the newest posts, to appear again under their categories.
                     * but posts that are hidden - we do want to show. So add to array only first 5 */
                    if ( $kamoha_latest_post_index <= NEWEST_POSTS_CNT - 2 ) {
                        $do_not_duplicate[] = get_the_ID();
                    }
                    ?>

                    <?php
                    $kamoha_homepage_part = $kamoha_sticky_exists || $kamoha_latest_post_index == 1 ? KamohaHomepagePart::Sticky : KamohaHomepagePart::Newest; /* indicate what part of homepage we're on */
                    get_template_part( 'content', 'home' );
                    $kamoha_latest_post_index ++;
                    ?>

                <?php endwhile; ?>

                <?php
                global $kamoha_sticky_exists;
                if ( $kamoha_sticky_exists ) { // only if there is a sticky post, we need this query, to get 6 latest posts
                    // run a query to get the 6 latest posts. Am using array as args, because that's the only way that post__not_in works
                    $newest_posts = new WP_Query( array('post__not_in' => get_option( 'sticky_posts' ), 'posts_per_page' => 6) );
                    if ( $newest_posts->have_posts() ) {
                        $kamoha_homepage_part = KamohaHomepagePart::Newest; /* indicate what part of homepage we're on */

                        // display newest posts
                        while ( $newest_posts->have_posts() ) : $newest_posts->the_post();
                            /* we don't want posts that appeared in the newest posts, to appear again under their categories.
                             * but posts that are hidden - we do want to show. So add to array only first 5 */
                            if ( $kamoha_latest_post_index <= NEWEST_POSTS_CNT - 2 ) {
                                $do_not_duplicate[] = get_the_ID();
                            }
                            get_template_part( 'content', 'home' );

                        endwhile; // end of the loop. 

                        wp_reset_postdata();
                    }
                }
                ?>


                <?php // add a button for loading more posts. JS will hide the 2 last posts, and this button will toggle show/hide them     ?>
                <div id="morePosts" class="toOpen showMore"> <?php _e( 'Load more', 'kamoha_2015' ) ?> </div>
            </div>





            <?php /*             * ***************** Block - Blog categories whose extra field is set to show in homepage*************************** */ ?>
            <div class="block block-blogs clear">
                <?php
                //get an array of blog-category objects that will be displayed om homepage; 
                $blogs = get_homepage_categories( BLOGS_CAT );
                $kamoha_homepage_part = KamohaHomepagePart::Blogs; /* indicate what part of homepage we're on */
                foreach ( $blogs as $blog ) {

                    // loop through the blog-categories, and get the 3 latest posts from each one
                    $blog_posts = new WP_Query( array('category__in' => $blog->term_id, 'posts_per_page' => 3, 'post__not_in' => $do_not_duplicate) );

                    if ( $blog_posts->have_posts() ) {
                        global $kamoha_blog_post_index;
                        $kamoha_blog_post_index = 1;
                        // give the block a title 
                        ?>
                        <section class="blog_posts clear">
                            <h3 class="category_title">
                                <span class="blogs_sub_title"><?php _e( 'blog', 'kamoha_2015' ) ?></span>
                                <a href="<?php echo get_category_link( $blog->term_id ) ?>"><?php echo $blog->name ?></a>
                            </h3>
                            <?php
                            // display blog posts
                            while ( $blog_posts->have_posts() ) : $blog_posts->the_post();

                                get_template_part( 'content', 'home' );

                                $kamoha_blog_post_index ++;

                            endwhile; // end of the loop. 

                            wp_reset_postdata();
                            ?>
                        </section>
                        <?php
                    }
                }
                ?>
            </div>





            <?php /*             * ***************** Block - Issues categories whose extra field is set to show in homepage*************************** */ ?>
            <div class="block block-issues clear">
                <?php
                //get an array of blog-category objects that will be displayed om homepage; 
                $issues = get_homepage_categories( ISSUES_CAT );
                $kamoha_homepage_part = KamohaHomepagePart::Issues; /* indicate what part of homepage we're on */
                foreach ( $issues as $issue ) {

                    // loop through the blog-categories, and get the 3 latest posts from each one
                    $issues_posts = new WP_Query( array('category__in' => $issue->term_id, 'posts_per_page' => 3, 'post__not_in' => $do_not_duplicate) );

                    if ( $issues_posts->have_posts() ) {
                        global $issue_post_index;
                        $issue_post_index = 1;
                        // give the block a title 
                        ?>
                        <section class="blog_posts clear">
                            <h3 class="category_title">
                                <span class="blogs_sub_title"><?php _e( 'issue', 'kamoha_2015' ) ?></span>
                                <a href="<?php echo get_category_link( $issue->term_id ) ?>"><?php echo $issue->name ?></a>
                            </h3>
                            <?php
                            // display blog posts
                            while ( $issues_posts->have_posts() ) : $issues_posts->the_post();

                                get_template_part( 'content', 'home' );

                                $issue_post_index ++;

                            endwhile; // end of the loop. 

                            wp_reset_postdata();
                            ?>
                        </section>
                        <?php
                    }
                }
                ?>
            </div>





            <?php /*             * ***************** Block - All top level categories whose extra field is set to show in homepage*************************** */ ?>
            <div class="block block-categories clear">
                <?php
                //get an array of category objects that will be displayed om homepage; 
                $cats = get_homepage_categories( 0 );
                $kamoha_homepage_part = KamohaHomepagePart::Categories; /* indicate what part of homepage we're on */
                foreach ( $cats as $category ) {
                    ?>

                    <?php
                    // loop through the categories, and get the 3 latest posts from each one
                    $cat_posts = new WP_Query( array('category__in' => $category->term_id, 'posts_per_page' => 3, 'post__not_in' => $do_not_duplicate) );

                    if ( $cat_posts->have_posts() ) {
                        ?>
                        <section class="category_posts clear">
                            <?php // give the block a title  ?>
                            <h3 class="category_title">
                                <a href="<?php echo get_category_link( $category->term_id ) ?>"><?php echo $category->name ?></a>
                            </h3>

                            <?php
                            // display category posts
                            while ( $cat_posts->have_posts() ) : $cat_posts->the_post();
                                $do_not_duplicate[] = get_the_ID(); /* some posts appear in more than one category. Make sure they only appear once in the homepage */
                                get_template_part( 'content', 'home' );

                            endwhile; // end of the loop. 

                            wp_reset_postdata();
                            ?>
                        </section>
                        <?php
                    }
                }
                ?>
            </div>





            <?php //************************Most commented, most popular ever, and most popular monthly posts **************************/                ?>
            <aside class="block block-tabs">
                <?php // tab implementation taken from css trick: http://css-tricks.com/functional-css-tabs-revisited/      ?>
                <?php
                if ( function_exists( 'wpp_get_mostpopular' ) ) {
                    /* show most viewed posts in all time. */
                    ?>
                    <div class="tab">
                        <input type="radio" id="tab-1" name="tab-group-1" checked>
                        <label for="tab-1"><?php _e( 'Popular Posts Monthly', 'kamoha_2015' ); ?></label>
                        <div class="tab_content tab-bounceInRight">
                            <?php
                            /* prepare args for wpp_get_mostpopular:
                             *  In order to show thumbnail - need to set thumb width and height.
                             * In order to show excerpt - have to set excerpt_length.
                             * In order for the posts to get our style, we set custom HTML. class names have to use one apostrophe, and be escaped  */
                            $custom_html = '<li class=\'clear\'>'
                                    . '<article class=\'hentry\'>'
                                    . '	<span class=\'image-wrapper\'>{thumb} </span>' /* span needed for class image-wrapper. not goog, but necessary */
                                    . '	<section class=\'entry-body\'>'
                                    . '		<header class=\'entry-header\'><h1 class=\'entry-title\'><a href=\'{url}\'>{text_title}</a></h1></header>'
                                    . '		<div class=\'entry-summary\'>{summary} </div>'
                                    . '		<footer class=\'entry-meta\'>{stats}</footer>'
                                    . '	</section>'
                                    . '</article>'
                                    . '</li>';
                            $wpp_args = 'limit=4&range="monthly"&thumbnail_width=' . TEENY_WIDTH . '&thumbnail_height=' . TEENY_HEIGHT . '&excerpt_length=' . TAB_EXCERPT_3_ROWS . '&stats_date=true&stats_date_format="d/m/Y"&post_html="' . $custom_html . '"';

                            wpp_get_mostpopular( $wpp_args );
                            ?>
                        </div>
                    </div>
                    <div class="tab">
                        <?php // show most viewed posts in past month          ?>
                        <input type="radio" id="tab-2" name="tab-group-1">
                        <label for="tab-2"><?php _e( 'Popular Posts Ever', 'kamoha_2015' ); ?></label>
                        <div class="tab_content tab-bounceInRight">
                            <?php
                            // use same args as most popular, just change "all" to "monthly"
                            $wpp_args = str_replace( 'monthly', 'all', $wpp_args );
                            wpp_get_mostpopular( $wpp_args );
                            ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="tab">
                    <input type="radio" id="tab-3" name="tab-group-1">
                    <label for="tab-3"><?php _e( 'Commented Posts', 'kamoha_2015' ) ?></label>
                    <?php
                    // show most commented posts
                    $comment_args = 'orderby=comment_count&posts_per_page=4&no_found_rows=true&ignore_sticky_posts=1';
                    $commented_posts = new WP_Query( $comment_args );
                    if ( $commented_posts->have_posts() ) :
                        $kamoha_homepage_part = KamohaHomepagePart::Tabs; /* indicate what part of homepage we're on */
                        ?>
                        <div class="tab_content tab-bounceInRight">
                            <ul class="cmnt-list">
                                <?php while ( $commented_posts->have_posts() ) : $commented_posts->the_post(); ?>
                                    <li class="clear">
                                        <?php get_template_part( 'content', 'home' ); ?>  
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div> <!-- tab_content -->
                        <?php
                    endif;
                    ?>
                </div> <!-- tab -->			

            </aside>

        <?php else : ?>

            <?php get_template_part( 'content', 'none' ); ?>

        <?php endif; ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
