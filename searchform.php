<?php
$format = current_theme_supports( 'html5', 'search-form' ) ? 'html5' : 'xhtml';
if ( 'html5' == $format ) {
    ?>
    <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ) ?>">
        <label>
            <span class="screen-reader-text"><?php _x( 'Search for:', 'label', 'kamoha' ) ?> </span>
            <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'kamoha' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php esc_attr_x( 'Search for:', 'label', 'kamoha' ) ?>" />
        </label>
        <input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'kamoha' ) ?>" />
        <!-- &#58883; -->
    </form>
<?php } else { ?>
    <form role="search" method="get" id="searchform" class="searchform" action="<?php esc_url( home_url( '/' ) ) ?>">
        <div>
            <label class="screen-reader-text" for="s"><?php _x( 'Search for:', 'label', 'kamoha' ) ?></label>
            <input type="text" value="<?php get_search_query() ?>" name="s" id="s" />
            <input type="submit" id="searchsubmit" value="<?php esc_attr_x( 'Search', 'submit button' ) ?>" />
        </div>
    </form>
<?php } ?>