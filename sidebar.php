<?php
/**
 * The Sidebar.
 * It contains:
 * News (CPT), 
 * Ask Rabbi link, 
 * Latest comments, Calendar, 
 * Countdown (dynamic sidebar widget), 
 * Events (events and meetings category)
 * Tags
 * In lower resolutions, the sidebar appears on the bottom, under the primary area, and only shows latest comments, and events.
 * In single show only the events
 * @package kamoha
 */
?>

<?php /* * ******************************************************** 
 * ***************** Right sidebar *************************** 
 * *************************************************************** */ ?>
<div id="secondary" class="widget-area" role="complementary">

    <?php /*     * ***************** News & Countdown watch *************************** */ ?>
    <?php if ( ! is_single() && ! is_404() ) { ?>
        <aside class="news_flash">
            <h3 class="aside_title"><?php _e( 'newsflash', 'kamoha_2015' ) ?></h3>
            <section class="aside_content">
                <?php echo kamoha_get_newsflash(); ?>
            </section>

            <?php // countdown - call dynamic sidebar, because needs to be updated by site manager ?>
            <?php
            if ( is_active_sidebar( 'sidebar-countdown' ) ) {
                dynamic_sidebar( 'sidebar-countdown' );
            }
            ?>
        </aside>
    <?php } ?>


    <?php /*     * ***************** Ask Rabbi Link *************************** */ ?>
    <?php if ( ! is_single() && ! is_404() ) { ?>
        <aside class="ask_rabbi_link kamoha_btn">
            <a href="<?php echo get_page_link( ASK_RABBI_PAGE ) ?>"><?php _e( 'ask rabbi', 'kamoha_2015' ) ?></a>
        </aside>
    <?php } ?>


    <?php /*     * ***************** Facebook Link *************************** */ ?>
    <?php if ( ! is_single() && ! is_404() ) { ?>
        <aside class="facebook_link kamoha_btn">
            <a href="<?php echo FACEBOOK_LINK ?>"><?php _e( 'our facebook', 'kamoha_2015' ) ?></a>
        </aside>
    <?php } ?>


    <?php /*     * ***************** Calendar, Next meetings  *************************** */ ?>

    <aside class="events_box" id="events_box"> <?php // use id as anchor for next and previous month links    ?>
        <h3 class="aside_title"><?php _e( 'Events and meetings', 'kamoha_2015' ) ?></h3>


        <?php // calendar ?>
        <section class="aside_content calendar_block clear">
            <?php kamoha_get_event_calendar( MEETINGS_CAT, '' ); ?>
        </section>


        <?php // events ?>
        <section class="aside_content events_block">
            <?php kamoha_event_list(); ?>
        </section><!-- events block -->
    </aside>


    <?php /*     * ***************** Latest comments  *************************** */ ?>
    <?php if ( ! is_single() && ! is_404() ) { ?>
        <aside class="widget latest_comments_widget">
            <?php
            // the title and section wrpa are inside the function				
            kamoha_comments_widget();
            ?>
        </aside>
    <?php } ?>


    <?php /*     * ***************** Tags  *************************** */ ?>
    <?php if ( ! is_single() && ! is_404() ) { ?>
        <aside class="widget tags_widget">
            <h3 class="aside_title"><?php _e( 'chosen tags', 'kamoha_2015' ) ?></h3>
            <section class="aside_content">
                <?php
                $args = array(
                    'smallest' => 9,
                    'largest' => 25,
                    'unit' => 'pt',
                    'number' => 25,
                    'format' => 'flat',
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'exclude' => '',
                    'include' => '',
                    'link' => 'view',
                    'taxonomy' => 'post_tag',
                    'echo' => true);
                wp_tag_cloud( $args );
                ?> 
            </section>
        </aside>
    <?php } ?>


</div><!-- #secondary -->
