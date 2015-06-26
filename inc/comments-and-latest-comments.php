<?php
/* -----------------------------------------
 * Latest Comments functions
 * ----------------------------------------- */

/**
 * Show latest comments. Adapted from the default-widget comments function
 * */
function kamoha_comments_widget(){
    global $comments, $comment;
    $args = array();

    $cache = wp_cache_get( 'widget_recent_comments', 'widget' );

    if ( !is_array( $cache ) )
        $cache = array();

    if ( !isset( $args['widget_id'] ) )
        $args['widget_id'] = 'recent-comments-2';

    if ( isset( $cache[$args['widget_id']] ) ) {
        echo $cache[$args['widget_id']];
        return;
    }

    $output = '';

    $title = __( 'Recent Comments', 'kamoha' );
    $before_title = '<h3 class="aside_title">';
    $after_title = '</h3>';
    $number = 5;

    $comments = get_comments( apply_filters( 'widget_comments_args', array('number' => $number, 'status' => 'approve', 'post_status' => 'publish') ) );
    if ( $title )
        $output .= $before_title . $title . $after_title;

    $output .= '<section class="aside_content">';
    $output .= '<ul id="recentcomments">';
    if ( $comments ) {
        // Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
        $post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
        _prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

        foreach ( (array) $comments as $comment ) {
            $comment_author = $comment->comment_author == '' ? __( 'anonymous user', 'kamoha' ) : strip_tags( $comment->comment_author );
            $output .= '<li class="recentcomments">' .
                    '<a href="' . get_permalink( $comment->ID ) . '#comment-' . $comment->comment_ID . '">' .
                    '<span class="comment_name">' . $comment_author . ' </span>' .
                    ' ' . __( 'about', 'kamoha' ) . ' ' .
                    '<span class="comment_post_name">' . get_the_title( $comment->comment_post_ID ) . '</span>: ' .
                    '<span class="comment_content">' . mb_substr( strip_tags( $comment->comment_content ), 0, 60 ) . '...</span>' .
                    '<div class="comment_date">' . get_hebrew_date( get_comment_date( 'm' ), get_comment_date( 'd' ), get_comment_date( 'Y' ) ) . ', ' . get_comment_date() . '</div>' .
                    '</a>' .
                    '</li>';
        }
    }
    $output .= '</ul>';
    $output .= '<div id="moreComments" class="toOpen showMore">' . __( 'Load more', 'kamoha' ) . ' </div>';
    $output .= '</section>';

    echo $output;
    $cache[$args['widget_id']] = $output;
    wp_cache_set( 'widget_recent_comments', $cache, 'widget' );
}

/* * *********************************************** */
/* * ***********  Comments functions *************** */
/* * *********************************************** */

/**
 * Output a comment in the HTML5 format.
 *
 * @access protected
 * @since 3.6.0
 *
 * @see wp_list_comments()
 *
 * @param object $comment Comment to display.
 * @param int    $depth   Depth of comment.
 * @param array  $args    An array of arguments.
 */
function kamoha_comment( $comment, $args, $depth ){
    ?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? 'clear' : 'clear parent'  ); ?>>
        <article id="div-comment-<?php comment_ID(); ?>" class="comment-body clear">
            <footer class="comment-meta">
                <div class="comment-author vcard">
    <?php printf( __( '%s <span class="says">says:</span>', 'kamoha' ), sprintf( '<b class="fn">%s</b>', get_comment_author_link() ) ); ?>
                </div><!-- .comment-author -->

                <div class="comment-metadata">
                    <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID, $args ) ); ?>">
                        <time datetime="<?php comment_time( 'c' ); ?>">
    <?php printf( _x( '%1$s, %2$s at %3$s', '1: hewbrewdate, 2: date, 3:time', 'kamoha' ), get_hebrew_date( get_comment_date( 'm' ), get_comment_date( 'd' ), get_comment_date( 'Y' ) ), get_comment_date(), get_comment_time() ); ?>
                        </time>
                    </a>


    <?php edit_comment_link( __( 'Edit', 'kamoha' ), '<span class="edit-link">', '</span>' ); ?>
                </div><!-- .comment-metadata -->

    <?php if ( '0' == $comment->comment_approved ) : ?>
                    <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'kamoha' ); ?></p>
                <?php endif; ?>
            </footer><!-- .comment-meta -->
            <!-- commenter img -->
    <?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
            <div class="comment-content">

    <?php comment_text(); ?>

                <div class="reply">
    <?php comment_reply_link( array_merge( $args, array('add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth']) ) ); ?>
                </div><!-- .reply -->

            </div><!-- .comment-content -->


        </article><!-- .comment-body -->
    <?php
}

/**
 * Remove the url fiels from comments form.
 * This function also altered the label and input fields of name and email,
 * in order to apply a cute style, but it counted on those fields being required,
 * which isn't the case in this site.
 * Am leaving the code commented out, in order to get back to it some day
 * @param type $fields
 * @return $fields
 */
function kamoha_alter_comment_form_fields( $fields ){
    // $commenter = wp_get_current_commenter();
    // $req = get_option( 'require_name_email' );
    // $aria_req = ( $req ? " aria-required='true'" : '' );
    // $normal_req = ( $req ? " required=''" : '' );
    // $html5 = 'html5' === current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
    // $fields[ 'author' ] = '<p class="comment-form-author">' .
    // '<input id="author" name="author" type="text" value="' . esc_attr( $commenter[ 'comment_author' ] ) . '" size="30"' . $aria_req . $normal_req . ' />' .
    // '<label for="author" alt="' . __( 'Name' ) . ( $req ? ' *' : '' ) . '" placeholder="' . __( 'Name' ) . ( $req ? ' *' : '' ) . '"> </label>'
    // . '</p> ';
    // $fields[ 'email' ] = '<p class="comment-form-email">' .
    // '<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr( $commenter[ 'comment_author_email' ] ) . '" size="30"' . $aria_req . $normal_req . ' />' .
    // '<label for="email" alt="' . __( 'Email' ) . ( $req ? ' *' : '' ) . '" placeholder="' . __( 'Email' ) . ( $req ? ' *' : '' ) . '">  </label> ' .
    // '</p>';

    $fields['url'] = '';  //removes website field
    return $fields;
}

add_filter( 'comment_form_default_fields', 'kamoha_alter_comment_form_fields' );

/**
 * Get the Hebrew date, based on the Gregorian date input
 * @param int $month Gregorian month
 * @param int $day Gregorian day
 * @param int $year Gregorian year
 * @return string
 */
function get_hebrew_date( $month, $day, $year ){
    $jew_date = jdtojewish( gregoriantojd( $month, $day, $year ), true, CAL_JEWISH_ADD_GERESHAYIM );
    $jew_date = iconv( 'WINDOWS-1255', 'UTF-8', $jew_date );

    // (Lea 06/2014) add מר before חשון and add ב before month, as Hebrew standards dictates
    $jew_date = explode( ' ', $jew_date );
    if ( $jew_date[1] == 'חשון' ) {
        $jew_date[1] = 'מר' . $jew_date[1];
    }

    // in אדר ב' move the apostrophe from before the אדר to after the ב
    if ( mb_strpos( $jew_date[1], '\'' ) === 0 ) {
        $jew_date[1] = mb_substr( $jew_date[1], 1 );
        $jew_date[2] .= '\'';
    }

    $jew_date[1] = 'ב' . $jew_date[1];
    $jew_date = implode( $jew_date, ' ' );

    return $jew_date;
}
