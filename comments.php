<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package kamoha
 */
/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php // You can start editing here -- including this comment!  ?>
    <?php if ( have_comments() ) { ?>
        <h2 class="comments-title">
            <?php
            printf( _nx( 'One thought', '%1$s thoughts', get_comments_number(), 'comments title', 'kamoha_2015' ), number_format_i18n( get_comments_number() ) );
            ?>
        </h2>
    <?php } else { ?>
        <h2 class="comments-title">
            <?php
            _e( 'No thoughts', 'kamoha_2015' );
            ?>
        </h2>
    <?php } ?>

    <?php /* Add facebook commnets box - begin */ ?>
    <?php
    if (function_exists('fbcommentshortcode')) { // run the shortcode only if the function associated with it exists
        echo do_shortcode('[fbcomments]');
    }
    ?>
    <?php /* Add facebook commnets box - end */ ?>

    <?php
    comment_form( array(
        'comment_notes_after' => '',
        'comment_field' => '<p class="comment-form-comment">'
        . '<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" required=""></textarea>'
        . '<label for="comment" alt="' . _x( 'Comment', 'noun', 'kamoha_2015' ) . '" placeholder="' . _x( 'Comment', 'noun', 'kamoha_2015' ) . '"> </label> '
        . '</p>',
    ) );
    ?>

    <?php if ( have_comments() ) : ?>

        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through   ?>
            <nav id="comment-nav-above" class="comment-navigation" role="navigation">
                <h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'kamoha_2015' ); ?></h1>
                <div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'kamoha_2015' ) ); ?></div>
                <div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'kamoha_2015' ) ); ?></div>
            </nav><!-- #comment-nav-above -->
        <?php endif; // check for comment navigation   ?>

        <ol class="comment-list">
            <?php
            wp_list_comments( array(
                'style' => 'ol',
                'short_ping' => true,
                'callback' => 'kamoha_comment',
            ) );
            ?>
        </ol><!-- .comment-list -->

        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through   ?>
            <nav id="comment-nav-below" class="comment-navigation" role="navigation">
                <h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'kamoha_2015' ); ?></h1>
                <div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'kamoha_2015' ) ); ?></div>
                <div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'kamoha_2015' ) ); ?></div>
            </nav><!-- #comment-nav-below -->
        <?php endif; // check for comment navigation   ?>

    <?php endif; // have_comments()  ?>

    <?php
    // If comments are closed and there are comments, let's leave a little note, shall we?
    if ( !comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
        ?>
        <p class="no-comments"><?php _e( 'Comments are closed.', 'kamoha_2015' ); ?></p>
    <?php endif; ?>


</div><!-- #comments -->
